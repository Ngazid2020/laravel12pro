<?php

namespace App\Filament\Widgets;

use App\Models\MemberProfile;
use Filament\Widgets\ChartWidget;

class MembresParSecteurChart extends ChartWidget
{
    protected static ?string $heading = 'Répartition par secteur';
    protected static ?string $description = 'Membres actifs uniquement';
    protected static ?int $sort = 4;
    protected static string $color = 'warning';
    protected static ?string $maxHeight = '280px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = MemberProfile::query()
            ->where('membership_status', 'active')
            ->whereNotNull('sector')
            ->get()
            ->groupBy('sector')
            ->map->count()
            ->sortDesc()
            ->take(9);

        $palette = [
            'rgba(59, 130, 246, 0.85)',   // bleu
            'rgba(34, 197, 94, 0.85)',    // vert
            'rgba(245, 158, 11, 0.85)',   // jaune
            'rgba(168, 85, 247, 0.85)',   // violet
            'rgba(239, 68, 68, 0.85)',    // rouge
            'rgba(20, 184, 166, 0.85)',   // teal
            'rgba(249, 115, 22, 0.85)',   // orange
            'rgba(236, 72, 153, 0.85)',   // rose
            'rgba(99, 102, 241, 0.85)',   // indigo
        ];

        return [
            'datasets' => [
                [
                    'data'                   => array_values($data->values()->toArray()),
                    'backgroundColor'        => array_slice($palette, 0, $data->count()),
                    'hoverOffset'            => 6,
                ],
            ],
            'labels' => array_values($data->keys()->toArray()),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels'   => ['boxWidth' => 12, 'padding' => 10],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
