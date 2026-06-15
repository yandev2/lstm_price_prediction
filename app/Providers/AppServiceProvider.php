<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use Illuminate\Support\ServiceProvider;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       // static::ssl();
    }

    private function ssl()
    {
        URL::macro(
            'alternateHasCorrectSignature',
            function (Request $request, $absolute = true, array $ignoreQuery = []) {
                $ignoreQuery[] = 'signature';

                $absoluteUrl = url($request->path());
                $url = $absolute ? $absoluteUrl : '/' . $request->path();

                $queryString = collect(explode('&', (string) $request
                    ->server->get('QUERY_STRING')))
                    ->reject(fn($parameter) => in_array(Str::before($parameter, '='), $ignoreQuery))
                    ->join('&');

                $original = rtrim($url . '?' . $queryString, '?');

                $key = config('app.key');

                if (empty($key)) {
                    throw new \RuntimeException('Application key is not set.');
                }

                $signature = hash_hmac('sha256', $original, $key);
                return hash_equals($signature, (string) $request->query('signature', ''));
            }
        );

        URL::macro('alternateHasValidSignature', function (Request $request, $absolute = true, array $ignoreQuery = []) {
            return URL::alternateHasCorrectSignature($request, $absolute, $ignoreQuery)
                && URL::signatureHasNotExpired($request);
        });

        Request::macro('hasValidSignature', function ($absolute = true, array $ignoreQuery = []) {
            return URL::alternateHasValidSignature($this, $absolute, $ignoreQuery);
        });
    }

    /**
     */
    private function bootSSL()
    {
        if ($this->app->environment('production')) {
          //  URL::forceScheme('https');
        }
    }
    public function boot(): void
    {
        static::bootSSL();
        static::styleApp();
        static::observer();
        Gate::policy(Activity::class, ActivityPolicy::class);
    }

    private static function observer(): void
    {
        \App\Models\EksportHistory::observe(\App\Observers\EksportHistoryObserver::class);
        \App\Models\HargaPangan::observe(\App\Observers\HargaPanganObserver::class);
        \App\Models\ModelAi::observe(\App\Observers\ModelAiObserver::class);
    }

    private static function styleApp(): void
    {

        FilamentTimezone::set(config('app.timezone'));
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action
                ->label('Tambah Data')
                ->color('info')
                ->icon(Heroicon::PlusCircle)
                ->modalWidth(Width::Large)
                ->modalIcon(Heroicon::PlusCircle)
                ->successNotificationTitle("Data telah berhasil disimpan")
                ->failureNotificationTitle("Terjadi kesalahan saat menyimpan data")
                ->modalHeading('Tambah Data');
        });

        EditAction::configureUsing(function (EditAction $action): void {
            $action
                ->color('warning')
                ->button()
                ->authorize(true)
                ->icon(Heroicon::PencilSquare)
                ->modalWidth(Width::Large)
                ->modalIcon(Heroicon::PencilSquare)
                ->successNotificationTitle("Data telah berhasil diperbarui")
                ->failureNotificationTitle("Terjadi kesalahan saat memperbarui data")
                ->modalHeading('Edit Data');
        });

        ViewAction::configureUsing(function (ViewAction $action): void {
            $action
                ->color('gray')
                ->button()
                ->authorize(true)
                ->icon(Heroicon::Eye)
                ->modalWidth(Width::Large)
                ->modalIcon(Heroicon::Eye)
                ->modalHeading('Detail Data');
        });

        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action
                ->color('danger')
                ->button()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation()
                ->modalHeading('KONFIRMASI')
                ->modalWidth(Width::Medium)
                ->modalDescription("konfirmasi untuk menghapus data")
                ->successNotificationTitle("Data telah berhasil dihapus")
                ->failureNotificationTitle("Terjadi kesalahan saat menghapus data")
            ;
        });

        RestoreAction::configureUsing(function (RestoreAction $action): void {
            $action
                ->color('gray')
                ->button()
                ->icon(Heroicon::ArrowPath)
                ->requiresConfirmation()
                ->modalHeading('KONFIRMASI')
                ->modalWidth(Width::Medium)
                ->modalDescription("konfirmasi untuk memulihkan data")
                ->successNotificationTitle("Data telah berhasil dipulihkan")
                ->failureNotificationTitle("Terjadi kesalahan saat memulihkan data")
            ;
        });

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action): void {
            $action
                ->color('danger')
                ->button()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation()
                ->modalHeading('KONFIRMASI')
                ->modalWidth(Width::Medium)
                ->modalDescription('konfirmasi untuk menghapus data yang dipilih')
                ->successNotificationTitle("Data yang dipilih telah berhasil dihapus")
                ->failureNotificationTitle("Terjadi kesalahan saat menghapus data yang dipilih")
            ;
        });

        RestoreBulkAction::configureUsing(function (RestoreBulkAction $action): void {
            $action
                ->color('gray')
                ->button()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation()
                ->modalHeading('KONFIRMASI')
                ->modalWidth(Width::Medium)
                ->modalDescription('konfirmasi untuk memulihkan data yang dipilih')
                ->successNotificationTitle("Data yang dipilih telah berhasil dipulihkan")
                ->failureNotificationTitle("Terjadi kesalahan saat memulihkan data yang dipilih")
            ;
        });

        ForceDeleteBulkAction::configureUsing(function (ForceDeleteBulkAction $action): void {
            $action
                ->color('danger')
                ->button()
                ->icon(Heroicon::Trash)
                ->requiresConfirmation()
                ->modalHeading('KONFIRMASI')
                ->modalWidth(Width::Medium)
                ->modalDescription('konfirmasi untuk menghapus permanen data yang dipilih')
                ->successNotificationTitle("Data yang dipilih telah berhasil dihapus permanen")
                ->failureNotificationTitle("Terjadi kesalahan saat menghapus permanen data yang dipilih")
            ;
        });

        TrashedFilter::configureUsing(function (TrashedFilter $action) {
            $action
                ->columnSpanFull()
                ->options([
                    'only' => 'Hanya Sampah',
                ])
                ->native(false);
        });

        ForceDeleteAction::configureUsing(function (ForceDeleteAction $action) {
            $action
                ->button()
                ->successNotificationTitle("Data telah berhasil dihapus permanen")
                ->failureNotificationTitle("Terjadi kesalahan saat menghapus data");
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->groupRecordsTriggerAction(
                    fn(Action $action) => $action
                        ->button()
                        ->color('primary')
                        ->label('Group'),
                )
                ->defaultSort('created_at', 'desc')
                ->selectable()
                ->emptyStateHeading('TIDAK ADA DATA')
                ->emptyStateDescription('belum ada data ditambahkan')
                ->emptyStateIcon(HeroIcon::FolderOpen)
                ->filtersFormWidth('2xl')
                ->filtersFormColumns(3)
                ->defaultPaginationPageOption(5)

                ->extremePaginationLinks()
                ->paginated([5, 10, 25, 50, 100])
                ->paginationMode(PaginationMode::Default)
                ->headerActionsPosition(HeaderActionsPosition::Bottom)
                ->filtersLayout(FiltersLayout::Modal)
                ->filtersTriggerAction(
                    fn(Action $action) => $action
                        ->button()
                        ->badgeColor('info')
                        ->color('primary')
                        ->label('Filter'),
                )
                ->filtersApplyAction(
                    fn(Action $action) => $action
                        ->badge()
                        ->button()
                        ->color('info')
                        ->label('Terapkan Filter')
                );
        });


        Select::configureUsing(function (Select $select): void {
            $select
                ->preload()
                ->placeholder('')
                ->searchable()
                ->native(false);
        });

        DatePicker::configureUsing(function (DatePicker $datePicker): void {
            $datePicker
                ->prefixIcon(Heroicon::Calendar)
                ->displayFormat('d M Y')
                ->native(false);
        });

        FileUpload::configureUsing(function (FileUpload $fileUpload) {
            $fileUpload
                ->disk('public')
                ->openable()
                ->downloadable()
                ->alignCenter()
                ->panelLayout('integrated')
                ->loadingIndicatorPosition('center')
                ->removeUploadedFileButtonPosition('right')
                ->uploadButtonPosition('center')
                ->uploadProgressIndicatorPosition('center');
        });

        SelectFilter::configureUsing(function (SelectFilter $select) {
            $select
                ->preload()
                ->searchable()
                ->native(false);
        });
    }
}
