<?php

namespace App\Filament\Pages;

use App\Models\Bhagat;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\Builder;

class SarnaamUpdate extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static string $view = 'filament.pages.sarnaam-update';
    
    protected static ?string $navigationGroup = 'Bhagat Database';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $navigationLabel = 'Sarnaam Update';

    public ?string $formNo = null;
    public ?string $mobileNo = null;
    public bool $hasSearched = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('formNo')
                    ->label('Form Number')
                    ->placeholder('e.g., HR-00031622')
                    ->maxLength(255),
                    
                TextInput::make('mobileNo')
                    ->label('Mobile Number')
                    ->placeholder('e.g., 9876543210')
                    ->tel()
                    ->maxLength(15),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                if (!$this->hasSearched) {
                    return Bhagat::query()->whereRaw('1 = 0');
                }

                $query = Bhagat::query();

                if ($this->formNo || $this->mobileNo) {
                    $query->where(function (Builder $q) {
                        if ($this->formNo) {
                            $q->orWhere('user_id', $this->formNo);
                        }
                        if ($this->mobileNo) {
                            $q->orWhere('mobile_no', $this->mobileNo)
                              ->orWhere('whatsapp_no', $this->mobileNo);
                        }
                    });
                }

                return $query;
            })
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                    
                TextColumn::make('user_id')
                    ->label('Form No')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('mobile_no')
                    ->label('Mobile')
                    ->searchable(),
                    
                TextColumn::make('sarnaam_mantra_date')
                    ->label('Sarnaam Date')
                    ->date('d/m/Y')
                    ->placeholder('Not set'),
            ])
            ->actions([
                TableAction::make('edit')
                    ->label('Edit Sarnaam')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Bhagat $record): string => route('filament.admin.pages.edit-sarnaam', ['record' => $record->id]))
            ])
            ->emptyStateHeading('No results found')
            ->emptyStateDescription('Try searching by Form Number or Mobile Number above.')
            ->emptyStateIcon('heroicon-o-magnifying-glass');
    }

    public function search(): void
    {
        $this->hasSearched = true;
        $this->resetTable();
    }

    public function clear(): void
    {
        $this->formNo = null;
        $this->mobileNo = null;
        $this->hasSearched = false;
        $this->form->fill();
        $this->resetTable();
    }
}
