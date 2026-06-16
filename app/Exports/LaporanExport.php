<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(
        protected string $dari,
        protected string $sampai
    ) {}

    public function collection()
    {
        $rows = collect();

        $transaksi = Transaksi::selesai()
            ->with(['user', 'detailTransaksi.menu.resepProduk.bahanBaku'])
            ->rentang($this->dari, $this->sampai . ' 23:59:59')
            ->get();

        foreach ($transaksi as $trx) {
            foreach ($trx->detailTransaksi as $detail) {
                // Hitung modal per item
                $modalPerUnit = 0;
                if ($detail->menu && $detail->menu->resepProduk) {
                    foreach ($detail->menu->resepProduk as $resep) {
                        $modalPerUnit += $resep->jumlah * $resep->bahanBaku->harga_per_satuan;
                    }
                }
                $totalModal  = $modalPerUnit * $detail->qty;
                $profit      = $detail->subtotal - $totalModal;

                $rows->push([
                    'nomor_transaksi' => $trx->nomor_transaksi,
                    'tanggal'         => $trx->tanggal->format('d/m/Y'),
                    'jam'             => $trx->tanggal->format('H:i:s'),
                    'kasir'           => $trx->user->name,
                    'menu'            => $detail->nama_menu,
                    'qty'             => $detail->qty,
                    'harga'           => $detail->harga_saat_transaksi,
                    'subtotal'        => $detail->subtotal,
                    'modal'           => $totalModal,
                    'profit'          => $profit,
                    'total_bayar'     => $trx->total,
                    'metode_bayar'    => strtoupper($trx->metode_bayar),
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'Jam',
            'Kasir',
            'Menu',
            'Qty',
            'Harga (Rp)',
            'Subtotal (Rp)',
            'Modal (Rp)',
            'Profit (Rp)',
            'Total Bayar (Rp)',
            'Metode Bayar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a1d27'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data rows style
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            // Zebra striping
            for ($i = 2; $i <= $highestRow; $i++) {
                $color = $i % 2 === 0 ? 'f9f9f9' : 'ffffff';
                $sheet->getStyle("A{$i}:L{$i}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color],
                    ],
                ]);
            }

            // Format angka kolom G, H, I, J, K sebagai currency
            $sheet->getStyle("G2:K{$highestRow}")->getNumberFormat()
                  ->setFormatCode('#,##0');

            // Center align kolom tertentu
            $sheet->getStyle("B2:D{$highestRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$highestRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L2:L{$highestRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Warna profit negatif merah
            for ($i = 2; $i <= $highestRow; $i++) {
                $profitCell = $sheet->getCell("J{$i}")->getValue();
                if ($profitCell < 0) {
                    $sheet->getStyle("J{$i}")->getFont()->getColor()->setRGB('e07c7c');
                } else {
                    $sheet->getStyle("J{$i}")->getFont()->getColor()->setRGB('2e7d32');
                }
            }
        }

        // Border seluruh tabel
        $sheet->getStyle("A1:L{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'dddddd'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, // No. Transaksi
            'B' => 12, // Tanggal
            'C' => 10, // Jam
            'D' => 18, // Kasir
            'E' => 20, // Menu
            'F' => 6,  // Qty
            'G' => 14, // Harga
            'H' => 14, // Subtotal
            'I' => 14, // Modal
            'J' => 14, // Profit
            'K' => 16, // Total Bayar
            'L' => 14, // Metode Bayar
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->dari . ' sd ' . $this->sampai;
    }
}