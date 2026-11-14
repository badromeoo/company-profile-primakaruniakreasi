<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the editable content for the public home page.
     */
    public function edit(): View
    {
        $contents = HomeContent::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group');

        return view('admin.home.edit', ['contents' => $contents]);
    }

    /**
     * Update the content of the public home page in bulk.
     */
    public function update(Request $request): RedirectResponse
    {
        $sectionKeys = HomeContent::query()->pluck('section_key')->all();

        $attributeLabels = [];

        foreach ($sectionKeys as $sectionKey) {
            $attributeLabels['contents.'.$sectionKey] = Str::headline($sectionKey);
        }

        /** @var array{contents: array<string, string|null>} $validated */
        $validated = $request->validate(
            [
                'contents' => ['required', 'array'],
                'contents.*' => ['nullable', 'string', 'max:5000'],
            ],
            [],
            $attributeLabels,
        );

        $updates = array_intersect_key($validated['contents'], array_flip($sectionKeys));

        DB::transaction(function () use ($updates): void {
            foreach ($updates as $sectionKey => $contentValue) {
                HomeContent::query()
                    ->where('section_key', $sectionKey)
                    ->update(['content_value' => $contentValue ?? '']);
            }
        });

        return redirect()
            ->route('admin.home-content.edit')
            ->with('status', 'home-content-updated');
    }
}
