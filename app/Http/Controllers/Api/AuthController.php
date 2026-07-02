<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $r){
        $d=$r->validate([
            'name'=>'required|string|max:120',
            'email'=>'nullable|email|unique:users,email',
            'phone'=>'nullable|string|max:20|unique:users,phone',
            'password'=>'required|min:6|confirmed'
        ]);
        if(empty($d['email']) && empty($d['phone'])) return response()->json(['message'=>'Email or phone required'],422);
        $u=User::create(['name'=>$d['name'],'email'=>$d['email']??null,'phone'=>$d['phone']??null,'password'=>$d['password'],'role'=>'customer']);
        return response()->json(['status'=>'success','message'=>'Registration successful','token'=>$u->createToken('auth')->plainTextToken,'user'=>$u],201);
    }

    public function login(Request $r){
        $d=$r->validate(['login'=>'required','password'=>'required']);
        $u=User::where('email',$d['login'])->orWhere('phone',$d['login'])->first();
        if(!$u || !Hash::check($d['password'],$u->password)) return response()->json(['status'=>'failed','message'=>'Login failed'],401);
        return response()->json(['status'=>'success','message'=>'Login successful','token'=>$u->createToken('auth')->plainTextToken,'user'=>$u]);
    }

    public function forgotPasswordSendCode(Request $r){
        $d=$r->validate(['email'=>'nullable|email','phone'=>'nullable|string|max:20']);
        $code=(string)random_int(100000,999999);
        PasswordResetOtp::create([
            'email'=>$d['email']??null,'phone'=>$d['phone']??null,'code_hash'=>Hash::make($code),'expires_at'=>now()->addMinutes(10)
        ]);
        return response()->json(['status'=>'success','message'=>'Reset code sent','demo_otp'=>app()->isLocal()?$code:null]);
    }

    public function resetPassword(Request $r){
        $d=$r->validate([
            'email'=>'nullable|email','phone'=>'nullable|string|max:20',
            'code'=>'required|digits:6','password'=>'required|min:6|confirmed'
        ]);
        $otp=PasswordResetOtp::when($d['email']??null,fn($q,$v)=>$q->where('email',$v))
            ->when($d['phone']??null,fn($q,$v)=>$q->where('phone',$v))
            ->latest()->first();
        if(!$otp || now()->gt($otp->expires_at) || !Hash::check($d['code'],$otp->code_hash)) return response()->json(['message'=>'Invalid or expired code'],422);

        $u=User::when($d['email']??null,fn($q,$v)=>$q->where('email',$v))
            ->when($d['phone']??null,fn($q,$v)=>$q->orWhere('phone',$v))->firstOrFail();
        $u->update(['password'=>$d['password']]); $u->tokens()->delete();
        return response()->json(['status'=>'success','message'=>'Password reset successful']);
    }

    public function logout(Request $r){ $r->user()?->currentAccessToken()?->delete(); return response()->json(['status'=>'success','message'=>'Logged out']);}
}