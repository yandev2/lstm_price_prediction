<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class CustomLoginPage extends Login
{
    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'custom-auth-bg',
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('email anda tidak valid'),
            'data.password' => __('password anda tidak valid'),
        ]);
    }
    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->color('primary')
            ->label('Login')
            ->submit('authenticate');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('Password'))
            ->hint(new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::auth/pages/login.actions.request_password_reset.label\') }}</x-filament::link>')))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete(autocomplete: 'current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function authenticate(): LoginResponse
    {
        $response = parent::authenticate();
        session(['show_alert_modal' => true]);
        return $response;
    }
}
