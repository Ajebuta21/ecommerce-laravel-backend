<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $user = new User();
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = bcrypt(request()->password);
        $user->save();

        return response()->json(['message' => 'Account created'], 201);
    }

    public function updateNameEmail(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => 'Profile updated'], 200);
    }

    public function updatePhoneAddress(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|unique:users,phone,' . $user->id,
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json(['message' => 'Contact information updated'], 200);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        // Check if old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 403);
        }

        // Update password
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated'], 200);
    }

    public function index()
    {
        $user = User::all();

        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function changeRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user = User::findOrFail($id);

        $user->role = $request->role;

        $user->save();

        return response()->json(['message' => 'Role updated.'], 201);
    }
}
