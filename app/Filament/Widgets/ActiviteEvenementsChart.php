<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class ActiviteEvenementsChart extends ChartWidget
{
    protected static ?string $heading = 'Inscriptions par événement';
    protected static ?string $description = '6 derniers événements publiés';
    protected static ?int $sort = 5;
    protected static string $color = 'danger';
    protected static ?string $maxHeight = '280px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $events = Event::query()
            ->where('is_published', true)
            ->withCount('registrations')
            ->latest('starts_at')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $labels   = $events->map(fn ($e) => Str::limit($e->title, 22))->toArray();
        $inscrits = $events->map(fn ($e) => $e->registrations_count)->toArray();
        $capacite = $events->map(fn ($e) => $e->capacity)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Inscrits',
                    'data'            => $inscrits,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.85)',
                    'borderColor'     => 'rgb(168, 85, 247)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Capacité',
                    'data'            => $capacite,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.15)',
                    'borderColor'     => 'rgba(168, 85, 247, 0.4)',
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
            'indexAxis' => 'y',
            'scales'    => [
                'x' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1, 'precision' => 0]],
            ],
        ];
    }
}
