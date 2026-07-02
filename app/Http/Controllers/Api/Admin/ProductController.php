<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller {
    public function index(){ return Product::with(['category','images'])->latest()->paginate(30); }

    public function store(Request $r){
        $d=$r->validate([
            'category_id'=>'required|exists:categories,id','name'=>'required|max:190','brand'=>'nullable|max:120',
            'description'=>'nullable','specifications'=>'nullable|array','price'=>'required|numeric|min:0',
            'discount_price'=>'nullable|numeric|min:0','stock'=>'required|integer|min:0',
            'is_popular'=>'boolean','is_featured'=>'boolean','is_active'=>'boolean',
            'images.*'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);
        $p=Product::create([...$d,'slug'=>Str::slug($d['name']).'-'.Str::lower(Str::random(6))]);
        if($r->hasFile('images')){
            foreach($r->file('images') as $i=>$f){
                $path=$f->store('products','public');
                ProductImage::create(['product_id'=>$p->id,'path'=>$path,'is_primary'=>$i===0,'sort_order'=>$i]);
            }
        }
        return response()->json(['message'=>'Product created','product'=>$p->load('images')],201);
    }

    public function update(Request $r, Product $product){
        $d=$r->validate([
            'category_id'=>'sometimes|exists:categories,id','name'=>'sometimes|max:190','brand'=>'nullable|max:120',
            'description'=>'nullable','specifications'=>'nullable|array','price'=>'sometimes|numeric|min:0',
            'discount_price'=>'nullable|numeric|min:0','stock'=>'sometimes|integer|min:0',
            'is_popular'=>'boolean','is_featured'=>'boolean','is_active'=>'boolean',
            'images.*'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);
        if(isset($d['name'])) $d['slug']=Str::slug($d['name']).'-'.Str::lower(Str::random(4));
        $product->update($d);
        if($r->hasFile('images')){
            $start=(int)$product->images()->max('sort_order');
            foreach($r->file('images') as $i=>$f){
                $path=$f->store('products','public');
                ProductImage::create(['product_id'=>$product->id,'path'=>$path,'is_primary'=>false,'sort_order'=>$start+$i+1]);
            }
        }
        return response()->json(['message'=>'Product updated','product'=>$product->load('images')]);
    }

    public function destroy(Product $product){
        foreach($product->images as $img){ Storage::disk('public')->delete($img->path); }
        $product->delete(); return response()->json(['message'=>'Product deleted']);
    }
}