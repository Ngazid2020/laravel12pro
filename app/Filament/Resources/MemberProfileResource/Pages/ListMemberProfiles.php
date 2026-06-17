<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Exports\MembresExport;
use App\Filament\Resources\MemberProfileResource;
use App\Models\MemberProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListMemberProfiles extends ListRecords
{
    protected static string $resource = MemberProfileResource::class;

    protected function getHeaderActions(): array
    {
        $statusOptions = [
            'all'       => 'Tous les membres',
            'active'    => 'Actifs uniquement',
            'candidate' => 'Candidats',
            'suspended' => 'Suspendus',
            'excluded'  => 'Exclus',
            'alumni'    => 'Alumni',
        ];

        $filterForm = [
            Forms\Components\Select::make('status')
                ->label('Filtrer par statut')
                ->options($statusOptions)
                ->default('all')
                ->required(),
        ];

        return [
            Actions\CreateAction::make(),

            Actions\ActionGroup::make([
                Actions\Action::make('exportCsv')
                    ->label('Exporter en CSV')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->form($filterForm)
                    ->action(fn (array $data) => Excel::download(
                        new MembresExport($data['status']),
                        'membres-'.now()->format('Y-m-d').'.csv',
                        \Maatwebsite\Excel\Excel::CSV,
                        ['Content-Type' => 'text/csv'],
                    )),

                Actions\Action::make('exportExcel')
                    ->label('Exporter en Excel (.xlsx)')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->form($filterForm)
                    ->action(fn (array $data) => Excel::download(
                        new MembresExport($data['status']),
                        'membres-'.now()->format('Y-m-d').'.xlsx',
                    )),

                Actions\Action::make('exportPdf')
                    ->label('Exporter en PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->form($filterForm)
                    ->action(function (array $data) {
                        $query = MemberProfile::with(['user', 'mentor.user'])
                            ->orderBy('membership_status')
                            ->orderBy('created_at', 'desc');

                        if ($data['status'] !== 'all') {
                            $query->where('membership_status', $data['status']);
                        }

                        $membres = $query->get();

                        return Pdf::loadView('exports.membres-pdf', compact('membres'))
                            ->setPaper('a4', 'landscape')
                            ->download('membres-'.now()->format('Y-m-d').'.pdf');
                    }),
            ])
            ->label('Exporter')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('info')
            ->button(),
        ];
    }
}
