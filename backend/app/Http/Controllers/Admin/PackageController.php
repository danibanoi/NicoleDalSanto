<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        return Package::withCount(['orders' => fn ($q) => $q->where('status', 'active')])->orderBy('sort')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] ??= Str::slug($data['name']);

        return response()->json(Package::create($data), 201);
    }

    public function update(Request $request, Package $package)
    {
        $package->update($this->validated($request, $package));

        return $package;
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return response()->noContent();
    }

    private function validated(Request $request, ?Package $package = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'alpha_dash', 'max:120', Rule::unique('packages')->ignore($package)],
            'category' => ['required', 'string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'duration_weeks' => ['required', 'integer', 'min:1', 'max:104'],
            'sessions_per_week' => ['required', 'integer', 'min:1', 'max:14'],
            'level' => ['required', 'string', 'max:60'],
            'mode' => ['required', 'string', 'max:60'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:190'],
            'program' => ['nullable', 'array'],
            'program.*.title' => ['required', 'string', 'max:120'],
            'program.*.text' => ['required', 'string', 'max:500'],
            'color' => ['required', Rule::in(['pink', 'magenta', 'black'])],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort' => ['integer'],
        ]);
    }
}
