/**
 * Web Bluetooth ESC/POS Thermal Printer — SIM Coffee (Contact Coffee & Eatery)
 * Layout dibikin mirip versi RawBT: header 2 baris, info transaksi rapi
 * kolom kiri-kanan, tabel item, badge metode bayar (reverse/hitam).
 * Logo & barcode BELUM diimplementasi (nanti menyusul).
 *
 * CARA PAKAI: sama seperti sebelumnya —
 *   await BluetoothPrinter.pairPrinter();     // sekali saja
 *   await BluetoothPrinter.printReceipt(data); // tiap transaksi
 */

const BluetoothPrinter = {
  device: null,
  characteristic: null,

  SERVICE_UUID: '000018f0-0000-1000-8000-00805f9b34fb',
  CHARACTERISTIC_UUID: '00002af1-0000-1000-8000-00805f9b34fb',

  // Ganti sesuai lebar kertas printer kamu. 58mm dengan font default
  // biasanya 32 karakter/baris. Kalau teks kepotong/ke-wrap aneh,
  // coba ganti ke 42 atau 48.
  CHARS_PER_LINE: 32,

  ESC: 0x1b,
  GS: 0x1d,

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
      }
      return ok;
    } catch (error) {
      console.error('Pairing error:', error);
      this._toast('Gagal memasangkan printer: ' + error.message, 'error');
      return false;
    }
  },

  async autoConnect() {
    if (!navigator.bluetooth || !navigator.bluetooth.getDevices) return false;
    try {
      const devices = await navigator.bluetooth.getDevices();
      if (!devices || devices.length === 0) return false;
      return await this._connectToDevice(devices[0]);
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
    if (options.reverse) commands.push(this.GS, 0x42, 0x01); // white-on-black

    const bytes = new Uint8Array([...commands, ...this._textToBytes(text), 0x0a]);
    await this._write(bytes);

    // Reset semua formatting supaya baris berikutnya normal lagi
    if (options.bold || options.large || options.reverse) {
      await this._write(
        new Uint8Array([this.ESC, 0x45, 0x00, this.GS, 0x21, 0x00, this.GS, 0x42, 0x00])
      );
    }
  },

  /**
   * Print 1 baris dengan teks kiri & kanan, dipisah spasi otomatis
   * sampai penuh lebar kertas. Kalau gabungan kepanjangan, kanan
   * dipindah ke baris baru sendiri (rata kanan).
   */
  async printRow(left, right = '', options = {}) {
    const width = this.CHARS_PER_LINE;
    left = String(left);
    right = String(right);

    if (left.length + right.length + 1 > width) {
      await this.printText(left, options);
      await this.printText(right.padStart(width), options);
      return;
    }

    const spaces = width - left.length - right.length;
    await this.printText(left + ' '.repeat(Math.max(spaces, 1)) + right, options);
  },

  async printDivider(char = '-') {
    await this.printText(char.repeat(this.CHARS_PER_LINE), { align: 0 });
  },

  async cutPaper() {
    await this._write(new Uint8Array([this.GS, 0x56, 0x00]));
  },

  /**
   * dataStruk = {
   *   namaToko, tagline, alamat, telepon, sosmed,
   *   kasir, nomorTransaksi, tanggal, jam,
   *   items: [{ nama, hargaSatuan, qty, subtotal }],
   *   subtotal, diskon, pajak, pajakPersen, total,
   *   metodeBayar, uangBayar, kembalian,
   *   catatan, wifiPassword,
   * }
   */
  async printReceipt(dataStruk) {
    const connected = await this._ensureConnected();
    if (!connected) return false;

    try {
      // ── Header ──
      await this.printText(dataStruk.namaToko, { align: 1, bold: true, large: true });
      if (dataStruk.tagline) await this.printText(dataStruk.tagline, { align: 1 });
      if (dataStruk.alamat) await this.printText(dataStruk.alamat, { align: 1 });
      if (dataStruk.telepon) await this.printText(dataStruk.telepon, { align: 1 });
      if (dataStruk.sosmed) await this.printText(dataStruk.sosmed, { align: 1 });
      await this.printDivider();

      // ── Info transaksi (kolom kiri-kanan) ──
      await this.printRow('No. Transaksi', dataStruk.nomorTransaksi);
      await this.printRow('Tanggal', dataStruk.tanggal);
      if (dataStruk.jam) await this.printRow('Jam', dataStruk.jam);
      await this.printRow('Kasir', dataStruk.kasir);
      await this.printRow('Metode Bayar', dataStruk.metodeBayar.toUpperCase());
      await this.printDivider();

      // ── Tabel item ──
      await this.printRow('ITEM', 'QTY   TOTAL', { bold: true });
      for (const item of dataStruk.items) {
        await this.printText(item.nama, { align: 0 });
        const hargaLabel = `Rp ${item.hargaSatuan.toLocaleString('id-ID')} / pcs`;
        const qtyTotal = `${item.qty}x  Rp ${item.subtotal.toLocaleString('id-ID')}`;
        await this.printRow(hargaLabel, qtyTotal);
      }
      await this.printDivider();

      // ── Ringkasan total ──
      await this.printRow('Subtotal', `Rp ${dataStruk.subtotal.toLocaleString('id-ID')}`);
      if (dataStruk.diskon > 0) {
        await this.printRow('Diskon', `-Rp ${dataStruk.diskon.toLocaleString('id-ID')}`);
      }
      if (dataStruk.pajak > 0) {
        const label = dataStruk.pajakPersen ? `Pajak (${dataStruk.pajakPersen}%)` : 'Pajak';
        await this.printRow(label, `Rp ${dataStruk.pajak.toLocaleString('id-ID')}`);
      }
      await this.printDivider();
      await this.printRow('TOTAL', `Rp ${dataStruk.total.toLocaleString('id-ID')}`, {
        bold: true,
        large: true,
      });

      if (dataStruk.uangBayar) {
        await this.printRow('Tunai', `Rp ${dataStruk.uangBayar.toLocaleString('id-ID')}`);
        await this.printRow('Kembali', `Rp ${dataStruk.kembalian.toLocaleString('id-ID')}`);
      }

      // ── Badge metode bayar (reverse video, mirip kotak hitam RawBT) ──
      await this.printText('');
      const badge = dataStruk.metodeBayar.toUpperCase();
      const padded = badge.padStart((this.CHARS_PER_LINE + badge.length) / 2).padEnd(
        this.CHARS_PER_LINE
      );
      await this.printText(padded, { align: 1, reverse: true });
      await this.printText('');

      if (dataStruk.catatan) {
        await this.printDivider();
        await this.printText('Catatan: ' + dataStruk.catatan, { align: 0 });
      }

      await this.printDivider();
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