<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaiementsChart extends ChartWidget
{
    protected static ?string $heading = 'Paiements par statut';
    protected static ?string $description = 'Déclarés sur les 6 derniers mois';
    protected static ?int $sort = 3;
    protected static string $color = 'success';
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        $labels   = [];
        $pending  = [];
        $validated = [];
        $rejected = [];

        for ($i = 5; $i >= 0; $i--) {
            $date   = now()->subMonths($i);
            $labels[] = $mois[$date->month - 1].' '.$date->format('Y');

            $base = Payment::query()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $pending[]   = (clone $base)->where('status', 'pending')->count();
            $validated[] = (clone $base)->where('status', 'validated')->count();
            $rejected[]  = (clone $base)->where('status', 'rejected')->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'En attente',
                    'data'            => $pending,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.85)',
                    'borderColor'     => 'rgb(245, 158, 11)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Validés',
                    'data'            => $validated,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.85)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Rejetés',
                    'data'            => $rejected,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.85)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => false],
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1, 'precision' => 0]],
            ],
        ];
    }
}
