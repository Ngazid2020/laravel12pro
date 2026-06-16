<?php

namespace App\Filament\Widgets;

use App\Models\MemberProfile;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class NouveauxMembresChart extends ChartWidget
{
    protected static ?string $heading = 'Nouveaux membres actifs';
    protected static ?string $description = 'Évolution sur les 12 derniers mois';
    protected static ?int $sort = 2;
    protected static string $color = 'info';
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        $raw = MemberProfile::query()
            ->where('membership_status', 'active')
            ->whereNotNull('activated_at')
            ->where('activated_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(fn ($p) => Carbon::parse($p->activated_at)->format('Y-m'))
            ->map->count();

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date   = now()->subMonths($i);
            $key    = $date->format('Y-m');
            $labels[] = $mois[$date->month - 1].' '.$date->format('Y');
            $values[] = $raw->get($key, 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Nouveaux membres',
                    'data'            => $values,
                    'fill'            => true,
                    'tension'         => 0.4,
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointRadius'     => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales'  => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1, 'precision' => 0],
                ],
            ],
        ];
    }
}
