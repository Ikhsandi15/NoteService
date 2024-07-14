<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function notes() {
        return $this->hasMany(Note::class,'category_id','id');
    }
}
