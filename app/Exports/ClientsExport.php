<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;


class ClientsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize
{
    public function query()
    {
        return User::role('client')
            ->with('approvedBy:id,name')
            ->select('id', 'name', 'email', 'mobile', 'country_id', 'gender', 'is_approved', 'approved_by', 'created_at');
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Mobile',
            'Country',
            'Gender',
            'Status',
            'Approved By',
            'Registered At',
        ];
    }

    public function map($client): array
    {
        // Get country name from lc_countries
        $country = DB::table('lc_countries')
            ->where('id', $client->country_id)
            ->value('official_name') ?? '—';

        return [
            $client->id,
            $client->name,
            $client->email,
            $client->mobile ?? '—',
            $country,
            ucfirst($client->gender ?? '—'),
            $client->is_approved ? 'Approved' : 'Pending',
            $client->approvedBy?->name ?? '—',
            $client->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold the header row
            1 => ['font' => ['bold' => true]],
        ];
    }
}