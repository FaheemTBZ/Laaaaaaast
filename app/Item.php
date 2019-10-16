<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Item extends Model
{
    protected $guarded = [];
    use HasMediaTrait;
    
    public function itemSuppliers()
    {
        return $this->hasMany('App\ItemSupplier');
    }
    
}
