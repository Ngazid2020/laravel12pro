<?php

namespace App\Exports;

use App\Models\MemberProfile;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembresExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private string $status = 'all') {}

    public function query()
    {
        $query = MemberProfile::with('user')
            ->orderBy('membership_status')
            ->orderBy('created_at', 'desc');

        if ($this->status !== 'all') {
            $query->where('membership_status', $this->status);
        }

        return $query;
    }

    public function title(): string
    {
        return 'Membres';
    }

    public function headings(): array
    {
        return [
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'Entreprise / Projet',
            'Secteur',
            'Ville',
            'Statut',
            'Date d\'adhésion',
            'Expiration',
            'Mentor',
            'Code parrainage',
        ];
    }

    public function map($profile): array
    {
        $statusLabels = [
            'active'    => 'Actif',
            'candidate' => 'Candidat',
            'suspended' => 'Suspendu',
            'excluded'  => 'Exclu',
            'alumni'    => 'Alumni',
        ];

        $mentor = $profile->mentor?->user?->name ?? '—';

        return [
            $profile->user->first_name ?? '',
            $profile->user->last_name  ?? '',
            $profile->user->email,
            $profile->user->phone      ?? '—',
            $profile->company_name ?? $profile->project_name ?? '—',
            $profile->sector       ?? '—',
            $profile->city         ?? '—',
            $statusLabels[$profile->membership_status] ?? $profile->membership_status,
            $profile->activated_at?->format('d/m/Y')           ?? '—',
            $profile->membership_expires_at?->format('d/m/Y')  ?? '—',
            $mentor,
            $profile->referral_code ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // En-tête : fond primaire, texte blanc, gras
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1D4ED8'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
