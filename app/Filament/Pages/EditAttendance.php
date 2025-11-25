<?php

namespace App\Filament\Pages;

use App\Models\Bhagat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class EditAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'filament.pages.edit-attendance';
    
    protected static bool $shouldRegisterNavigation = false; // Hide from sidebar
    
    public ?Bhagat $record = null;
    
    public ?array $data = [];

    public function mount(int $record): void
    {
        $this->record = Bhagat::findOrFail($record);
        
        $this->form->fill([
            'user_id' => $this->record->user_id,
            'first_name' => $this->record->first_name,
            'last_name' => $this->record->last_name,
            'guardian_type' => $this->record->guardian_type,
            'guardian_name' => $this->record->guardian_name,
            'mobile_no' => $this->record->mobile_no,
            'whatsapp_no' => $this->record->whatsapp_no,
            'first_mantra_date' => $this->record->first_mantra_date,
            'satnaam_mantra_date' => $this->record->satnaam_mantra_date,
            'sarnaam_mantra_date' => $this->record->sarnaam_mantra_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-Only Bhagat Information Section
                Section::make('Bhagat Information')
                    ->description('Basic information (read-only)')
                    ->schema([
                        TextInput::make('user_id')
                            ->label('Form No')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('guardian_type')
                            ->label('Relationship')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('guardian_name')
                            ->label('Guardian Name')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('mobile_no')
                            ->label('Mobile No')
                            ->disabled()
                            ->dehydrated(false),
                            
                        TextInput::make('whatsapp_no')
                            ->label('WhatsApp No')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

                // Attendance Update Section
                Section::make('Attendance Update')
                    ->description('Update mantra attendance dates')
                    ->schema([
                        DatePicker::make('first_mantra_date')
                            ->label('First Mantra Date')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('satnaam_mantra_date')
                            ->label('Satnaam Mantra Date (Attendance Date 1)')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => !$get('first_mantra_date'))
                            ->columnSpanFull(),
                            
                        DatePicker::make('sarnaam_mantra_date')
                            ->label('Sarnaam Mantra Date (Attendance Date 2)')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => !$get('satnaam_mantra_date'))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $this->record->update([
            'first_mantra_date' => $data['first_mantra_date'] ?? null,
            'satnaam_mantra_date' => $data['satnaam_mantra_date'] ?? null,
            'sarnaam_mantra_date' => $data['sarnaam_mantra_date'] ?? null,
        ]);

        Notification::make()
            ->title('Attendance updated successfully')
            ->success()
            ->send();
            
        $this->redirect(route('filament.admin.pages.attendance'));
    }

    public function getTitle(): string
    {
        return 'Edit Attendance - ' . $this->record->user_id;
    }
}
