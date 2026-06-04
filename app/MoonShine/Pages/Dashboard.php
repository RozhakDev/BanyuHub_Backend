<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
#[\MoonShine\MenuManager\Attributes\SkipMenu]

/**
 * Class Dashboard
 * 
 * Halaman Dashboard utama untuk admin panel MoonShine.
 * Menyajikan visualisasi data ringkas statistik platform BanyuHub.space (Total Event, User, RSVP, Ulasan),
 * serta charts/grafik pertumbuhan event dan pendaftaran dalam 30 hari terakhir.
 */
class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    /**
     * Mendapatkan daftar breadcrumb navigasi halaman Dashboard.
     * 
     * @return array<string, string> Label dan URL navigasi.
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    /**
     * Mendapatkan judul halaman Dashboard untuk ditampilkan di header admin panel.
     * 
     * @return string Judul halaman.
     */
    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    /**
     * Menyusun komponen UI yang ditampilkan pada Halaman Dashboard.
     * 
     * Menyusun layout grid berisi ValueMetric untuk menampilkan total data dari database secara real-time,
     * serta LineChartMetric untuk menampilkan grafik tren harian penambahan event baru dan pendaftaran baru.
     * 
     * @return list<\MoonShine\Contracts\UI\ComponentContract> Koleksi komponen antarmuka dashboard.
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
