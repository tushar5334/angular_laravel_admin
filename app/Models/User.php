<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ColumnFillable;
use Illuminate\Support\Str;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasFactory, Notifiable;
    use SoftDeletes;
    use ColumnFillable;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /* protected $fillable = [
        'name',
        'email',
        'password',
    ]; */

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new UserFactory();
    }

    protected static function boot()
    {
        parent::boot();
        User::creating(function ($model) {
            $model->user_id
                = Str::uuid();
        });
    }

    /**
     * Get and Set the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    /* protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
            set: fn ($value) => strtolower($value),
        );
    } */

    /**
     * Get the user's profile picture.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function displayPicture(): Attribute
    {

        return Attribute::make(
            get: function ($value, $attributes) {
                if (isset($attributes['profile_picture'])) {
                    return (new ImageService())->getFileUrl('user_images', $attributes['profile_picture'], 'public');
                } else {
                    return (new ImageService())->getFileUrl('user_images', "", 'public');
                }
            }
        );

        /* return Attribute::make(
            get: fn ($value, $attributes) => (new ImageService())->getFileUrl('user_images', $attributes['profile_picture'], 'public'),
        ); */
    }

    public static function createUpdateUser(array $request, string $user_id): object
    {
        $user = User::updateOrCreate(
            ['user_id' => $user_id],
            $request
        );

        return $user;
    }
}
