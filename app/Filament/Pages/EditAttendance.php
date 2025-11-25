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

    public function mount(): void
    {
        $recordId = request()->query('record');
        
        if (!$recordId) {
            abort(404, 'Record not found');
        }
        
        $this->record = Bhagat::findOrFail($recordId);
        
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
                    ->description('Update attendance dates (sequential entry)')
                    ->schema([
                        DatePicker::make('first_mantra_date')
                            ->label('First Mantra Date')
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('Read-only - managed in main Bhagat form')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_1')
                            ->label('Attendance Date 1')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_2')
                            ->label('Attendance Date 2')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => !$get('attendance_date_1'))
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_3')
                            ->label('Attendance Date 3')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => !$get('attendance_date_2'))
                            ->columnSpanFull(),
                            
                        DatePicker::make('attendance_date_4')
                            ->label('Attendance Date 4')
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => !$get('attendance_date_3'))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.attendance') => 'Attendance',
            '' => 'Edit',
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Only update attendance dates, not mantra dates
        $this->record->update([
            'attendance_date_1' => $data['attendance_date_1'] ?? null,
            'attendance_date_2' => $data['attendance_date_2'] ?? null,
            'attendance_date_3' => $data['attendance_date_3'] ?? null,
            'attendance_date_4' => $data['attendance_date_4'] ?? null,
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
