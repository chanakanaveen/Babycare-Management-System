<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Midwife extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'midwives';
    protected $guard = 'midwife';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'picture',
        'phone',
        'address',
        'provinces',
        'districts',
        'cities',
        'latitude',
        'longitude',
        'email_verified_at',
        'verified',
        'status',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function getPictureAttribute($value){
        if( $value ){
            return asset('/images/users/sellers/'.$value);
        }else{
            return asset('/images/users/default-avatar.png');
        }
    }

    /**
     * Get all babies assigned to this midwife.
     */
    public function assignedBabies()
    {
        return $this->hasMany(Baby::class, 'midwife_id');
    }

    /**
     * Scope: only approved midwives.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: only unapproved midwives.
     */
    public function scopeUnapproved($query)
    {
        return $query->where('is_approved', false);
    }
}
