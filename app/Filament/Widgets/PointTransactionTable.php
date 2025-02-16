<?php

namespace App\Filament\Widgets;

use App\Models\PointTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PointTransactionTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PointTransaction::query()
                    ->whereHas('customer', function (Builder $query) {
                        $query->where('business_id', Auth::id()); // Show only current user's customers
                    })
                    ->latest() // Order by latest transaction
                    ->limit(10) // Only show the latest 10
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('points')
                    ->label('Points')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Transaction Type')
                    ->badge()
                    ->colors([
                        'success' => 'add',
                        'danger' => 'deduct',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
