<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Partners/Index', [
            'partners' => $partners,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Partners/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_of_owner' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('partners', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['created_by'] = Auth::id();

        Partner::create($validated);

        return redirect()->route('partners.index')
            ->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        $partner->load('user');

        return Inertia::render('Partners/Show', [
            'partner' => $partner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        return Inertia::render('Partners/Edit', [
            'partner' => $partner,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_of_owner' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($partner->image) {
                Storage::disk('public')->delete($partner->image);
            }
            
            $imagePath = $request->file('image')->store('partners', 'public');
            $validated['image'] = $imagePath;
        }

        $partner->update($validated);

        return redirect()->route('partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        // Delete associated image if it exists
        if ($partner->image) {
            Storage::disk('public')->delete($partner->image);
        }
        
        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', 'Partner deleted successfully.');
    }

    /**
     * Get partners for public display with search and filter capabilities
     */
    public function getPublicPartners(Request $request)
    {
        $query = Partner::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        // Category filter
        if ($request->has('category') && $request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // City filter
        if ($request->has('city') && $request->city && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        // Pagination - default 12 per page for "Load More" functionality
        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);
        
        $partners = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // For initial load (page 1), also get categories and cities for filters
        $categories = [];
        $cities = [];
        if ($page == 1 && !$request->has('search') && !$request->has('category') && !$request->has('city')) {
            $allPartners = Partner::all();
            $categories = $allPartners->pluck('category')->unique()->filter()->values()->toArray();
            $cities = $allPartners->pluck('city')->unique()->filter()->values()->toArray();
        }

        return response()->json([
            'data' => $partners->items(),
            'current_page' => $partners->currentPage(),
            'last_page' => $partners->lastPage(),
            'per_page' => $partners->perPage(),
            'total' => $partners->total(),
            'has_more' => $partners->hasMorePages(),
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }
}