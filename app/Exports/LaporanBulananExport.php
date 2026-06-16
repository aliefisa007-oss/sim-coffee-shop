<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class LaporanBulananExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected array $dataHarian;
    protected array $ringkasan;
    protected string $bulan;

    public function __construct(array $dataHarian, array $ringkasan, string $bulan)
    {
        $this->dataHarian = $dataHarian;
        $this->ringkasan  = $ringkasan;
        $this->bulan      = $bulan;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->dataHarian as $hari) {
            $margin = $hari['total_penjualan'] > 0
                ? ($hari['laba'] / $hari['total_penjualan']) * 100
                : 0;

            $rows->push([
                'tanggal'          => $hari['tanggal_lengkap'],
                'hari'             => $hari['hari'],
                'total_penjualan'  => $hari['total_penjualan'],
                'jumlah_transaksi' => $hari['jumlah_transaksi'],
                'jumlah_item'      => $hari['jumlah_item'],
                'modal'            => $hari['modal'],
                'laba'             => $hari['laba'],
                'margin'           => round($margin, 1),
                'status'           => $hari['total_penjualan'] > 0 ? 'Ada Transaksi' : 'Tidak Ada Transaksi',
            ]);
        }

        // Tambah baris total
        $marginTotal = $this->ringkasan['total_penjualan'] > 0
            ? ($this->ringkasan['total_laba'] / $this->ringkasan['total_penjualan']) * 100
            : 0;

        $rows->push([
            'tanggal'          => 'TOTAL',
            'hari'             => '',
            'total_penjualan'  => $this->ringkasan['total_penjualan'],
            'jumlah_transaksi' => $this->ringkasan['total_transaksi'],
            'jumlah_item'      => $this->ringkasan['total_item'],
            'modal'            => $this->ringkasan['total_modal'],
            'laba'             => $this->ringkasan['total_laba'],
            'margin'           => round($marginTotal, 1),
            'status'           => '',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Hari',
            'Total Penjualan (Rp)',
            'Jumlah Transaksi',
            'Jumlah Item',
            'Modal (Rp)',
            'Laba (Rp)',
            'Margin (%)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:I1')->applyFromArray([
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

        // Zebra striping
        for ($i = 2; $i <= $highestRow - 1; $i++) {
            $color = $i % 2 === 0 ? 'f9f9f9' : 'ffffff';
            $sheet->getStyle("A{$i}:I{$i}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
            ]);
        }

        // Format angka
        if ($highestRow > 1) {
            $sheet->getStyle("C2:C{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F2:G{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("H2:H{$highestRow}")->getNumberFormat()->setFormatCode('0.0"%"');
        }

        // Center align
        $sheet->getStyle("B2:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H2:I{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Total row style (baris terakhir)
        $sheet->getStyle("A{$highestRow}:I{$highestRow}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c8a97e'],
            ],
        ]);

        // Warna laba positif/negatif
        for ($i = 2; $i <= $highestRow - 1; $i++) {
            $laba = $sheet->getCell("G{$i}")->getValue();
            if ($laba > 0) {
                $sheet->getStyle("G{$i}")->getFont()->getColor()->setRGB('2e7d32');
            } elseif ($laba < 0) {
                $sheet->getStyle("G{$i}")->getFont()->getColor()->setRGB('c62828');
            }
        }

        // Border
        $sheet->getStyle("A1:I{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'dddddd'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, // Tanggal
            'B' => 14, // Hari
            'C' => 20, // Total Penjualan
            'D' => 16, // Jumlah Transaksi
            'E' => 14, // Jumlah Item
            'F' => 18, // Modal
            'G' => 18, // Laba
            'H' => 12, // Margin
            'I' => 20, // Status
        ];
    }

    public function title(): string
    {
        return 'Laporan Bulanan ' . $this->bulan;
    }
}