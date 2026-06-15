<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\CustomLoginPage;
use App\Filament\Admin\Pages\CustomProfilePage;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('')
            ->default()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'profile' => fn(Action $action) => $action
                    ->label(fn() => auth()->user()->name)
                    ->url(fn(): string => auth()->user()->exists() ? CustomProfilePage::getUrl() : '#')
                    ->icon(Heroicon::UserCircle)
            ])
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Blue,
                'success' => Color::Lime,
                'warning' => Color::Amber,
            ])
            ->spa(hasPrefetching: true)
            ->databaseTransactions()
            ->databaseNotifications()
            ->plugins([
                FilamentShieldPlugin::make()
                    ->registerNavigation(true)
                    ->navigationGroup('Management User'),
                GlobalSearchModalPlugin::make()
            ])
            ->login(CustomLoginPage::class)
            ->registration()
            ->passwordReset()
            ->sidebarCollapsibleOnDesktop()
            ->simplePageMaxContentWidth('md')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => new HtmlString('
                    <script>
                        // 1. Simpan posisi scroll tepat saat user mengklik menu (sebelum pindah)
                        document.addEventListener("livewire:navigating", () => {
                            const sidebar = document.querySelector(".fi-sidebar-nav");
                            if (sidebar) {
                                sessionStorage.setItem("sidebar-scroll", sidebar.scrollTop);
                            }
                        });
            
                        // 2. Kembalikan posisi scroll saat DOM baru dimuat
                        document.addEventListener("livewire:navigated", () => {
                            const sidebar = document.querySelector(".fi-sidebar-nav");
                            if (sidebar) {
                                const scrollPos = sessionStorage.getItem("sidebar-scroll");
                                if (scrollPos !== null) {
                                    // Gunakan requestAnimationFrame agar dieksekusi SEBELUM browser menggambar ulang layar (menghindari kedip)
                                    requestAnimationFrame(() => {
                                        sidebar.scrollTop = parseInt(scrollPos);
                                    });
                                }
                                
                                // 3. Simpan posisi saat di-scroll (untuk antisipasi jika user menekan F5/Refresh)
                                // Menggunakan onscroll agar tidak terjadi duplikasi event saat mode SPA berjalan
                                sidebar.onscroll = () => {
                                    sessionStorage.setItem("sidebar-scroll", sidebar.scrollTop);
                                };
                            }
                        });
                    </script>
                ')
            )
        ;
    }
}
