<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductGallery extends Model
{
    protected $guarded = [];
    protected $table = "product_gallery_images";
    public $sortOrder = 'desc';
    public $sortEntity = 'product_gallery_images.id';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }



}
