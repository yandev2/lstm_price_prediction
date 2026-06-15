<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WelcomeBanner extends Widget
{
    protected string $view = 'filament.admin.widgets.welcome-banner';
    protected int | string | array $columnSpan = 'full';

    public function getUserData(): array
    {
        $user = Auth::user();
        $avatarUrl = null;
        if (!empty($user->avatar)) {
            $avatarUrl = Storage::disk('public')->exists($user->avatar)
                ? Storage::url($user->avatar)
                : $user->avatar; 
        }
        return [
            'name' => $user->name,
            'role' => $user->getRoleNames()->first() ?? 'User', // Asumsi menggunakan Spatie Permission
            'date' => Carbon::now()->translatedFormat('l, d F Y'),
            'time_greeting' => $this->getTimeGreeting(),
            'avatar' => $avatarUrl,
        ];
    }

    protected function getTimeGreeting(): string
    {
        $hour = Carbon::now()->hour;
        if ($hour < 12) return 'Selamat Pagi';
        if ($hour < 15) return 'Selamat Siang';
        if ($hour < 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }
}
