<?php

namespace App\Models;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model};

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'document_type',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'share_with_doctor',
        'share_with_specialists',
        'share_with_family',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
