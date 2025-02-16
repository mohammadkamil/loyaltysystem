<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\TransactionRelationManager;
use App\Models\Customer;
use App\Models\PointTransaction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Customer Details')
                    ->description('Fill in the customer’s personal information')
                    ->schema([
                        Grid::make(2) // Two-column layout
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name')
                                    ->columnSpanFull(),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter email'),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter phone number'),
                            ]),
                    ])->collapsible(),

                Section::make('Loyalty Points')
                    ->description('Manage customer’s loyalty points')
                    ->schema([

                        TextInput::make('total_points')
                            ->label('Initial Points')
                            ->numeric()
                            ->default(0)
                            ->helperText('Set an initial point balance for this customer')
                            ->columnSpanFull(),
                    ])->collapsible(),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('Add Points')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('points')
                            ->label('Points')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->action(function (array $data, Customer $record) {
                        // $record->increment('total_points', $data['points']);

                        PointTransaction::create([
                            'customer_id' => $record->id,
                            'points' => $data['points'],
                            'type' => 'add',
                        ]);

                        Notification::make()
                            ->title('Points Added')
                            ->body("Added {$data['points']} points to {$record->name}.")
                            ->success()
                            ->send();
                    }),

                Action::make('Deduct Points')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('points')
                            ->label('Points')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->action(function (array $data, Customer $record) {
                        if ($record->total_points < $data['points']) {
                            Notification::make()
                                ->title('Insufficient Points')
                                ->body("Cannot deduct more points than available.")
                                ->danger()
                                ->send();
                            return;
                        }

                        // $record->decrement('total_points', $data['points']);

                        PointTransaction::create([
                            'customer_id' => $record->id,
                            'points' => $data['points'],
                            'type' => 'reduce',
                        ]);

                        Notification::make()
                            ->title('Points Deducted')
                            ->body("Deducted {$data['points']} points from {$record->name}.")
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
