<?php

namespace App\Filament\Resources\DaanRecordResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('txn_id')
                    ->label('Txn ID')
                    ->default(function () {
                        $nextId = \App\Models\SevaPayment::max('id') + 1;
                        return 'T-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                    })
                    ->readOnly()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('txn_date')
                    ->label('Txn Date')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('payment_type')
                    ->options([
                        'Cash' => 'Cash',
                        'UPI' => 'UPI',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('₹'),
                Forms\Components\Select::make('collection_location')
                    ->options([
                        'Naamdaan Kendra' => 'Naamdaan Kendra',
                        'Satlok Ashram Dhavalpuri' => 'Satlok Ashram Dhavalpuri',
                        'Satlok Ashram Dhanadham' => 'Satlok Ashram Dhanadham',
                    ])
                    ->default('Naamdaan Kendra')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('txn_id')
            ->columns([
                Tables\Columns\TextColumn::make('txn_id')
                    ->label('Txn ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('txn_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collection_location')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Payment')
                    ->modalHeading('Create Payment')
                    ->after(function ($record) {
                        // Update Daan Record Status
                        $daanRecord = $record->daanRecord;
                        $totalPaid = $daanRecord->payments()->sum('amount');
                        $pledgeAmount = $daanRecord->original_amount;

                        if ($totalPaid >= $pledgeAmount) {
                            $daanRecord->update(['status' => 'Completed']);
                        } elseif ($totalPaid > 0) {
                            $daanRecord->update(['status' => 'In Progress']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
