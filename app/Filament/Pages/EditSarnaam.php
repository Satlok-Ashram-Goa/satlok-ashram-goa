<?php

namespace App\Filament\Pages;

use App\Models\Bhagat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class EditSarnaam extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static string $view = 'filament.pages.edit-sarnaam';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public ?Bhagat $record = null;
    
    public ?array $data = [];
    
    public bool $canEditSarnaam = false;
    public array $missingRequirements = [];

    public function mount(): void
    {
        $recordId = request()->query('record');
        
        if (!$recordId) {
            abort(404, 'Record not found');
        }
        
        $this->record = Bhagat::findOrFail($recordId);
        
        // Check requirements
        $this->missingRequirements = [];
        
        if (empty($this->record->attendance_date_1)) $this->missingRequirements[] = 'Attendance Date 1';
        if (empty($this->record->attendance_date_2)) $this->missingRequirements[] = 'Attendance Date 2';
        if (empty($this->record->attendance_date_3)) $this->missingRequirements[] = 'Attendance Date 3';
        if (empty($this->record->attendance_date_4)) $this->missingRequirements[] = 'Attendance Date 4';
        if (empty($this->record->satnaam_mantra_date)) $this->missingRequirements[] = 'Satnaam Mantra Date';
        
        $this->canEditSarnaam = empty($this->missingRequirements);
        
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
            'sarnaam_mantra_date' => $this->record->sarnaam_mantra_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-Only Bhagat Information
                Section::make('Bhagat Information')
                    ->description('Basic information (read-only)')
                    ->schema([
                        TextInput::make('user_id')->label('Form No')->disabled()->dehydrated(false),
                        TextInput::make('first_name')->label('First Name')->disabled()->dehydrated(false),
                        TextInput::make('last_name')->label('Last Name')->disabled()->dehydrated(false),
                        TextInput::make('guardian_type')->label('Relationship')->disabled()->dehydrated(false),
                        TextInput::make('guardian_name')->label('Guardian Name')->disabled()->dehydrated(false),
                        TextInput::make('mobile_no')->label('Mobile No')->disabled()->dehydrated(false),
                        TextInput::make('whatsapp_no')->label('WhatsApp No')->disabled()->dehydrated(false),
                    ])
                    ->columns(3),

                // Progress Section
                Section::make('Progress')
                    ->description('All dates (read-only)')
                    ->schema([
                        DatePicker::make('first_mantra_date')
                            ->label('First Mantra Date')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                        DatePicker::make('attendance_date_1')
                            ->label('Attendance Date 1')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                        DatePicker::make('attendance_date_2')
                            ->label('Attendance Date 2')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                        DatePicker::make('attendance_date_3')
                            ->label('Attendance Date 3')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                        DatePicker::make('attendance_date_4')
                            ->label('Attendance Date 4')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                        DatePicker::make('satnaam_mantra_date')
                            ->label('Satnaam Mantra Date')
                            ->disabled()->dehydrated(false)->native(false)->displayFormat('d/m/Y')->columnSpanFull(),
                    ])
                    ->columns(1),

                // Sarnaam Update Section
                Section::make('Sarnaam Mantra Date')
                    ->description($this->canEditSarnaam 
                        ? 'Update Sarnaam Mantra Date' 
                        : 'Complete all requirements before updating Sarnaam date')
                    ->schema([
                        DatePicker::make('sarnaam_mantra_date')
                            ->label('Sarnaam Mantra Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(!$this->canEditSarnaam)
                            ->helperText($this->canEditSarnaam 
                                ? 'Once set, all previous dates will be permanently locked' 
                                : 'Missing: ' . implode(', ', $this->missingRequirements))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.sarnaam-update') => 'Sarnaam Update',
            '' => 'Edit',
        ];
    }

    public function save(): void
    {
        if (!$this->canEditSarnaam) {
            Notification::make()
                ->title('Cannot update Sarnaam date')
                ->body('Missing requirements: ' . implode(', ', $this->missingRequirements))
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();
        
        $this->record->update([
            'sarnaam_mantra_date' => $data['sarnaam_mantra_date'] ?? null,
        ]);

        Notification::make()
            ->title('Sarnaam date updated successfully')
            ->body('All dates are now permanently locked and cannot be modified.')
            ->success()
            ->send();
            
        $this->redirect(route('filament.admin.pages.sarnaam-update'));
    }

    public function getTitle(): string
    {
        return 'Edit Sarnaam - ' . $this->record->user_id;
    }
}
