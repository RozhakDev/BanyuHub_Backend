<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use App\MoonShine\Resources\Event\EventResource;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Registration\RegistrationResource;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\Review\ReviewResource;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            
            \MoonShine\MenuManager\MenuGroup::make('Manajemen Event', [
                MenuItem::make(EventResource::class, 'Data Event')->icon('calendar-days'),
                MenuItem::make(RegistrationResource::class, 'Pendaftaran Event')->icon('ticket'),
                MenuItem::make(ReviewResource::class, 'Ulasan Event')->icon('star'),
            ])->icon('queue-list'),

            \MoonShine\MenuManager\MenuGroup::make('Data Pengguna', [
                MenuItem::make(UserResource::class, 'Data Mahasiswa')->icon('users'),
            ])->icon('user-group'),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }
}
