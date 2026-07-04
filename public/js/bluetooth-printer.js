/**
 * Web Bluetooth ESC/POS Thermal Printer — SIM Coffee (Contact Coffee & Eatery)
 * v2 — perbaikan:
 *   1. Nama toko: large SAJA (tanpa bold) → hindari distorsi font di printer clone
 *   2. Word-wrap manual untuk teks panjang (header) → tidak lagi kepotong di tengah kata
 *   3. Baris "Metode Bayar" di info transaksi dihapus (sudah ada badge di bawah TOTAL)
 *   4. Baris TOTAL dipisah jadi 2 printText (label + nilai align kanan) → tidak lagi
 *      berantakan saat large:true, karena lebar efektif kertas jadi setengah CHARS_PER_LINE
 *
 * CATATAN PENTING soal total "Rp 36000.00": itu bug terpisah di pos/index.blade.php —
 * data.total dari server masih string decimal ("36000.00"), harus di-convert
 * `Number(data.total)` SEBELUM dimasukkan ke lastDataStruk. Lihat instruksi di bawah.
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

  // Lebar kertas dalam dots. 58mm ≈ 384 dots, 80mm ≈ 576 dots (di 203dpi).
  // Cek spek printer kamu — kalau logo kepotong di kanan atau ke-crop aneh,
  // ini angkanya yang salah.
  PRINTER_WIDTH_DOTS: 384,

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
   * Load gambar dari URL jadi elemen <img> yang siap digambar ke canvas.
   */
  _loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.crossOrigin = 'anonymous'; // aman kalau logo di-host di domain sama
      img.onload = () => resolve(img);
      img.onerror = () => reject(new Error('Gagal load gambar logo: ' + src));
      img.src = src;
    });
  },

  /**
   * KONVERSI: gambar → bitmap 1-bit hitam/putih siap kirim ke printer.
   * - Resize ke targetWidth (px = dots)
   * - Threshold sederhana: pixel dianggap "hitam" kalau gelap & tidak transparan
   * - Pack 8 pixel jadi 1 byte (format wajib ESC/POS GS v 0)
   */
  async _imageToRaster(img, targetWidth) {
    const scale = targetWidth / img.width;
    const width = targetWidth;
    const height = Math.round(img.height * scale);

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff'; // background putih (kalau logo PNG transparan)
    ctx.fillRect(0, 0, width, height);
    ctx.drawImage(img, 0, 0, width, height);

    const { data } = ctx.getImageData(0, 0, width, height);
    const widthBytes = Math.ceil(width / 8);
    const raster = new Uint8Array(widthBytes * height);

    // Threshold: makin kecil angkanya, makin sedikit yang dianggap "hitam".
    // Kalau logo hasil print kelihatan terlalu tebal/pudar, ubah nilai ini (0-255).
    const THRESHOLD = 160;

    for (let y = 0; y < height; y++) {
      for (let x = 0; x < width; x++) {
        const i = (y * width + x) * 4;
        const r = data[i], g = data[i + 1], b = data[i + 2], a = data[i + 3];
        const gray = r * 0.299 + g * 0.587 + b * 0.114;
        const isDark = a > 128 && gray < THRESHOLD;
        if (isDark) {
          const byteIndex = y * widthBytes + Math.floor(x / 8);
          const bitIndex = 7 - (x % 8);
          raster[byteIndex] |= (1 << bitIndex);
        }
      }
    }

    return { raster, widthBytes, height };
  },

  /**
   * Cetak logo/gambar. Panggil ini SEBELUM printText nama toko.
   * options.width = lebar target dalam dots (default: sesuaikan PRINTER_WIDTH_DOTS
   *   atau kasih angka lebih kecil, mis. 200, biar ada margin kiri-kanan)
   * options.align = 0 kiri, 1 tengah, 2 kanan
   */
  async printImage(src, options = {}) {
    const connected = await this._ensureConnected();
    if (!connected) return false;

    try {
      const targetWidth = options.width || Math.round(this.PRINTER_WIDTH_DOTS * 0.6);
      const img = await this._loadImage(src);
      const { raster, widthBytes, height } = await this._imageToRaster(img, targetWidth);

      if (options.align !== undefined) {
        await this._write(new Uint8Array([this.ESC, 0x61, options.align]));
      }

      const xL = widthBytes & 0xff;
      const xH = (widthBytes >> 8) & 0xff;
      const yL = height & 0xff;
      const yH = (height >> 8) & 0xff;

      // GS v 0 m xL xH yL yH d1...dk — raster bit image command
      const header = new Uint8Array([this.GS, 0x76, 0x30, 0x00, xL, xH, yL, yH]);
      const bytes = new Uint8Array(header.length + raster.length);
      bytes.set(header, 0);
      bytes.set(raster, header.length);

      await this._write(bytes);
      return true;
    } catch (error) {
      console.error('printImage error:', error);
      this._toast('Gagal mencetak logo: ' + error.message, 'error');
      return false;
    }
  },

  /**
   * BARU: pecah teks panjang jadi beberapa baris, wrap di batas SPASI
   * (bukan di tengah kata), supaya "Friendliness" tidak lagi jadi
   * "Friendline" + "ss". Dipakai untuk header (nama toko, tagline, alamat, dst).
   */
  _wrapText(text, width) {
    const words = String(text).split(' ');
    const lines = [];
    let current = '';

    for (const word of words) {
      const candidate = current ? current + ' ' + word : word;
      if (candidate.length > width) {
        if (current) lines.push(current);
        // Kalau satu kata saja sudah lebih panjang dari width, potong paksa
        current = word.length > width ? word.slice(0, width) : word;
      } else {
        current = candidate;
      }
    }
    if (current) lines.push(current);
    return lines;
  },

  async printWrapped(text, options = {}) {
    const lines = this._wrapText(text, this.CHARS_PER_LINE);
    for (const line of lines) {
      await this.printText(line, options);
    }
  },

  /**
   * Print 1 baris dengan teks kiri & kanan, dipisah spasi otomatis
   * sampai penuh lebar kertas. Kalau gabungan kepanjangan, kanan
   * dipindah ke baris baru sendiri (rata kanan).
   *
   * CATATAN: jangan pakai fungsi ini untuk teks dengan option `large`,
   * karena lebar efektif kertas jadi setengahnya dan padding di sini
   * dihitung berdasarkan CHARS_PER_LINE penuh → hasilnya wrap berantakan.
   * Untuk baris besar (mis. TOTAL), print label & nilai secara terpisah
   * pakai printText (lihat printReceipt di bawah).
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
   *
   * PENTING: pastikan semua field angka (subtotal, diskon, pajak, total,
   * uangBayar, kembalian) sudah berupa Number, BUKAN string. Kalau salah
   * satu masih string (mis. dari decimal cast Laravel via API), panggilan
   * `.toLocaleString('id-ID')` tidak akan memformat apa-apa dan hasilnya
   * jadi "36000.00" alih-alih "36.000". Convert dengan Number(...) di
   * pemanggil (pos/index.blade.php) sebelum membuat objek ini.
   */
  async printReceipt(dataStruk) {
    const connected = await this._ensureConnected();
    if (!connected) return false;

    try {
      // ── Logo (opsional) ──
      if (dataStruk.logoUrl) {
        await this.printImage(dataStruk.logoUrl, { align: 1, width: 220 });
        await this.printText('');
      }

      // ── Header ──
      // Nama toko: large SAJA, tanpa bold, supaya font tidak terdistorsi
      await this.printWrapped(dataStruk.namaToko, { align: 1, large: true });
      if (dataStruk.tagline) await this.printWrapped(dataStruk.tagline, { align: 1 });
      if (dataStruk.alamat) await this.printWrapped(dataStruk.alamat, { align: 1 });
      if (dataStruk.telepon) await this.printWrapped(dataStruk.telepon, { align: 1 });
      if (dataStruk.sosmed) await this.printWrapped(dataStruk.sosmed, { align: 1 });
      await this.printDivider();

      // ── Info transaksi (kolom kiri-kanan) ──
      // "Metode Bayar" dihapus dari sini — sudah ditampilkan sebagai badge di bawah TOTAL
      await this.printRow('No. Transaksi', dataStruk.nomorTransaksi);
      await this.printRow('Tanggal', dataStruk.tanggal);
      if (dataStruk.jam) await this.printRow('Jam', dataStruk.jam);
      await this.printRow('Kasir', dataStruk.kasir);
      await this.printDivider();

      // ── Tabel item ──
      await this.printRow('ITEM', 'QTY   TOTAL', { bold: true });
      for (const item of dataStruk.items) {
        await this.printText(item.nama, { align: 0 });
        const hargaLabel = `Rp ${Number(item.hargaSatuan).toLocaleString('id-ID')} / pcs`;
        const qtyTotal = `${item.qty}x  Rp ${Number(item.subtotal).toLocaleString('id-ID')}`;
        await this.printRow(hargaLabel, qtyTotal);
      }
      await this.printDivider();

      // ── Ringkasan total ──
      await this.printRow('Subtotal', `Rp ${Number(dataStruk.subtotal).toLocaleString('id-ID')}`);
      if (dataStruk.diskon > 0) {
        await this.printRow('Diskon', `-Rp ${Number(dataStruk.diskon).toLocaleString('id-ID')}`);
      }
      if (dataStruk.pajak > 0) {
        const label = dataStruk.pajakPersen ? `Pajak (${dataStruk.pajakPersen}%)` : 'Pajak';
        await this.printRow(label, `Rp ${Number(dataStruk.pajak).toLocaleString('id-ID')}`);
      }
      await this.printDivider();

      // TOTAL: label & nilai dipisah (BUKAN printRow) supaya tidak wrap berantakan
      // saat pakai large. Nilai rata kanan pakai align:2.
      await this.printText('TOTAL', { align: 0, bold: true });
      await this.printText(`Rp ${Number(dataStruk.total).toLocaleString('id-ID')}`, {
        align: 2,
        bold: true,
        large: true,
      });

      if (dataStruk.uangBayar) {
        await this.printRow('Tunai', `Rp ${Number(dataStruk.uangBayar).toLocaleString('id-ID')}`);
        await this.printRow('Kembali', `Rp ${Number(dataStruk.kembalian).toLocaleString('id-ID')}`);
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