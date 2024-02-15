<?php

namespace App\Models\Admin;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasFactory, Notifiable;
    use SoftDeletes;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $appends = ['is_selected', 'display_picture'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'user_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'date:Y-m-d h:i A',
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

    public function password(): Attribute
    {
        return new Attribute(
            set: fn ($value) => Hash::make($value),
        );
    }

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

    protected function isSelected(): Attribute
    {
        return Attribute::make(
            get: fn () => false,
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value === 1) ? true : false,
        );
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
