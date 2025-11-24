<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use App\Models\Bhagat;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationGroup = 'Finance & Donations';
    protected static ?string $navigationLabel = 'Donation Receipts';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Receipt Details')
                    ->schema([
                        TextInput::make('receipt_no')
                            ->label('Receipt No')
                            ->default(fn () => 'DON-' . str_pad(\App\Models\Donation::count() + 1, 5, '0', STR_PAD_LEFT))
                            ->readOnly()
                            ->required(),

                        DatePicker::make('donation_date')
                            ->label('Date')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                Section::make('Donor Information')
                    ->schema([
                        Select::make('bhagat_id')
                            ->label('Registered Bhagat')
                            ->options(Bhagat::all()->mapWithKeys(function ($bhagat) {
                                return [$bhagat->id => "{$bhagat->first_name} {$bhagat->last_name} ({$bhagat->user_id}) - {$bhagat->mobile_no}"];
                            }))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, $state) => 
                                $state ? $set('donor_name', \App\Models\Bhagat::find($state)?->first_name . ' ' . \App\Models\Bhagat::find($state)?->last_name) : null
                            ),

                        TextInput::make('donor_name')
                            ->label('Donor Name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Payment Details')
                    ->schema([
                        TextInput::make('amount')->label('Amount')->numeric()->prefix('₹')->required(),
                        Select::make('payment_mode')->label('Payment Mode')
                            ->options(['Cash' => 'Cash', 'UPI' => 'UPI', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer'])
                            ->default('Cash')->required(),
                        Select::make('purpose')->label('Purpose')
                            ->options(['General Fund' => 'General Fund', 'Bhandara' => 'Bhandara (Food)', 'Construction' => 'Construction', 'Education' => 'Education / Books', 'Other' => 'Other'])
                            ->required(),
                        Textarea::make('remarks')->label('Remarks')->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_no')->label('Receipt #')->searchable()->sortable(),
                TextColumn::make('donation_date')->date()->sortable(),
                TextColumn::make('donor_name')->label('Donor')->searchable(),
                TextColumn::make('amount')->money('INR')->sortable()->weight('bold'),
                TextColumn::make('payment_mode')->badge(),
                TextColumn::make('purpose')->searchable(),
                TextColumn::make('createdBy.name')->label('Received By')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([ EditAction::make(), DeleteAction::make() ])
            ->bulkActions([ BulkActionGroup::make([ DeleteBulkAction::make() ]) ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
