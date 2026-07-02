<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function show(Request $r)
    {
        return response()->json($r->user());
    }

    public function update(Request $r)
    {
        $d = $r->validate([
            'name' => 'sometimes|string|max:120',
            'email' => 'sometimes|email|unique:users,email,' . $r->user()->id,
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $r->user()->id,
            'password' => 'sometimes|min:6|confirmed',
        ]);

        $user = $r->user();
        $updateData = [];

        if (isset($d['name'])) $updateData['name'] = $d['name'];
        if (isset($d['email'])) $updateData['email'] = $d['email'];
        if (isset($d['phone'])) $updateData['phone'] = $d['phone'];
        if (isset($d['password'])) $updateData['password'] = $d['password'];

        $user->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated',
            'user' => $user->fresh(),
        ]);
    }
}
