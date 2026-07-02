<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PasswordResetOtp extends Model {
    protected $fillable=['email','phone','code_hash','expires_at','verified_at','attempts'];
    protected $casts=['expires_at'=>'datetime','verified_at'=>'datetime'];
}