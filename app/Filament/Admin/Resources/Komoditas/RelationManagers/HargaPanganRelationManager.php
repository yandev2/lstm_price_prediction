<?php

namespace App\Filament\Admin\Resources\Komoditas\RelationManagers;

use Carbon\Carbon;
use Devletes\FilamentTimelineView\Tables\Columns\TimelineEntry;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class HargaPanganRelationManager extends RelationManager
{
    protected static string $relationship = 'hargaPangan';


    public function isReadOnly(): bool
    {
        return true;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
     
        return $pageClass === \App\Filament\Admin\Resources\Komoditas\Pages\ViewKomoditas::class;
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('harga')
            ->heading('')
            ->description(fn() => 'History harga untuk komoditas ' . $this->getOwnerRecord()->nama_komoditas . ' di berbagai pasar')
            ->asTimeline()
            ->asDoubleSidedTimeline()
            ->groups([Group::make('tanggal')->date()])
            ->columns([
                TimelineEntry::make()
                    ->title(fn($record) => Number::currency($record->harga, in: 'IDR', locale: 'id') . " /{$this->getOwnerRecord()->satuan}")
                    ->content(function ($record) {
                        $tanggal = Carbon::parse($record->tanggal)->translatedFormat('l, d F Y');
                        $content = "harga di pasar {$record->pasar->nama_pasar} pada {$tanggal}";
                        return $content;
                    })
                    ->image('hero_url')
                    ->author('createdBy.name',  fn($record) => $record->createdBy?->getFilamentAvatarUrl())
                    ->time('published_at'),
            ]);
    }
}
