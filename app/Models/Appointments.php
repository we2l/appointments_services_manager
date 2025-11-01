<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'title',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
