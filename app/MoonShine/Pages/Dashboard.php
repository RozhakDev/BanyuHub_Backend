<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Grid::make([
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Total Event')
                        ->value(\App\Models\Event::count())
                        ->icon('calendar-days')
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Total Mahasiswa')
                        ->value(\App\Models\User::count())
                        ->icon('users')
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Pendaftaran')
                        ->value(\App\Models\Registration::count())
                        ->icon('ticket')
                ])->columnSpan(3),
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Total Ulasan')
                        ->value(\App\Models\Review::count())
                        ->icon('star')
                ])->columnSpan(3),
            ]),
            
            \MoonShine\UI\Components\Layout\Grid::make([
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\Apexcharts\Components\LineChartMetric::make('Pertumbuhan Event (30 Hari)')
                        ->series([
                            \MoonShine\Apexcharts\Support\SeriesItem::make('Event Baru', \App\Models\Event::where('created_at', '>=', now()->subDays(30))
                                ->get()
                                ->groupBy(fn($e) => $e->created_at->format('Y-m-d'))
                                ->map->count()
                                ->toArray()
                            )
                        ])
                ])->columnSpan(12),
                
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\Apexcharts\Components\LineChartMetric::make('Pertumbuhan Pendaftaran (30 Hari)')
                        ->series([
                            \MoonShine\Apexcharts\Support\SeriesItem::make('Pendaftaran', \App\Models\Registration::where('created_at', '>=', now()->subDays(30))
                                ->get()
                                ->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
                                ->map->count()
                                ->toArray()
                            )
                        ])
                ])->columnSpan(12),
            ]),
        ];
    }
}
