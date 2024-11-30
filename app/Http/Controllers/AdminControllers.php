<?php

namespace App\Http\Controllers;
// require_once vender\;

use App\Models\Otp;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminControllers extends Controller
{
    // User login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'userName' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('user_name', $fields['userName'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad credentials'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        return response(['user' => $user, 'token' => $token], 200);
    }

    // User registration
    public function register(Request $request)
    {
        $request->validate([
            'user_name' => 'required|unique:users,user_name',
            'contact' => 'required|unique:users,contact',
            'password' => 'required'
        ]);

        User::create([
            'user_name' => $request->user_name,
            'contact' => $request->contact,
            'password' => bcrypt($request->password),
        ]);


        return response()->json(['message' => 'Registration successful'], 201);
    }

    // Update user details
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_name' => 'sometimes|required|unique:users,user_name,' . $id,
            'contact' => 'sometimes|required|unique:users,contact,' . $id,
            'password' => 'sometimes|nullable|min:8',
        ]);

        $user->update([
            'user_name' => $request->user_name,
            'contact' => $request->contact,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return response()->json(['message' => 'Update successful'], 200);
    }

    // User logout
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }

    // Get user details
    public function userDetails()
    {
        $user = Auth::user(['id', 'user_name']);
        return response()->json($user, 200);
    }

    // Get all users
    public function getAllUser()
    {
        $users = User::select('id', 'user_name', 'contact')->get();

        return response()->json($users, 200);
    }

    // Send OTP function
    public function sendOTP(Request $request)
    {
        $request->validate(['contact' => 'required', 'userName' => 'required']);
        $contact = $request->contact;
        $userName = $request->userName;

        $user = Teacher::where('user_name', 'LIKE', '%' . $userName)->first();

        if ($user) {
            $queryParams = http_build_query([
                'recipient' => '94' . $contact,
                'sender_id' => 'TextLKDemo',
                'type' => 'otp',
                'message' => 'Use this as the OTP Code is: {{OTP4}}',
            ]);

            $url = 'https://app.text.lk/api/v3/sms/send?'.$queryParams;

            $response = Http::withToken('62|u9MhYN6e0faDAOlFyWznAxII9cDFtbCNo65IEKvNdcd92f65')
            ->post($url);
            Log::info($response->status());
            Log::info('asjsajdsj');

            Log::info($response->body());

            if ($response->successful()) {
                Otp::create([
                    'contact' => $contact,
                    'otp' => $response['data']['otp'],
                    'expires_at' => now()->addMinutes(2)
                ]);
                return response()->json(['message' => 'OTP successfully sent!'], 200);
            } else {
                return response()->json(['message' => 'Invalid OTP'], 400);
                Log::info($response->body());
                Log::info($response->status());
            }
        } else {
            return response()->json(['message' => 'Invalied User..!'], 430);
        }
    }

    // Verify OTP function
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'contact' => 'required',
        ]);

        $userOtp = Otp::where('contact', $request->contact)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($userOtp->otp == $request->otp) {
            return response()->json(['message' => 'OTP verified successfully'], 200);
        } else {
            return response()->json(['message' => 'Invalid OTP'], 401);
        }
    }

    // Change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'contact' => 'required',
            'userName' => 'required|exists:teachers,user_name',
            'password' => 'required|min:4|string',
        ]);
        $userName = $request->userName;
        $contact = $request->contact;
        $password = $request->password;

        $user = Teacher::where('user_name', $userName)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($password),
                'contact' => $contact,
            ]);
            return response()->json(['message' => 'Password changed successfully'], 200);
        } else {
            return response()->json(['message' => 'Invalid user'], 404);
        }
    }

    function generateOTP()
    {
        return rand(1000, 9999);
    }
}
