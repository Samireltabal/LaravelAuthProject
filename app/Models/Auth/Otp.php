<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'expire_at'];
	protected $hidden = ['code', 'created_at', 'updated_at'];
    
    public function scopeCode($query, $value) {
    	return $query->where('code', $value);
    }

    public function user() {
    	return $this->belongsTo('App\Models\User','user_id');
    }
}
