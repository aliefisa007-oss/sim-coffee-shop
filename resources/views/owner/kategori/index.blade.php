@extends('layouts.app')
@section('title', 'Kategori Menu')
@section('page-title', 'Kategori Menu')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-title">➕ Tambah Kategori</div>
            <form method="POST" action="{{ route('owner.kategori.store') }}">
                @csrf
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Kode Kategori</label>
                    <input type="text" name="kode_kategori" class="form-control mt-1" placeholder="KAT-005" required>
                    @error('kode_kategori')<div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control mt-1" placeholder="Contoh: Snack" required>
                    @error('nama_kategori')<div style="color:#e07c7c; font-size:11px; margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label style="font-size:11px; color:#888; text-transform:uppercase;">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="form-control mt-1"></textarea>
                </div>
                <button type="submit" class="btn-gold w-100">Simpan Kategori</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="chart-title mb-0">🗂️ Daftar Kategori</div>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                           style="background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:6px 12px; font-size:12px;">
                    <button type="submit" style="padding:6px 14px; background:#1a1d27; border:1px solid #2a2d38; color:#888; border-radius:8px; font-size:12px; cursor:pointer;">Cari</button>
                </form>
            </div>
            <table class="table-dark-custom">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jumlah Menu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $kat)
                    <tr>
                        <td style="color:#c8a97e; font-weight:600;">{{ $kat->kode_kategori }}</td>
                        <td>{{ $kat->nama_kategori }}</td>
                        <td style="color:#888;">{{ $kat->menus_count }} menu</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button onclick="editKategori({{ $kat->id }}, '{{ $kat->kode_kategori }}', '{{ $kat->nama_kategori }}', '{{ $kat->deskripsi }}')"
                                        style="padding:4px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#5b8dee; font-size:11px; cursor:pointer;">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('owner.kategori.destroy', $kat->id) }}"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="padding:4px 10px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#e07c7c; font-size:11px; cursor:pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#555; padding:30px;">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $kategori->links() }}</div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#1a1d27; border:1px solid #23262f; border-radius:16px; padding:30px; max-width:400px; width:90%;">
        <div style="font-size:14px; font-weight:600; color:#c8a97e; margin-bottom:20px;">✏️ Edit Kategori</div>
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label style="font-size:11px; color:#888; text-transform:uppercase;">Kode Kategori</label>
                <input type="text" name="kode_kategori" id="editKode" class="form-control mt-1" required>
            </div>
            <div class="mb-3">
                <label style="font-size:11px; color:#888; text-transform:uppercase;">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="editNama" class="form-control mt-1" required>
            </div>
            <div class="mb-3">
                <label style="font-size:11px; color:#888; text-transform:uppercase;">Deskripsi</label>
                <textarea name="deskripsi" id="editDeskripsi" rows="2" class="form-control mt-1"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="tutupModal()"
                        style="flex:1; padding:10px; border-radius:8px; border:1px solid #2a2d38; background:transparent; color:#888; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" class="btn-gold" style="flex:1; padding:10px; border-radius:8px;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editKategori(id, kode, nama, deskripsi) {
    document.getElementById('formEdit').action = '/owner/kategori/' + id;
    document.getElementById('editKode').value      = kode;
    document.getElementById('editNama').value      = nama;
    document.getElementById('editDeskripsi').value = deskripsi || '';
    document.getElementById('modalEdit').style.display = 'flex';
}
function tutupModal() {
    document.getElementById('modalEdit').style.display = 'none';
}
</script>
@endpush