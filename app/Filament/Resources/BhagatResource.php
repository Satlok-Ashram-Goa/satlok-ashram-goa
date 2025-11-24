<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BhagatResource\Pages;
use App\Models\Bhagat;
use App\Models\District;
use App\Models\State;
use App\Models\Zilla;
use App\Models\Pincode; 

use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;


class BhagatResource extends Resource
{
    protected static ?string $model = Bhagat::class;
    protected static ?string $navigationGroup = 'Bhagat Database';
    protected static ?string $navigationLabel = 'Register New';
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- SECTION 1: IDENTITY & CONTACT ---
                Section::make('Identity & Contact')
                    ->description('Personal, Guardian, and Contact information.')
                    ->schema([
                        // Row 1.1: Manual Entry Toggle
                        Toggle::make('manual_id_entry')
                            ->label(fn (Get $get) => 
                                $get('manual_id_entry') 
                                ? 'Uploading Old Data (Manual Form No)' 
                                : 'Uploading New Form (Auto-ID Generated)' 
                            )
                            ->onIcon('heroicon-m-document-arrow-up')
                            ->offIcon('heroicon-m-document-plus')
                            ->live()
                            ->columnSpanFull(),
                        
                        // --- ROW 2: PRIMARY ID FIELDS (3 COLUMNS) ---
                        Group::make([
                            // State Selection (Must be driven by Pincode in new version)
                            Select::make('current_state_id')
                                ->label('State')
                                ->relationship('currentState', 'name')
                                ->searchable()
                                ->live() 
                                ->required()
                                ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                    $set('current_district_id', null);
                                    $set('current_zilla_id', null);
                                    
                                    // ID Generation Logic
                                    if (!$get('manual_id_entry') && $state) {
                                        $stateRecord = State::find($state);
                                        if ($stateRecord) {
                                            $stateCode = $stateRecord->code;
                                            $nextId = Bhagat::count() + 1;
                                            $formNo = str_pad($nextId, 8, '0', STR_PAD_LEFT);
                                            $set('user_id', "{$stateCode}-{$formNo}");
                                        }
                                    }
                                })
                                ->columnSpan(1),
                            
                            // Manual Form Number
                            TextInput::make('manual_form_no')
                                ->label('Form No (8 Digits)')
                                ->numeric()
                                ->length(8)
                                ->debounce('1500ms') // Enforced 1500ms debounce
                                ->rules(['digits:8'])
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    if ($get('manual_id_entry') && $get('current_state_id') && $get('manual_form_no')) {
                                        $stateRecord = State::find($get('current_state_id'));
                                        if ($stateRecord) {
                                            $stateCode = $stateRecord->code;
                                            $formNo = str_pad($get('manual_form_no'), 8, '0', STR_PAD_LEFT);
                                            $set('user_id', "{$stateCode}-{$formNo}");
                                        }
                                    }
                                })
                                ->required(fn (Get $get) => $get('manual_id_entry'))
                                ->hidden(fn (Get $get) => !$get('manual_id_entry')), 
                                
                            // User ID
                            TextInput::make('user_id')
                                ->label('User ID')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->readOnly(fn (Get $get) => !$get('manual_id_entry')) 
                                ->dehydrated(), 

                        ])->columns(3)->columnSpanFull(), 
                        
                        // --- ROW 3: PERSONAL DETAILS ---
                        TextInput::make('first_name')->label('First Name')->required(),
                        TextInput::make('last_name')->label('Last Name')->required(),
                        DatePicker::make('date_of_birth')->label('Date of Birth')->required(),

                        // Guardian Info
                        Select::make('guardian_type')
                            ->label('Relationship')
                            ->options(['S/o' => 'Son of', 'W/o' => 'Wife of', 'D/o' => 'Daughter of'])
                            ->required(),
                        
                        TextInput::make('guardian_name')->label('Guardian Name')->required()->columnSpan(2),

                        // Contact & Aadhar
                        TextInput::make('mobile_no')->label('Mobile No')->tel()->required()->unique(ignoreRecord: true),
                        TextInput::make('whatsapp_no')->label('WhatsApp No')->tel(),
                        TextInput::make('email_id')->label('Email Id')->email(),
                        
                        Group::make([
                            TextInput::make('aadhar_card_no')
                                ->label('Aadhar Card No')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->numeric()
                                ->length(12)
                                ->rules(['digits:12'])
                                ->columnSpanFull(),
                            
                            FileUpload::make('aadhar_front_path')->label('Aadhar Front Image')->image()->directory('aadhar-fronts')->disk('public')->maxSize(2048)->columnSpan(1), 
                            FileUpload::make('aadhar_rear_path')->label('Aadhar Back Image')->image()->directory('aadhar-rears')->disk('public')->maxSize(2048)->columnSpan(1), 
                        ])->columnSpanFull()->columns(2) 
                          ->label('Aadhar Details'),
                          
                    ])->columns(3), 

                // --- SECTION 2: CURRENT ADDRESS (Pincode Lookup - Flat 4-Column Layout) ---
                Section::make('Current Address')
                    ->columns(4) // Set to 4 columns for clean 4-field layout
                    ->schema([
                        TextInput::make('current_addr_line_1')->label('Current Address Line 1')->required()->columnSpanFull(),
                        TextInput::make('current_addr_line_2')->label('Current Address Line 2')->columnSpanFull(),

                        // --- Address Fields 4-Column Row ---
                        Select::make('current_pincode')
                            ->label('Pincode')
                            ->options(Pincode::pluck('pincode', 'pincode'))
                            ->searchable()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Clear dependent fields first
                                $set('current_district_id', null);
                                $set('current_zilla_id', null);
                                $set('current_state_id', null); 
                                
                                if ($state) {
                                    $pincodeRecord = Pincode::where('pincode', $state)->with('zilla.district.state')->first();
                                    if ($pincodeRecord && $pincodeRecord->zilla) {
                                        // Set fields based on lookup result
                                        $set('current_zilla_id', $pincodeRecord->zilla->id);
                                        $set('current_district_id', $pincodeRecord->zilla->district_id);
                                        $set('current_state_id', $pincodeRecord->zilla->district->state_id);
                                    }
                                }
                            }),

                        // Dependent Display Fields (Auto-Populated by Pincode)
                        Select::make('current_state_id')->label('State')->relationship('currentState', 'name')->required(),
                        Select::make('current_district_id')->label('District')->relationship('currentDistrict', 'name')->required(),
                        Select::make('current_zilla_id')->label('Zilla')->relationship('currentZilla', 'name')->required(),

                    ]), // End of Section 2

                // --- SECTION 3: PERMANENT ADDRESS (Pincode Lookup - Flat 4-Column Layout) ---
                Section::make('Permanent Address')
                    ->columns(4) // Set to 4 columns for clean 4-field layout
                    ->schema([
                        Toggle::make('same_as_current')
                            ->label('Permanent Address is same as Current')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        
                        Group::make([
                            // Address Lines (Full Width)
                            TextInput::make('perm_addr_line_1')
                                ->label('Permanent Address Line 1')
                                ->required(fn (Get $get) => !$get('same_as_current'))
                                ->columnSpanFull(),
                            TextInput::make('perm_addr_line_2')
                                ->label('Permanent Address Line 2')
                                ->required(fn (Get $get) => !$get('same_as_current'))
                                ->columnSpanFull(),
                            
                            // Pincode Lookup (Col 1 of the 4-column block)
                            Select::make('perm_pincode')
                                ->label('Pincode')
                                ->options(Pincode::pluck('pincode', 'pincode'))
                                ->searchable()
                                ->live()
                                ->required(fn (Get $get) => !$get('same_as_current'))
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $set('perm_state_id', null);
                                    $set('perm_district_id', null);
                                    $set('perm_zilla_id', null);
                                    
                                    if ($state) {
                                        $pincodeRecord = Pincode::where('pincode', $state)->with('zilla.district.state')->first();
                                        if ($pincodeRecord && $pincodeRecord->zilla) {
                                            $set('perm_zilla_id', $pincodeRecord->zilla->id);
                                            $set('perm_district_id', $pincodeRecord->zilla->district_id);
                                            $set('perm_state_id', $pincodeRecord->zilla->district->state_id);
                                        }
                                    }
                                }), 
                            
                            // Dependent Display Fields (Cols 2, 3, 4)
                            Select::make('perm_state_id')
                                ->label('State')
                                ->relationship('permState', 'name')
                                ->required(fn (Get $get) => !$get('same_as_current')),
                            
                            Select::make('perm_district_id')
                                ->label('District')
                                ->relationship('permDistrict', 'name')
                                ->required(fn (Get $get) => !$get('same_as_current')),
                            
                            Select::make('perm_zilla_id')
                                ->label('Zilla')
                                ->relationship('permZilla', 'name')
                                ->required(fn (Get $get) => !$get('same_as_current')),

                        ])->columns(4) // This Group now contains the 4 address fields using 4 columns
                        ->columnSpanFull() // This group must span the 4 columns of the parent section
                        ->hidden(fn (Get $get) => $get('same_as_current')),
                    ]), // End of Section 3

                // --- NEW SECTION 4 (formerly 5): DOCUMENT UPLOADS ---
                Section::make('Document Uploads')
                    ->description('Max size 2MB, JPEG/PNG only.')
                    ->schema([
                        FileUpload::make('photo_path')->label('Photo Upload')->image()->directory('bhagat-photos')->disk('public')->maxSize(2048)->required()->columnSpanFull(),
                    ])->columns(1),

                // --- NEW SECTION 5 (formerly 4): MANTRA DATES & STATUS (Updated Logic) ---
                Section::make('Mantra Dates & Status')
                    ->columns(3) // Ensure 3-column layout
                    ->schema([
                        // Row 1: First Mantra Date (Only 1 field, takes 1 column)
                        DatePicker::make('first_mantra_date')->label('First Mantra Date')->columnSpan(1),

                        // Row 2: Attendance Dates (Grouped into 3 columns, will wrap onto two lines in the 3-column section)
                        Group::make([
                            DatePicker::make('attendance_date_1')->label('Attendance Date 1')->required(),
                            DatePicker::make('attendance_date_2')->label('Attendance Date 2')->required(),
                            DatePicker::make('attendance_date_3')->label('Attendance Date 3')->required(),
                            DatePicker::make('attendance_date_4')->label('Attendance Date 4')->required(),
                        ])->columns(3)->columnSpanFull()->label('Required Attendance Dates'),

                        // Row 3: Satnaam & Sarnaam Mantra Dates & Status
                        DatePicker::make('satnaam_mantra_date')
                            ->label('Satnaam Mantra Date')
                            ->required()
                            ->disabled(fn (Get $get) => !(
                                $get('attendance_date_1') && 
                                $get('attendance_date_2') && 
                                $get('attendance_date_3') && 
                                $get('attendance_date_4')
                            ))
                            ->helperText('Requires 4 attendance dates to be set.')
                            ->live()
                            ->columnSpan(1),
                        
                        DatePicker::make('sarnaam_mantra_date')
                            ->label('Sarnaam Mantra Date')
                            ->required()
                            ->disabled(fn (Get $get) => !$get('satnaam_mantra_date'))
                            ->helperText('Requires Satnaam Mantra Date to be set.')
                            ->columnSpan(1), // Takes 1 column
                        
                        // NEW POSITION FOR STATUS (Replaces the Spacer/Placeholder)
                        Select::make('status')
                            ->options([
                                'Active' => 'Active',
                                'Blacklisted' => 'Blacklisted',
                                'Death' => 'Death',
                                'Not Interested' => 'Not Interested',
                            ])
                            ->default('Active')
                            ->required()
                            ->live()
                            ->columnSpan(1), // Takes 1 column (Completes Row 3)

                        // Row 4: Conditional Upload (Now spans the entire next row)
                        FileUpload::make('yellow_notice_path')
                            ->label('Upload Yellow Notice')
                            ->image()
                            ->directory('yellow-notices')
                            ->disk('public')
                            ->maxSize(5120) 
                            ->required(fn (Get $get) => $get('status') === 'Blacklisted')
                            ->visible(fn (Get $get) => $get('status') === 'Blacklisted')
                            ->columnSpanFull(), // Takes all 3 columns (New Row 4)
                        
                    ]), 
            ]);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (List View)
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')->label('User Id')->searchable()->sortable(),
                TextColumn::make('first_name')->searchable()->sortable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('mobile_no')->label('Mobile No')->searchable(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Blacklisted' => 'danger',
                        'Death' => 'warning',
                        'Not Interested' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(), 
                DeleteAction::make(), 
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBhagats::route('/'),
            'create' => Pages\CreateBhagat::route('/create'),
            'edit' => Pages\EditBhagat::route('/{record}/edit'),
        ];
    }
}
