<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $appends = ['image_url','nama_baru'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // get img
    public function getImageUrlAttribute()
    {
        return $this->image !== null ? url('storage/'.$this->image) : null;
    }

    public function getNamaBaruAttribute()
    {
        return strtoupper($this->name);
    }
}
