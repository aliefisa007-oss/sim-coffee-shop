/**
 * Web Bluetooth ESC/POS Thermal Printer — SIM Coffee (Contact Coffee & Eatery)
 *
 * CARA PAKAI:
 * 1. Include: <script src="{{ asset('js/bluetooth-printer.js') }}"></script>
 * 2. Panggil dari tombol: await BluetoothPrinter.printReceipt(dataStruk)
 *    (lihat contoh mapping data di struk-bluetooth-integration.blade.php)
 *
 * CATATAN PENTING:
 * - Hanya jalan di Chrome/Edge (desktop & Android). TIDAK jalan di Safari/iOS.
 * - Harus HTTPS (kecuali localhost).
 * - Printer harus support BLE (Bluetooth Low Energy), bukan Bluetooth Classic/SPP.
 * - UUID default di bawah ini cocok buat kebanyakan printer thermal generic
 *   (Goojprt, Zjiang, dll). Kalau printer kamu beda merek dan gagal connect,
 *   jalankan BluetoothPrinter.scanServices() di console buat cari UUID yang benar,
 *   lalu ganti SERVICE_UUID & CHARACTERISTIC_UUID di bawah.
 */

const BluetoothPrinter = {
  device: null,
  characteristic: null,

  SERVICE_UUID: '000018f0-0000-1000-8000-00805f9b34fb',
  CHARACTERISTIC_UUID: '00002af1-0000-1000-8000-00805f9b34fb',

  ESC: 0x1b,
  GS: 0x1d,

  async scanAndConnect() {
    if (!navigator.bluetooth) {
      this._toast('Browser tidak mendukung Web Bluetooth. Pakai Chrome/Edge.', 'error');
      return false;
    }

    try {
      this._toast('🔍 Mencari printer Bluetooth...', 'info');

      this.device = await navigator.bluetooth.requestDevice({
        filters: [{ services: [this.SERVICE_UUID] }],
        optionalServices: [this.SERVICE_UUID],
      });

      this.device.addEventListener('gattserverdisconnected', () => {
        this._toast('Printer terputus', 'error');
        this.characteristic = null;
      });

      const server = await this.device.gatt.connect();
      const service = await server.getPrimaryService(this.SERVICE_UUID);
      this.characteristic = await service.getCharacteristic(this.CHARACTERISTIC_UUID);

      this._toast(`✅ Terhubung: ${this.device.name || 'Printer Bluetooth'}`, 'success');
      return true;
    } catch (error) {
      console.error('Bluetooth connect error:', error);
      this._toast('Gagal menghubungkan printer: ' + error.message, 'error');
      return false;
    }
  },

  async scanServices() {
    if (!this.device) {
      const connected = await this.scanAndConnect().catch(() => false);
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
      const ok = await this.scanAndConnect();
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
   *   metodeBayar, uangBayar, kembalian,   // uangBayar/kembalian hanya kalau cash
   *   catatan,                             // optional
   *   wifiPassword,                        // optional
   * }
   */
  async printReceipt(dataStruk) {
    const connected = this.characteristic || (await this.scanAndConnect());
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
      alert(message);
    }
  },
};
