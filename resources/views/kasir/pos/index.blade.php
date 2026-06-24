@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Point of Sale')

@section('content')
<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; height:calc(100vh - 100px);">
    <div style="display:flex; flex-direction:column; gap:12px;">
        <input id="searchMenu" type="text" placeholder="🔍 Cari menu..."
               style="background:#1a1d27; border:1px solid #23262f; color:#e8e6e0; border-radius:10px; padding:10px 14px; font-size:13px; outline:none; width:100%;">
        <div class="d-flex gap-2 flex-wrap">
            @foreach(['Semua'] + \App\Models\KategoriMenu::pluck('nama_kategori')->toArray() as $kat)
                <button onclick="filterKategori('{{ $kat }}')" class="filter-btn" data-kat="{{ $kat }}"
                        style="padding:5px 14px; border-radius:20px; font-size:12px; cursor:pointer; border:1px solid {{ $kat === 'Semua' ? '#c8a97e' : '#2a2d38' }}; background:{{ $kat === 'Semua' ? 'rgba(200,169,126,0.12)' : 'transparent' }}; color:{{ $kat === 'Semua' ? '#c8a97e' : '#666' }}; transition:all 0.15s;">
                    {{ $kat }}
                </button>
            @endforeach
        </div>
        <div id="menuGrid" style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; overflow-y:auto; flex:1;">
            @foreach($menus as $menu)
                <div class="menu-card" data-id="{{ $menu->id }}" data-nama="{{ $menu->nama_menu }}"
                     data-harga="{{ $menu->harga_jual }}" data-kat="{{ $menu->kategori->nama_kategori }}"
                     onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->nama_menu) }}', {{ $menu->harga_jual }})"
                     style="background:#1a1d27; border:1px solid #23262f; border-radius:12px; padding:16px 12px; cursor:pointer; text-align:center; transition:all 0.15s;">
                    <div style="font-size:28px; margin-bottom:8px;">☕</div>
                    <div style="font-size:12px; font-weight:600; margin-bottom:2px;">{{ $menu->nama_menu }}</div>
                    <div style="font-size:10px; color:#555; margin-bottom:6px;">{{ $menu->kategori->nama_kategori }}</div>
                    <div style="font-size:13px; font-weight:700; color:#c8a97e;">Rp {{ number_format($menu->harga_jual, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="background:#161920; border:1px solid #23262f; border-radius:12px; display:flex; flex-direction:column; overflow:hidden;">
        <div style="padding:16px 18px; border-bottom:1px solid #23262f; font-size:13px; font-weight:600; color:#c8a97e; text-transform:uppercase; letter-spacing:0.08em;">
            🛒 Keranjang <span id="cartCount" style="background:#c8a97e; color:#1a1208; border-radius:10px; padding:1px 8px; font-size:11px; margin-left:6px;">0</span>
        </div>
        <div id="cartItems" style="flex:1; overflow-y:auto; padding:12px;">
            <div id="emptyCart" style="text-align:center; color:#444; font-size:12px; margin-top:40px;">
                <div style="font-size:32px; margin-bottom:8px;">☕</div>Keranjang kosong
            </div>
        </div>
        <div style="padding:14px 16px; border-top:1px solid #23262f;">
            <div style="font-size:11px; color:#666; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.06em;">Metode Bayar</div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px; margin-bottom:12px;">
                @foreach(['cash' => '💵 Cash', 'qris' => '📱 QRIS', 'transfer' => '🏦 Transfer'] as $val => $label)
                    <button onclick="setMetode('{{ $val }}')" id="btn-{{ $val }}"
                            style="padding:7px 0; border-radius:8px; border:1px solid {{ $val === 'cash' ? '#c8a97e' : '#2a2d38' }}; background:{{ $val === 'cash' ? 'rgba(200,169,126,0.12)' : 'transparent' }}; color:{{ $val === 'cash' ? '#c8a97e' : '#666' }}; font-size:11px; font-weight:600; cursor:pointer;">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div style="display:flex; justify-content:space-between; font-size:12px; color:#888; margin-bottom:4px;"><span>Subtotal</span><span id="subtotal">Rp 0</span></div>
            <div style="display:flex; justify-content:space-between; font-size:12px; color:#888; margin-bottom:8px;"><span>Pajak (10%)</span><span id="pajak">Rp 0</span></div>
            <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:700; padding-top:8px; border-top:1px solid #23262f; margin-bottom:14px;">
                <span>Total</span><span id="total" style="color:#c8a97e;">Rp 0</span>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
    <span style="font-size:12px; color:#888;">
        Diskon
    </span>

    <div>
    <label>Diskon (%)</label>

    <input 
        type="number"
        id="diskonInput"
        value="0"
        min="0"
        max="100"
        style="width:80px"
        oninput="renderCart()">

</div>

</div>
            </div>
            {{-- Catatan --}}
<div style="margin-bottom:12px;">
    <input type="text" id="catatanInput" placeholder="📝 Catatan (contoh: Dandy/16/outdoor)"
           style="width:100%; background:#0f1117; border:1px solid #2a2d38; color:#e8e6e0; border-radius:8px; padding:8px 12px; font-size:11px; outline:none;">
</div>
            <button onclick="bayar()" style="width:100%; padding:12px; background:linear-gradient(135deg,#c8a97e,#a87d50); border:none; border-radius:10px; color:#1a1208; font-size:13px; font-weight:700; cursor:pointer; letter-spacing:0.06em;">
                BAYAR SEKARANG
            </button>
        </div>
    </div>
</div>

<div id="modalSukses" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#1a1d27; border:1px solid #c8a97e44; border-radius:16px; padding:36px 40px; text-align:center; max-width:320px; width:90%;">
        <div style="font-size:48px; margin-bottom:12px;">✅</div>
        <div style="font-size:16px; font-weight:700; color:#3ecf8e; margin-bottom:6px;">Transaksi Berhasil!</div>
        <div id="nomorTrx" style="font-size:12px; color:#888; margin-bottom:4px;"></div>
        <div id="totalTrx" style="font-size:22px; font-weight:700; color:#c8a97e; margin-bottom:20px;"></div>
        <div style="display:flex; gap:8px;">
    <button onclick="cetakStruk()"
            style="flex:1; padding:12px; background:#1a1d27; border:1px solid #c8a97e; border-radius:10px; color:#c8a97e; font-size:13px; font-weight:700; cursor:pointer;">
        🖨️ Cetak Struk
    </button>
    <button onclick="transaksiSelesai()"
            style="flex:1; padding:12px; background:linear-gradient(135deg,#c8a97e,#a87d50); border:none; border-radius:10px; color:#1a1208; font-size:13px; font-weight:700; cursor:pointer;">
        Transaksi Baru
    </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = {};
let metodeBayar = 'cash';
let lastTransaksiId = null;

const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');

function addToCart(id, nama, harga) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id, nama, harga, qty: 1 };
    }
    renderCart();
}

function changeQty(id, delta) {
    id = parseInt(id);
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const items = Object.values(cart);
    const container = document.getElementById('cartItems');

    if (items.length === 0) {
        container.innerHTML = `
            <div style="text-align:center; color:#444; font-size:12px; margin-top:40px;">
                <div style="font-size:32px; margin-bottom:8px;">☕</div>
                Keranjang kosong
            </div>`;
        updateTotals(0);
        document.getElementById('cartCount').textContent = '0';
        return;
    }

    let html = '';
    items.forEach(item => {
        html += `
        <div style="display:flex; align-items:center; gap:8px; padding:8px 10px; background:#1a1d27; border-radius:8px; margin-bottom:6px;">
            <div style="font-size:18px;">☕</div>
            <div style="flex:1;">
                <div style="font-size:12px; font-weight:500; color:#e8e6e0;">${item.nama}</div>
                <div style="font-size:11px; color:#888; margin-top:2px;">${fmt(item.harga)}</div>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
                <button onclick="changeQty(${item.id}, -1)"
                        style="width:24px; height:24px; border-radius:6px; border:1px solid #2a2d38; background:#0f1117; color:#c8a97e; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;">
                    −
                </button>
                <span style="font-size:13px; font-weight:600; color:#e8e6e0; min-width:20px; text-align:center;">
                    ${item.qty}
                </span>
                <button onclick="changeQty(${item.id}, +1)"
                        style="width:24px; height:24px; border-radius:6px; border:1px solid #2a2d38; background:#0f1117; color:#c8a97e; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center;">
                    +
                </button>
            </div>
        </div>`;
    });

    container.innerHTML = html;

    const subtotal = items.reduce((s, i) => s + i.harga * i.qty, 0);
    updateTotals(subtotal);
    document.getElementById('cartCount').textContent = items.reduce((s, i) => s + i.qty, 0);
}

function updateTotals(subtotal) {

    const diskonPersen = Number(
    document.getElementById('diskonInput').value || 0
);

const diskon = Math.round(
    subtotal * diskonPersen / 100
);

    const pajak = Math.round(subtotal * 0.1);

    const total = subtotal - diskon + pajak;


    document.getElementById('subtotal').textContent = fmt(subtotal);

    document.getElementById('pajak').textContent = fmt(pajak);

    document.getElementById('total').textContent = fmt(total);
}

function setMetode(m) {
    metodeBayar = m;
    ['cash', 'qris', 'transfer'].forEach(v => {
        const btn = document.getElementById('btn-' + v);
        if (!btn) return;
        btn.style.border     = v === m ? '1px solid #c8a97e' : '1px solid #2a2d38';
        btn.style.background = v === m ? 'rgba(200,169,126,0.12)' : 'transparent';
        btn.style.color      = v === m ? '#c8a97e' : '#666';
    });
}

function filterKategori(kat) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const active = btn.dataset.kat === kat;
        btn.style.border     = active ? '1px solid #c8a97e' : '1px solid #2a2d38';
        btn.style.background = active ? 'rgba(200,169,126,0.12)' : 'transparent';
        btn.style.color      = active ? '#c8a97e' : '#666';
    });
    document.querySelectorAll('.menu-card').forEach(card => {
        card.style.display = (kat === 'Semua' || card.dataset.kat === kat) ? 'block' : 'none';
    });
}

document.getElementById('searchMenu').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.menu-card').forEach(card => {
        card.style.display = card.dataset.nama.toLowerCase().includes(q) ? 'block' : 'none';
    });
});

function bayar() {
    const items = Object.values(cart);
    if (items.length === 0) {
        alert('Keranjang kosong!');
        return;
    }

    const subtotal = items.reduce((s, i) => s + i.harga * i.qty, 0);
    const pajak    = Math.round(subtotal * 0.1);
    const diskonPersen = Number(
    document.getElementById('diskonInput').value || 0);
    const diskon = Math.round(
    subtotal * diskonPersen / 100);
    const total = subtotal - diskon + pajak;
    const catatan  = document.getElementById('catatanInput')
                     ? document.getElementById('catatanInput').value
                     : '';

    const payload = {
        items: items.map(i => ({
            menu_id:   i.id,
            nama_menu: i.nama,
            harga:     i.harga,
            qty:       i.qty,
            subtotal:  i.harga * i.qty,
        })),
        metode_bayar: metodeBayar,
        pajak:        pajak,
        diskon: diskon,
        catatan:      catatan,
    };

    fetch('{{ route("kasir.pos.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(payload),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            lastTransaksiId = data.transaksi_id;
            document.getElementById('nomorTrx').textContent = data.nomor_transaksi;
            document.getElementById('totalTrx').textContent = fmt(data.total);
            document.getElementById('modalSukses').style.display = 'flex';
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan. Coba lagi.');
    });
}

function cetakStruk() {
    if (!lastTransaksiId) {
        alert('ID transaksi tidak ditemukan!');
        return;
    }
    const url = '{{ url("kasir/transaksi") }}/' + lastTransaksiId + '/struk';
    window.open(url, '_blank', 'width=420,height=700,scrollbars=yes');
}

function transaksiSelesai() {
    cart = {};
    renderCart();
    document.getElementById('modalSukses').style.display = 'none';
    if (document.getElementById('catatanInput')) {
        document.getElementById('catatanInput').value = '';
    }
}
</script>
@endpush