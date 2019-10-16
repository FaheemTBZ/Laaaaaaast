<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class ItemSupplier extends Model implements HasMedia
{    
    use HasMediaTrait, Notifiable;
    
    protected $guarded = [];
    protected $table = 'item_suppliers';
    
    public function items()
    {
        return $this->belongsTo('App\Item');
    }
}
