<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class RenewSubscriptions extends Command
{

    protected $signature = 'subscriptions:renew {user}';

    protected $description = 'Renew subscriptions for a user';

    public function handle()
    {
        $userIdOrEmail = $this->argument('user');

        $user = User::where('id', $userIdOrEmail)->orWhere('email', $userIdOrEmail)->first();

        if (!$user) {
            $this->error('User not found.');
            return;
        }

        $this->info('Checking subscriptions for user ' . $user->name . '...');

        $subscriptions = $user->subscriptions;

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found for user ' . $user->name . '.');
            return;
        }

        foreach ($subscriptions as $subscription) {
            $this->info('Checking subscription ID: ' . $subscription->id);

            if ($subscription->expired_at > now()) {
                $this->warn('Subscription is not expired yet for ID: ' . $subscription->id);
                continue;
            }

            $subscription->update(['expired_at' => now()->addMonths(1)]);

            $this->info('Subscription renewal successful for ID: ' . $subscription->id);

        }

        $this->info('Subscription renewal process completed.');

    }
}
