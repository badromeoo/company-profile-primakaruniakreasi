<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use App\Support\ImageOptimizer;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Post')
                    ->tabs([
                        Tab::make('Content')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(180)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    })
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(180)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Otomatis dari judul saat membuat post baru; bisa diubah manual.'),
                                Textarea::make('excerpt')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Ringkasan singkat yang tampil di kartu blog dan meta description fallback.')
                                    ->columnSpanFull(),
                                RichEditor::make('content')
                                    ->required()
                                    ->helperText('Konten disanitasi otomatis sebelum disimpan.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Media')
                            ->schema([
                                FileUpload::make('featured_image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['16:9'])
                                    ->extraAttributes(['class' => 'blog-media-upload'])
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->directory('posts')
                                    ->visibility('public')
                                    ->maxSize(3072)
                                    ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                                        $path = $component->saveUploadedFile($file);

                                        if (! is_string($path) || $path === '') {
                                            return $path;
                                        }

                                        return app(ImageOptimizer::class)->toWebpFixed('public', $path, 1600, 900) ?? $path;
                                    })
                                    ->helperText('Klik ikon pensil untuk memilih area crop 16:9. Gambar otomatis disimpan 1600×900 px, sama seperti kartu blog.'),
                                TextInput::make('featured_image_alt')
                                    ->maxLength(180)
                                    ->helperText('Wajib deskriptif untuk aksesibilitas dan SEO gambar.'),
                                FileUpload::make('og_image')
                                    ->label('Open Graph image')
                                    ->image()
                                    ->imageEditor()
                                    ->extraAttributes(['class' => 'blog-media-upload'])
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->directory('posts/og')
                                    ->visibility('public')
                                    ->maxSize(3072)
                                    ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                                        $path = $component->saveUploadedFile($file);

                                        if (! is_string($path) || $path === '') {
                                            return $path;
                                        }

                                        return app(ImageOptimizer::class)->toWebpFixed('public', $path, 1200, 630) ?? $path;
                                    })
                                    ->helperText('Opsional. Dipotong ke 1200×630 px (rasio 1.91:1) dan dikonversi ke WebP. Kosongkan untuk memakai featured image.'),
                            ])
                            ->columns(2),
                        Tab::make('SEO')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->maxLength(70)
                                    ->helperText('Ideal ≤ 60 karakter. Kosongkan untuk memakai judul post.'),
                                Textarea::make('meta_description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Ideal 120–160 karakter. Kosongkan untuk memakai excerpt.'),
                                TextInput::make('canonical_url')
                                    ->label('Canonical URL')
                                    ->url()
                                    ->maxLength(255)
                                    ->helperText('Isi hanya jika konten ini duplikat dari URL lain.'),
                                Toggle::make('noindex')
                                    ->label('Noindex')
                                    ->helperText('Aktifkan agar halaman ini tidak diindeks mesin pencari.'),
                            ])
                            ->columns(2),
                        Tab::make('Publish')
                            ->schema([
                                Select::make('status')
                                    ->options(PostStatus::class)
                                    ->required()
                                    ->native(false),
                                DateTimePicker::make('published_at')
                                    ->seconds(false)
                                    ->helperText('Wajib untuk status published/scheduled.'),
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Tanpa kategori'),
                                Toggle::make('is_featured')
                                    ->label('Featured'),
                                TextInput::make('reading_time')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('menit')
                                    ->helperText('Dihitung otomatis dari konten.'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
