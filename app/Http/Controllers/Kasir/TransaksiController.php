<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function __construct(
        protected TransaksiRepositoryInterface $repo
    ) {}

    public function index(Request $request)
    {
        $filters   = array_merge(
            $request->only(['dari', 'sampai', 'status']),
            ['user_id' => Auth::id()]
        );
        $transaksi = $this->repo->getAll($filters);
        return view('kasir.transaksi.index', compact('transaksi'));
    }

    public function indexOwner(Request $request)
    {
        $transaksi = $this->repo->getAll($request->only(['dari', 'sampai', 'status', 'user_id']));
        return view('kasir.transaksi.index', compact('transaksi'));
    }

    public function show(int $id)
    {
        $transaksi = $this->repo->findById($id);
        return view('kasir.transaksi.show', compact('transaksi'));
    }

    public function cancel(Request $request, int $id)
{
    $request->validate([
        'alasan_batal' => ['required', 'string', 'max:255'],
    ]);

    $transaksi = $this->repo->findById($id);

    // Cek apakah transaksi milik kasir ini
    if (auth()->user()->isKasir() && $transaksi->user_id !== auth()->id()) {
        return back()->with('error', 'Anda tidak bisa membatalkan transaksi ini.');
    }

    // Cek apakah sudah dibatalkan
    if ($transaksi->status === 'batal') {
        return back()->with('error', 'Transaksi sudah dibatalkan sebelumnya.');
    }

    // Update status transaksi
    $transaksi->update([
        'status'          => 'batal',
        'alasan_batal'    => $request->alasan_batal,
        'dibatalkan_at'   => now(),
        'dibatalkan_oleh' => auth()->id(),
    ]);

    // Kembalikan stok bahan baku
    foreach ($transaksi->detailTransaksi as $detail) {
        $menu = \App\Models\Menu::with('resepProduk.bahanBaku')->find($detail->menu_id);
        if ($menu) {
            foreach ($menu->resepProduk as $resep) {
                $bahan       = $resep->bahanBaku;
                $stokSebelum = $bahan->stok;
                $stokSesudah = $stokSebelum + ($resep->jumlah * $detail->qty);

                $bahan->update(['stok' => $stokSesudah]);

                \App\Models\RiwayatStok::create([
                    'bahan_baku_id' => $bahan->id,
                    'user_id'       => auth()->id(),
                    'transaksi_id'  => $transaksi->id,
                    'tipe'          => 'masuk',
                    'jumlah'        => $resep->jumlah * $detail->qty,
                    'stok_sebelum'  => $stokSebelum,
                    'stok_sesudah'  => $stokSesudah,
                    'keterangan'    => 'Retur dari pembatalan transaksi #' . $transaksi->nomor_transaksi,
                ]);
            }
        }
    }

    return back()->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
}

    public function struk(int $id)
{
    $transaksi = $this->repo->findById($id);
    return view('kasir.struk', compact('transaksi'));
}
public function laporan(Request $request)
{
    $dari   = $request->get('dari', now()->format('Y-m-d'));
    $sampai = $request->get('sampai', now()->format('Y-m-d'));

    // Kalau kasir, hanya lihat transaksi sendiri
    // Kalau owner, lihat semua
    $query = \App\Models\Transaksi::selesai()
        ->with(['user', 'detailTransaksi'])
        ->whereBetween('tanggal', [
            $dari . ' 00:00:00',
            $sampai . ' 23:59:59'
        ]);

    if (auth()->user()->isKasir()) {
        $query->where('user_id', auth()->id());
    }

    $transaksi = $query->orderBy('tanggal')->get();

    // Kelompokkan per tanggal
    $perTanggal = $transaksi->groupBy(fn($t) => $t->tanggal->format('Y-m-d'));

    return view('kasir.laporan', compact('perTanggal', 'dari', 'sampai', 'transaksi'));
}
}