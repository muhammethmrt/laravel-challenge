<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Http\Controllers\PaymentController;
use App\Models\Transaction;

class CronController extends Controller
{
    public function cron()
    {
        $success = [];
        $errors = [];

        $price = 2000;

        $expiredSubscriptions = Subscription::where('expired_at', '<', now())->get();
        foreach ($expiredSubscriptions as $subscription) {

            $paymentController = new PaymentController();
            $paymentSuccess = $paymentController->pay();

            if ($paymentSuccess) {

                $transaction = Transaction::create([
                    'subscription_id' => $subscription->id,
                    'price' => $price,
                ]);

                $subscription->update(['expired_at' => now()->addMonths(1)]);
                $success[] = $subscription->id;

            } else {

                $errors[] = $subscription->id;

            }

        }

        return response()->json(['message' => 'success', 'success' => $success, 'errors' => $errors], 200);

    }
}
