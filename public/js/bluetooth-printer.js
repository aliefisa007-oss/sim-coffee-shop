/**
 * Web Bluetooth ESC/POS Thermal Printer — SIM Coffee (Contact Coffee & Eatery)
 * Versi dengan PERSISTENT PAIRING — setelah pairing pertama kali, print
 * berikutnya otomatis connect tanpa munculin popup pilih device lagi.
 *
 * CARA PAKAI:
 * 1. Include: <script src="{{ asset('js/bluetooth-printer.js') }}"></script>
 *
 * 2. SEKALI SAJA di awal (misal tombol terpisah "Pasangkan Printer" di halaman setting kasir):
 *    await BluetoothPrinter.pairPrinter();
 *    -> ini yang munculin popup pilih device. WAJIB dipicu oleh klik tombol
 *       (browser tidak izinkan requestDevice() dipanggil otomatis tanpa klik user).
 *
 * 3. Setiap kali print struk (misal langsung setelah bayar):
 *    await BluetoothPrinter.printReceipt(dataStruk);
 *    -> otomatis connect ke printer yang sudah dipasangkan sebelumnya, TANPA popup.
 *    -> kalau belum pernah pairing sama sekali, otomatis fallback ke pairPrinter()
 *       (jadi baru muncul popup di percobaan print pertama, itu wajar).
 *
 * CATATAN:
 * - Hanya Chrome/Edge (desktop & Android). TIDAK ada di Safari/iOS.
 * - Harus HTTPS, kecuali localhost.
 * - Printer harus BLE (Bluetooth Low Energy), bukan Bluetooth Classic/SPP.
 * - Izin pairing tersimpan per-browser per-domain oleh Chrome sendiri (bukan
 *   localStorage kita) — kalau ganti browser/device/clear site data, perlu
 *   pairPrinter() ulang sekali.
 */

const BluetoothPrinter = {
  device: null,
  characteristic: null,

  SERVICE_UUID: '000018f0-0000-1000-8000-00805f9b34fb',
  CHARACTERISTIC_UUID: '00002af1-0000-1000-8000-00805f9b34fb',

  ESC: 0x1b,
  GS: 0x1d,

  /**
   * Panggil SEKALI dari klik tombol user (mis. "Pasangkan Printer").
   * Ini satu-satunya tempat popup pemilihan device muncul.
   */
  async pairPrinter() {
    if (!navigator.bluetooth) {
      this._toast('Browser tidak mendukung Web Bluetooth. Pakai Chrome/Edge.', 'error');
      return false;
    }
    try {
      this._toast('🔍 Pilih printer di popup yang muncul...', 'info');

      this.device = await navigator.bluetooth.requestDevice({
        filters: [{ services: [this.SERVICE_UUID] }],
        optionalServices: [this.SERVICE_UUID],
      });

      const ok = await this._connectToDevice(this.device);
      if (ok) {
        this._toast(`✅ Printer dipasangkan: ${this.device.name || 'Bluetooth Printer'}`, 'success');
        localStorage.setItem('bt_printer_name', this.device.name || '');
      }
      return ok;
    } catch (error) {
      console.error('Pairing error:', error);
      this._toast('Gagal memasangkan printer: ' + error.message, 'error');
      return false;
    }
  },

  /**
   * Coba connect otomatis ke device yang SUDAH pernah diizinkan sebelumnya,
   * tanpa popup. Return false kalau belum ada device yang pernah dipasangkan.
   */
  async autoConnect() {
    if (!navigator.bluetooth || !navigator.bluetooth.getDevices) {
      return false; // browser tidak support getDevices() (persistent permission)
    }
    try {
      const devices = await navigator.bluetooth.getDevices();
      if (!devices || devices.length === 0) return false;

      // Ambil device pertama yang pernah diizinkan (biasanya cuma ada 1 printer)
      const target = devices[0];
      return await this._connectToDevice(target);
    } catch (error) {
      console.error('Auto-connect error:', error);
      return false;
    }
  },

  async _connectToDevice(device) {
    try {
      this.device = device;

      device.addEventListener('gattserverdisconnected', () => {
        this._toast('Printer terputus', 'error');
        this.characteristic = null;
      });

      const server = await device.gatt.connect();
      const service = await server.getPrimaryService(this.SERVICE_UUID);
      this.characteristic = await service.getCharacteristic(this.CHARACTERISTIC_UUID);
      return true;
    } catch (error) {
      console.error('Connect error:', error);
      this.characteristic = null;
      return false;
    }
  },

  /**
   * Dipanggil otomatis secara internal sebelum tiap print. Urutan coba:
   * 1. Sudah connect di sesi ini? Pakai langsung.
   * 2. Ada device yang pernah dipasangkan? Auto-connect tanpa popup.
   * 3. Belum pernah pairing sama sekali? Fallback ke pairPrinter() (munculin popup).
   */
  async _ensureConnected() {
    if (this.characteristic) return true;

    const autoOk = await this.autoConnect();
    if (autoOk) return true;

    this._toast('Printer belum dipasangkan, silakan pilih printer', 'info');
    return await this.pairPrinter();
  },

  async scanServices() {
    if (!this.device) {
      const connected = await this._ensureConnected();
      if (!connected) return;
    }
    const server = await this.device.gatt.connect();
    const services = await server.getPrimaryServices();
    for (const service of services) {
      console.log('Service:', service.uuid);
      const chars = await service.getCharacteristics();
      chars.forEach((c) => console.log('  Characteristic:', c.uuid, c.properties));
    }
  },

  async _write(bytes) {
    if (!this.characteristic) {
      const ok = await this._ensureConnected();
      if (!ok) throw new Error('Printer belum terhubung');
    }
    const CHUNK_SIZE = 180;
    for (let i = 0; i < bytes.length; i += CHUNK_SIZE) {
      const chunk = bytes.slice(i, i + CHUNK_SIZE);
      await this.characteristic.writeValue(chunk);
      await new Promise((r) => setTimeout(r, 30));
    }
  },

  _textToBytes(text) {
    return new TextEncoder().encode(text);
  },

  async printText(text, options = {}) {
    const commands = [this.ESC, 0x40]; // init

    if (options.align !== undefined) commands.push(this.ESC, 0x61, options.align);
    if (options.bold) commands.push(this.ESC, 0x45, 0x01);
    if (options.large) commands.push(this.GS, 0x21, 0x11);

    const bytes = new Uint8Array([...commands, ...this._textToBytes(text), 0x0a]);
    await this._write(bytes);

    if (options.bold || options.large) {
      await this._write(new Uint8Array([this.ESC, 0x45, 0x00, this.GS, 0x21, 0x00]));
    }
  },

  async cutPaper() {
    await this._write(new Uint8Array([this.GS, 0x56, 0x00]));
  },

  /**
   * dataStruk = {
   *   namaToko, alamat, kasir, nomorTransaksi, tanggal,
   *   items: [{ nama, qty, harga, subtotal }],
   *   subtotal, diskon, pajak, total,
   *   metodeBayar, uangBayar, kembalian,   // optional, hanya kalau cash
   *   catatan,                             // optional
   *   wifiPassword,                        // optional
   * }
   */
  async printReceipt(dataStruk) {
    const connected = await this._ensureConnected();
    if (!connected) return false;

    try {
      await this.printText(dataStruk.namaToko, { align: 1, bold: true, large: true });
      if (dataStruk.alamat) await this.printText(dataStruk.alamat, { align: 1 });
      await this.printText('--------------------------------', { align: 0 });

      await this.printText(`No: ${dataStruk.nomorTransaksi}`, { align: 0 });
      await this.printText(`${dataStruk.tanggal}`, { align: 0 });
      await this.printText(`Kasir: ${dataStruk.kasir}`, { align: 0 });
      await this.printText('--------------------------------', { align: 0 });

      for (const item of dataStruk.items) {
        await this.printText(item.nama, { align: 0 });
        await this.printText(
          `  ${item.qty} x ${item.harga.toLocaleString('id-ID')} = ${item.subtotal.toLocaleString('id-ID')}`,
          { align: 0 }
        );
      }
      await this.printText('--------------------------------', { align: 0 });

      await this.printText(`Subtotal: Rp ${dataStruk.subtotal.toLocaleString('id-ID')}`, {
        align: 2,
      });

      if (dataStruk.diskon > 0) {
        await this.printText(`Diskon: -Rp ${dataStruk.diskon.toLocaleString('id-ID')}`, {
          align: 2,
        });
      }

      if (dataStruk.pajak > 0) {
        await this.printText(`Pajak: Rp ${dataStruk.pajak.toLocaleString('id-ID')}`, {
          align: 2,
        });
      }

      await this.printText(`TOTAL: Rp ${dataStruk.total.toLocaleString('id-ID')}`, {
        align: 2,
        bold: true,
      });
      await this.printText(`Bayar: ${dataStruk.metodeBayar.toUpperCase()}`, { align: 1 });

      if (dataStruk.uangBayar) {
        await this.printText(`Tunai: Rp ${dataStruk.uangBayar.toLocaleString('id-ID')}`, {
          align: 2,
        });
        await this.printText(`Kembali: Rp ${dataStruk.kembalian.toLocaleString('id-ID')}`, {
          align: 2,
        });
      }

      if (dataStruk.catatan) {
        await this.printText('--------------------------------', { align: 0 });
        await this.printText('Catatan: ' + dataStruk.catatan, { align: 0 });
      }

      await this.printText('--------------------------------', { align: 0 });
      if (dataStruk.wifiPassword) {
        await this.printText('WiFi: ' + dataStruk.wifiPassword, { align: 1 });
      }
      await this.printText('Terimakasih sudah mampir!', { align: 1 });
      await this.printText('#contactpeople', { align: 1 });
      await this.printText('\n\n', { align: 0 });
      await this.cutPaper();

      this._toast('✅ Struk berhasil dicetak', 'success');
      return true;
    } catch (error) {
      console.error('Print error:', error);
      this._toast('Gagal mencetak: ' + error.message, 'error');
      return false;
    }
  },

  _toast(message, type = 'info') {
    if (typeof showToast === 'function') {
      showToast(message, type);
    } else {
      console.log(`[${type}]`, message);
    }
  },
};
