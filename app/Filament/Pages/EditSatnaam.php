<?php

namespace App\Filament\Pages;

use App\Models\Bhagat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class EditSatnaam extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'filament.pages.edit-satnaam';
    
    protected static bool $shouldRegisterNavigation = false; // Hide from sidebar
    
    public ?Bhagat $record = null;
    
    public ?array $data = [];
    
    public bool $canEditSatnaam = false;

    public function mount(): void
    {
        $recordId = request()->query('record');
        
        if (!$recordId) {
            abort(404, 'Record not found');
        }
        
        $this->record = Bhagat::findOrFail($recordId);
        
        // Check if all 4 attendance dates are filled
        $this->canEditSatnaam = !empty($this->record->attendance_date_1) 
            && !empty($this->record->attendance_date_2)
            && !empty($this->record->attendance_date_3)
            && !empty($this->record->attendance_date_4);
        
        $this->form->fill([
            'user_id' => $this->record->user_id,
            'first_name' => $this->record->first_name,
            'last_name' => $this->record->last_name,
            'guardian_type' => $this->record->guardian_type,
            'guardian_name' => $this->record->guardian_name,
            'mobile_no' => $this->record->mobile_no,
            'whatsapp_no' => $this->record->whatsapp_no,
            'first_mantra_date' => $this->record->first_mantra_date,
            'attendance_date_1' => $this->record->attendance_date_1,
            'attendance_date_2' => $this->record->attendance_date_2,
            'attendance_date_3' => $this->record->attendance_date_3,
            'attendance_date_4' => $this->record->attendance_date_4,
            'satnaam_mantra_date' => $this->record->satnaam_mantra_date,
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

                // Attendance Progress Section
                Section::make('Attendance Progress')
                    ->description('Attendance dates (read-only)')
                    ->schema([
                        DatePicker::make('first_mantra_date')
                            ->label('First Mantra Date')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_1')
                            ->label('Attendance Date 1')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_2')
                            ->label('Attendance Date 2')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_3')
                            ->label('Attendance Date 3')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_4')
                            ->label('Attendance Date 4')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Satnaam Update Section
                Section::make('Satnaam Mantra Date')
                    ->description($this->canEditSatnaam 
                        ? 'Update Satnaam Mantra Date' 
                        : 'All 4 attendance dates must be completed before updating Satnaam date')
                    ->schema([
                        DatePicker::make('satnaam_mantra_date')
                            ->label('Satnaam Mantra Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(!$this->canEditSatnaam)
                            ->helperText($this->canEditSatnaam 
                                ? 'Once set, attendance dates will be locked' 
                                : 'Complete all 4 attendance dates first')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.satnaam-update') => 'Satnaam Update',
            '' => 'Edit',
        ];
    }

    public function save(): void
    {
        if (!$this->canEditSatnaam) {
            Notification::make()
                ->title('Cannot update Satnaam date')
                ->body('All 4 attendance dates must be completed first.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();
        
        $this->record->update([
            'satnaam_mantra_date' => $data['satnaam_mantra_date'] ?? null,
        ]);

        Notification::make()
            ->title('Satnaam date updated successfully')
            ->body('Attendance dates are now locked and cannot be modified.')
            ->success()
            ->send();
            
        $this->redirect(route('filament.admin.pages.satnaam-update'));
    }

    public function getTitle(): string
    {
        return 'Edit Satnaam - ' . $this->record->user_id;
    }
}
