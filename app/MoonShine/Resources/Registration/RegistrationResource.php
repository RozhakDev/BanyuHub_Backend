<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Registration;

use Illuminate\Database\Eloquent\Model;
use App\Models\Registration;
use App\MoonShine\Resources\Registration\Pages\RegistrationIndexPage;
use App\MoonShine\Resources\Registration\Pages\RegistrationFormPage;
use App\MoonShine\Resources\Registration\Pages\RegistrationDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Registration, RegistrationIndexPage, RegistrationFormPage, RegistrationDetailPage>
 */
class RegistrationResource extends ModelResource
{
    protected string $model = Registration::class;

    protected string $title = 'Pendaftaran';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            RegistrationIndexPage::class,
            RegistrationFormPage::class,
            RegistrationDetailPage::class,
        ];
    }
}
