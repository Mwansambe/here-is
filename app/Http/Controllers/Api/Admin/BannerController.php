<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller {
    public function index(){ return Banner::orderBy('sort_order')->get(); }
    public function store(Request $r){
        $d=$r->validate([
            'title'=>'required|max:160','subtitle'=>'nullable|max:255','badge'=>'nullable|max:50',
            'cta_text'=>'nullable|max:50','cta_url'=>'nullable|max:255','sort_order'=>'nullable|integer|min:0',
            'is_active'=>'boolean','image'=>'required|image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);
        $path=$r->file('image')->store('banners','public');
        $b=Banner::create([...$d,'image_path'=>$path,'is_active'=>$d['is_active']??true,'sort_order'=>$d['sort_order']??0]);
        return response()->json(['message'=>'Banner created','banner'=>$b],201);
    }
    public function destroy(Banner $banner){ Storage::disk('public')->delete($banner->image_path); $banner->delete(); return response()->json(['message'=>'Banner deleted']);}
}