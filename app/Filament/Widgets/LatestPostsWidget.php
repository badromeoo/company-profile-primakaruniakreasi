<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;

class LatestPostsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('5 post terbaru')
            ->query(Post::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PostStatus $state): string => Str::headline($state->value))
                    ->color(fn (PostStatus $state): string => match ($state) {
                        PostStatus::Published => 'success',
                        PostStatus::Scheduled => 'warning',
                        PostStatus::Draft => 'gray',
                    }),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('—'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->since()
                    ->label('Created'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
