<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller {
    public function categories(){ return Category::where('is_active',true)->orderBy('name')->get(); }
    public function products(Request $r){
        $q=Product::with(['category','images'])->where('is_active',true);
        if($r->filled('category_id')) $q->where('category_id',$r->integer('category_id'));
        if($r->filled('popular')) $q->where('is_popular',(bool)$r->popular);
        if($r->filled('search')){
            $s=strtolower($r->search);
            $q->whereRaw('LOWER(name) like ?',["%$s%"]);
        }
        return $q->paginate(20);
    }
    public function banners(){ return Banner::where('is_active',true)->orderBy('sort_order')->get(); }
}