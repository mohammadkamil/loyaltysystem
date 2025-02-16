<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\PointTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CustomerStats extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id(); // Store user ID once for efficiency

        $totalCustomers = Customer::where('business_id', $userId)->count();
        $totalPointsIssued = PointTransaction::whereHas('customer', function ($query) use ($userId) {
            $query->where('business_id', $userId);
        })->where('type', 'add')->sum('points');

        $totalPointsUsed = PointTransaction::whereHas('customer', function ($query) use ($userId) {
            $query->where('business_id', $userId);
        })->where('type', 'deduct')->sum('points');

        return [
            Stat::make('Total Customers', $totalCustomers)
                ->description('Total number of registered customers')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Points Issued', $totalPointsIssued)
                ->description('Total loyalty points given to customers')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Total Points Used', $totalPointsUsed)
                ->description('Total points redeemed by customers')
                ->icon('heroicon-o-currency-dollar')
                ->color('danger'),
        ];
    }
}
