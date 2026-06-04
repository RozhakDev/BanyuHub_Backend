<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review;

use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
use App\MoonShine\Resources\Review\Pages\ReviewIndexPage;
use App\MoonShine\Resources\Review\Pages\ReviewFormPage;
use App\MoonShine\Resources\Review\Pages\ReviewDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Review, ReviewIndexPage, ReviewFormPage, ReviewDetailPage>
 */
class ReviewResource extends ModelResource
{
    protected string $model = Review::class;

    protected string $title = 'Reviews';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ReviewIndexPage::class,
            ReviewFormPage::class,
            ReviewDetailPage::class,
        ];
    }
}
