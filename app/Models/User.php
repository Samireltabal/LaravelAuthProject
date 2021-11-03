<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Hash;
use Str; 
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements MustVerifyEmail , HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;


    public $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'uuid',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
        'media'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'role',
        'avatar'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
        ->singleFile()
        ->useFallbackUrl('/v.png')
        ->useFallbackPath(public_path('/v.png'));
    }

    public function getAvatarAttribute() {
        return $this->getFirstMediaUrl('avatars');
    }

    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    public function otp() {
        return $this->hasOne('App\Models\Auth\Otp','user_id');
    } 

    public function getRoleAttribute() {
        return $this->roles[0]->name;
    }

}
