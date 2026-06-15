<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

class CustomProfilePage extends Page
{
    protected string $view = 'filament.admin.pages.custom-profile-page';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = "Profile";
    protected static ?string $pluralModelLabel = "Profile";
    protected static ?string $title = "Profile";

    public ?array $data = [];
    public ?User $record = null;

    public function getRecord(): ?User
    {
        return auth()->user();
    }

    public function mount(): void
    {
        $this->record = auth()->user();
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    protected function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->record($this->getRecord())
            ->components([
                Section::make()
                    ->heading('')
                    ->extraAttributes(['class' => 'form-section-custom'])
                    ->footerActions([])
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('avatar')
                            ->disk('public')
                            ->directory(fn() => 'user/profile')
                            ->imageEditorMode(2)
                            ->image()
                            ->maxWidth('100%')
                            ->panelAspectRatio('1:1')
                            ->circleCropper()
                            ->hiddenLabel(),

                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nama')
                                    ->prefixIconColor('info')
                                    ->prefixIcon(Heroicon::UserCircle),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->label('Email')
                                    ->prefixIconColor('info')
                                    ->prefixIcon(Heroicon::Envelope)
                                    ->validationAttribute('email')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Email ini sudah terdaftar',
                                        'required' => 'Email wajib diisi, tidak boleh kosong.',
                                    ]),

                                Fieldset::make('Password')
                                    ->schema([
                                        TextInput::make('old_password')
                                            ->password()
                                            ->columnSpanFull()
                                            ->label('Password Lama')
                                            ->revealable()
                                            ->currentPassword()
                                            ->dehydrated(false)
                                            ->validationMessages([
                                                'current_password' => 'Password lama yang Anda masukkan salah.',
                                            ]),

                                        TextInput::make('new_password')
                                            ->password()
                                            ->columnSpanFull()
                                            ->label('Password Baru')
                                            ->nullable()
                                            ->revealable()
                                            ->minLength(8)
                                            ->requiredWith('old_password')
                                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                            ->dehydrated(fn($state) => filled($state))
                                            ->validationMessages([
                                                'required_with' => 'Silakan isi password baru jika ingin mengganti password.',
                                                'min' => 'Password baru minimal harus 8 karakter.',
                                            ]),


                                    ])
                            ]),

                        Actions::make([
                            Action::make('save')
                                ->label('Simpan')
                                ->color('info')
                                ->action('save')
                        ])
                    ]),
            ]);
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            $record = $this->getRecord();

            if (!empty($data['new_password'])) {
                $data['password'] = $data['new_password'];
            }
            unset($data['new_password'], $data['current_password']);

            $record->fill($data);
            $record->save();

            Notification::make()
                ->success()
                ->title('Profil Berhasil Diperbarui')
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Gagal memperbarui profil')
                ->body($th->getMessage())
                ->send();
        }
    }
}
