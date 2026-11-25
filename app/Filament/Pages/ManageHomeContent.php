<?php

namespace App\Filament\Pages;

use App\Models\HomeContent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageHomeContent extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Home Content';

    protected static ?string $title = 'Home Content';

    protected string $view = 'filament.pages.manage-home-content';

    /**
     * @var array<string, string|null>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            HomeContent::query()->pluck('content_value', 'section_key')->all(),
        );
    }

    public function form(Schema $schema): Schema
    {
        $sections = HomeContent::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group')
            ->map(fn (Collection $contents, string $group): Section => Section::make(Str::headline($group).' Section')
                ->description('Manage the text shown in this section of the public home page.')
                ->schema(
                    $contents
                        ->map(fn (HomeContent $content): TextInput|Textarea => $this->contentField($content))
                        ->all(),
                ))
            ->values()
            ->all();

        return $schema
            ->components($sections)
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->key('form-actions'),
                    ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $sectionKeys = HomeContent::query()->pluck('section_key')->all();

        /** @var array<string, string|null> $contents */
        $contents = array_intersect_key($this->form->getState(), array_flip($sectionKeys));

        DB::transaction(function () use ($contents): void {
            foreach ($contents as $sectionKey => $contentValue) {
                HomeContent::query()
                    ->where('section_key', $sectionKey)
                    ->update(['content_value' => $contentValue ?? '']);
            }
        });

        Notification::make()
            ->title('Home page content saved successfully.')
            ->success()
            ->send();
    }

    private function contentField(HomeContent $content): TextInput|Textarea
    {
        $field = Str::contains($content->section_key, ['text', 'subtitle', 'description'])
            ? Textarea::make($content->section_key)->rows(3)
            : TextInput::make($content->section_key);

        return $field
            ->label(Str::headline($content->section_key))
            ->maxLength(5000);
    }
}
