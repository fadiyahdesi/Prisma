<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AkreditasiMultiSheetExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new PenelitianSheet($this->data['penelitian'] ?? []),
            new PkmSheet($this->data['pkm'] ?? []),
            new PublikasiSheet($this->data['publikasi'] ?? []),
            new HkiSheet($this->data['hki'] ?? []),
        ];
    }
}

class PenelitianSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function title(): string
    {
        return '3.b.1 Penelitian DTPS';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Judul Penelitian',
            'Nama Dosen (DTPS)',
            'NIDN',
            'Fakultas',
            'Program Studi',
            'Skema Hibah',
            'Pagu Disetujui (Rp)',
            'Nomor Kontrak/SPK',
            'Status',
            'TKT',
        ];
    }

    public function array(): array
    {
        return array_map(function ($r) {
            return [
                $r['no'],
                $r['tahun'],
                $r['judul'],
                $r['nama_dosen'],
                $r['nidn'],
                $r['fakultas'],
                $r['prodi'],
                $r['skema'],
                $r['pagu'],
                $r['nomor_kontrak'],
                $r['status'],
                $r['tkt'],
            ];
        }, $this->rows);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'], // Navy Blue
                ],
            ],
        ];
    }
}

class PkmSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function title(): string
    {
        return '3.b.2 PkM DTPS';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Judul Pengabdian kepada Masyarakat (PkM)',
            'Nama Dosen (DTPS)',
            'NIDN',
            'Fakultas',
            'Program Studi',
            'Mitra Sasaran',
            'Pagu Dana (Rp)',
            'Sumber Dana',
        ];
    }

    public function array(): array
    {
        return array_map(function ($r) {
            return [
                $r['no'],
                $r['tahun'],
                $r['judul'],
                $r['nama_dosen'],
                $r['nidn'],
                $r['fakultas'],
                $r['prodi'],
                $r['mitra'],
                $r['pagu'],
                $r['sumber_dana'],
            ];
        }, $this->rows);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF047857'], // Emerald Green
                ],
            ],
        ];
    }
}

class PublikasiSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function title(): string
    {
        return '3.b.3 Publikasi Ilmiah';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun Terbit',
            'Judul Artikel Ilmiah',
            'Nama Dosen Penulis',
            'NIDN',
            'Fakultas',
            'Program Studi',
            'Nama Jurnal',
            'ISSN',
            'Kategori Peringkat',
            'Volume & Nomor',
            'DOI / Tautan',
            'Sitasi',
        ];
    }

    public function array(): array
    {
        return array_map(function ($r) {
            return [
                $r['no'],
                $r['tahun'],
                $r['judul'],
                $r['nama_dosen'],
                $r['nidn'],
                $r['fakultas'],
                $r['prodi'],
                $r['nama_jurnal'],
                $r['issn'],
                $r['peringkat'],
                $r['volume_nomor'],
                $r['doi'],
                $r['sitasi'],
            ];
        }, $this->rows);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF6B21A8'], // Purple
                ],
            ],
        ];
    }
}

class HkiSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function title(): string
    {
        return '3.b.4 HKI & Paten';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun Perolehan',
            'Jenis HKI',
            'Judul Ciptaan / Invensi / Desain',
            'Inventor / Dosen',
            'NIDN',
            'Fakultas',
            'Program Studi',
            'Nomor Permohonan',
            'Nomor Sertifikat',
            'Tanggal Terbit',
            'Pemegang Hak',
        ];
    }

    public function array(): array
    {
        return array_map(function ($r) {
            return [
                $r['no'],
                $r['tahun'],
                $r['jenis'],
                $r['judul'],
                $r['inventor'],
                $r['nidn'],
                $r['fakultas'],
                $r['prodi'],
                $r['nomor_permohonan'],
                $r['nomor_sertifikat'],
                $r['tanggal_terbit'],
                $r['pemegang_hak'],
            ];
        }, $this->rows);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC2410C'], // Orange/Amber
                ],
            ],
        ];
    }
}

