<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    public function index(){ return Category::orderBy('name')->get(); }
    public function store(Request $r){
        $d=$r->validate(['name'=>'required|unique:categories,name','icon'=>'nullable|string|max:100','is_active'=>'boolean']);
        $c=Category::create(['name'=>$d['name'],'slug'=>Str::slug($d['name']),'icon'=>$d['icon']??null,'is_active'=>$d['is_active']??true]);
        return response()->json(['message'=>'Category created','category'=>$c],201);
    }
    public function update(Request $r, Category $category){
        $d=$r->validate(['name'=>'sometimes|string|max:100|unique:categories,name,'.$category->id,'icon'=>'nullable|string|max:100','is_active'=>'boolean']);
        if(isset($d['name'])) $d['slug']=Str::slug($d['name']);
        $category->update($d);
        return response()->json(['message'=>'Category updated','category'=>$category]);
    }
    public function destroy(Category $category){ $category->delete(); return response()->json(['message'=>'Category deleted']); }
}