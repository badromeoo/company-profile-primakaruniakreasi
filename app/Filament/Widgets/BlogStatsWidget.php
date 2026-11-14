<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Published', Post::query()->where('status', PostStatus::Published->value)->count())
                ->description('Post tayang')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make('Scheduled', Post::query()->where('status', PostStatus::Scheduled->value)->count())
                ->description('Post terjadwal')
                ->color('warning'),
            Stat::make('Drafts', Post::query()->where('status', PostStatus::Draft->value)->count())
                ->description('Post draf')
                ->color('gray'),
        ];
    }
}
