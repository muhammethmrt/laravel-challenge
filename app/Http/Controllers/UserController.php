<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
    }
    public function register(Request $request)
    {

        try {
            $registerUserData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|min:8'
            ]);

            $user = User::create([
                'name' => $registerUserData['name'],
                'email' => $registerUserData['email'],
                'password' => Hash::make($registerUserData['password']),
            ]);
            return response()->json([
                'message' => 'success',
                'user' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    public function login(Request $request)
    {

        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $loginUserData['email'])->first();
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'message' => 'success',
            'access_token' => $token,
        ]);

    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function createSubscription(Request $request, $userId)
    {
        try {

            $request->validate([
                'renewed_at' => 'required|date',
                'expired_at' => 'required|date'
            ]);

            $user = User::findOrFail($userId);

            $subscription = Subscription::create([
                'renewed_at' => $request['renewed_at'],
                'expired_at' => $request['expired_at'],
                'user_id' => $userId
            ]);

            return response()->json(['message' => 'success', 'subscription' => $subscription], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    public function updateSubscription(Request $request, $id, $subscriptionId)
    {
        try {

            $request->validate([
                'renewed_at' => 'required|date',
                'expired_at' => 'required|date'
            ]);

            $subscription = Subscription::findOrFail($subscriptionId);

            $user = User::findOrFail($id);

            $update = $subscription->update([
                'renewed_at' => $request['renewed_at'],
                'expired_at' => $request['expired_at']
            ]);

            return response()->json(['message' => 'success', 'subscription' => $subscription], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    public function deleteSubscription(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $subscription = Subscription::where('user_id', $id);
        $subscription->delete();

        return response()->json(['message' => 'success'], 200);

    }

    public function createTransaction(Request $request, $userId)
    {
        try {

            $request->validate([
                'subscription_id' => 'required|int',
                'price' => 'required|numeric'
            ]);

            $user = User::findOrFail($userId);

            $transaction = Transaction::create([
                'subscription_id' => $request['subscription_id'],
                'price' => $request['price']
            ]);

            return response()->json(['message' => 'success', 'subscription' => $transaction], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }


    public function getUserWithDetails(Request $request, $userId)
    {

        $user = User::findOrFail($userId);

        $user = User::with('subscriptions.transactions')->find($userId);

        return response()->json(['user' => $user], 200);

    }


}
