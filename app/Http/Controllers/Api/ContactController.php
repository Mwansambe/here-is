<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller {
    public function store(Request $r){
        $d=$r->validate([
            'name'=>'required|string|max:120',
            'email'=>'nullable|email',
            'phone'=>'nullable|string|max:20',
            'subject'=>'required|string|max:160',
            'message'=>'required|string|max:3000',
        ]);
        ContactMessage::create($d);
        return response()->json(['status'=>'success','message'=>'Message sent successfully'],201);
    }
}