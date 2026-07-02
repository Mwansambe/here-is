<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable=['category_id','name','slug','brand','description','specifications','price','discount_price','stock','is_popular','is_featured','is_active'];
    protected $casts=['specifications'=>'array','is_popular'=>'boolean','is_featured'=>'boolean','is_active'=>'boolean'];
    public function category(){ return $this->belongsTo(Category::class); }
    public function images(){ return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
}