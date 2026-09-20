<?php

namespace App\Filament\Pages;

use App\Models\Mr\Visit;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use UnitEnum;

class MrLiveOpsMap extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-americas';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep Reports';

    protected static ?string $navigationLabel = 'Live Ops Field Map';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Field Operations Live GPS Map & Activity Feed';

    protected string $view = 'filament.pages.mr-live-ops-map';

    public function getLatestVisitsProperty(): Collection
    {
        return Visit::with(['representative', 'contact.specialty', 'contact.classification', 'contact.city'])
            ->whereNotNull('checkin_lat')
            ->whereNotNull('checkin_lng')
            ->orderBy('checkin_at', 'desc')
            ->limit(50)
            ->get();
    }
}
