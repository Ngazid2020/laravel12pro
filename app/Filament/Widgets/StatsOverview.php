<?php

namespace App\Filament\Widgets;

use App\Models\CandidatureApplication;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\Recommendation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeMembers    = MemberProfile::where('membership_status', 'active')->count();
        $pendingCandidatures = CandidatureApplication::where('status', 'pending')->count();
        $pendingPayments  = Payment::where('status', 'pending')->count();
        $openReco         = Recommendation::whereIn('status', ['pending', 'examining', 'transmitted'])->count();

        return [
            Stat::make('Membres actifs', $activeMembers)
                ->description('Adhérents à jour de cotisation')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Candidatures en attente', $pendingCandidatures)
                ->description('À examiner')
                ->icon('heroicon-o-document-text')
                ->color($pendingCandidatures > 0 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.candidature-applications.index')),

            Stat::make('Paiements à valider', $pendingPayments)
                ->description('En attente de confirmation')
                ->icon('heroicon-o-banknotes')
                ->color($pendingPayments > 0 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.payments.index')),

            Stat::make('Recommandations ouvertes', $openReco)
                ->description('En cours de traitement')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('info'),
        ];
    }
}
