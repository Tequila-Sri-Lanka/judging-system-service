<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminControllers extends Controller
{
    // user login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'user_name' => 'required',
            'password' => 'required|min:4|max:12'
        ]);

        $user = Admin::where('user_name', $fields['user_name'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad credits'], 401);
        }

        $response = [
            'user' => $user
        ];
        return response($response, 201);
    }


    // user register
    public function register(Request $request)
    {
        $user = new Admin;
        $user->user_name = $request->userName;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            "message" => "Registration successful",
        ], 201);
    }


    //update user details
    public function userUpdate(Request $request, $id)
    {
        $user = Admin::find($id);
        $user->user_name = $request->user_name;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            "message" => "update successful",
        ], 201);
    }


    // user logout
    public function logout()
    {
        auth()->logout();
        return response()->json([
            "message" => "Logout Successful",
        ], 201);
    }



    //get user details
    public function userDetails()
    {
        $userId = Auth::id();
        $usersDetails = Admin::select('admin_id', 'user_name')
            ->where('admin_id', '=', $userId)
            ->get();

        return response()->json($usersDetails, 200);
    }


    //get all user details
    public function getAllUser()
    {
        $usersDetails = Admin::select('admin_id','user_name')
            ->get();

        return response()->json($usersDetails, 200);
    }
}
