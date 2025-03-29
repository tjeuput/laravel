<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Factories\HasFactory,Model};

class Role extends Model
{
    protected $fillable = ['name'];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
