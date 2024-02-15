<?php

namespace App\Models\Admin;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ColumnFillable;
use Illuminate\Support\Str;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ColumnFillable;
    protected $table = 'pages';
    protected $primaryKey = 'page_id';
    public $incrementing = false;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new PageFactory();
    }

    protected static function boot()
    {
        parent::boot();
        Page::creating(function ($model) {
            $model->page_id
                = Str::uuid();
        });
    }


    public static function createOrUpdatePage(array $request, string $page_id): object
    {
        $page = Page::updateOrCreate(
            ['page_id' => $page_id],
            $request
        );

        return $page;
    }

    /**
     * Get the user's profile picture.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function displayHeaderImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (new ImageService())->getFileUrl('page_images', $attributes['page_header_image'], 'public'),
        );
    }
}
