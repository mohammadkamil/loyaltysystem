<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Customer Details')
                    ->description('Select a customer for this transaction')
                    ->schema([
                        Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'name') // Fetch customer names
                        ->searchable()
                        ->preload()
                        ->default(fn ($livewire) => $livewire->ownerRecord->id) // Auto-select the related customer
                        ->disabled(), // Prevent changing the customer manually
                    ])
                    ->collapsible(), // Makes it collapsible

                Section::make('Transaction Details')
                    ->description('Enter the transaction type and points')
                    ->schema([
                        Select::make('type')
                            ->label('Transaction Type')
                            ->options([
                                'add' => 'Add Points',
                                'reduce' => 'Reduce Points',
                            ])
                            ->required(),

                        TextInput::make('points')
                            ->label('Points')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter points'),
                    ])
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer_id')
            ->columns([
                Tables\Columns\TextColumn::make('customer.name') // 👈 This will fetch the related customer's name
                    ->label('Customer Name') // 👈 Set a proper label
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Point Transaction') // Optional: Set a heading
                    ->form(fn($record) => [
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(), // Prevent changing customer when editing

                        Select::make('type')
                            ->label('Transaction Type')
                            ->options([
                                'add' => 'Add Points',
                                'reduce' => 'Reduce Points',
                            ])
                            ->required(),

                        TextInput::make('points')
                            ->label('Points')
                            ->required()
                            ->numeric(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
