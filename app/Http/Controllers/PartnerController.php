<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerImage;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::with(['user', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Partners/Index', [
            'partners' => $partners,
            'meta' => [
                'title' => 'findemich - Manage Business Partners',
                'description' => 'Manage business partner listings, review applications, and maintain partner database with advanced filtering and search tools.',
                'keywords' => 'partner management, business administration, findemich admin, partner listings'
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        return Inertia::render('Partners/Create', [
            'user' => $user->only(['city', 'zip_code', 'latitude', 'longitude']),
            'meta' => [
                'title' => 'findemich - Add New Business Partner',
                'description' => 'Add new business partners to the findemich platform. Enter partner details, location data, and business information through our admin form.',
                'keywords' => 'add partner, new business, partner registration, findemich admin'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Partner creation request:', [
            'has_images' => $request->hasFile('images'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'has_single_image' => $request->hasFile('image'),
            'all_files' => array_keys($request->allFiles())
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_of_owner' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'images' => 'nullable|array|max:15',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
        ]);

        // Handle legacy single image upload
        if ($request->hasFile('image')) {
            \Log::info('Processing single image upload');
            $imagePath = ImageOptimizer::optimizeImage($request->file('image'));
            $validated['image'] = $imagePath;
            \Log::info('Single image optimized:', ['path' => $imagePath]);
        }

        $validated['created_by'] = Auth::id();

        $partner = Partner::create($validated);

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            \Log::info('Processing multiple images upload');
            $sortOrder = 0;
            foreach ($request->file('images') as $image) {
                \Log::info('Optimizing image:', ['original_name' => $image->getClientOriginalName(), 'size' => $image->getSize()]);
                
                // Optimize each image
                $imagePath = ImageOptimizer::optimizeImage($image);
                \Log::info('Image optimized and stored:', ['path' => $imagePath]);
                
                $partnerImage = PartnerImage::create([
                    'partner_id' => $partner->id,
                    'path' => $imagePath,
                    'sort_order' => $sortOrder++,
                ]);
                \Log::info('PartnerImage created:', ['id' => $partnerImage->id]);
            }
        } else {
            \Log::info('No images uploaded');
        }

        return redirect()->route('partners.index')
            ->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        $partner->load(['user', 'images']);

        return Inertia::render('Partners/Show', [
            'partner' => $partner,
            'meta' => [
                'title' => "findemich - {$partner->title}",
                'description' => "View detailed business partner information for {$partner->title} in {$partner->city}. Contact details, services, and administrative management options.",
                'keywords' => "business partner, {$partner->title}, {$partner->city}, {$partner->category}"
            ]
        ]);
    }

    /**
     * Display the specified resource for public viewing (SEO-friendly format).
     */
    public function showPublic(Request $request, $id = null)
    {
        \Log::info('SEO route hit', [
            'query_params' => $request->all(),
            'route_id' => $id,
            'query_id' => $request->query('id'),
            'title' => $request->query('title'),
            'url' => $request->fullUrl()
        ]);
        
        // Get the ID from route parameter or query parameters
        $partnerId = $id ?? $request->query('id');
        
        if (!$partnerId) {
            \Log::error('Partner ID missing in request', ['route_id' => $id, 'query' => $request->all()]);
            abort(404, 'Partner ID is required');
        }

        $partner = Partner::with(['user', 'images'])->findOrFail($partnerId);
        
        \Log::info('Partner found successfully', [
            'partner_id' => $partner->id,
            'partner_title' => $partner->title
        ]);

        // Transform partner data to include original language for frontend translation
        $transformedPartner = [
            'id' => $partner->id,
            'title' => $partner->title,
            'name_of_owner' => $partner->name_of_owner,
            'category' => $partner->category,
            'description' => $partner->description,
            'image' => $partner->image,
            'city' => $partner->city,
            'zip_code' => $partner->zip_code,
            'longitude' => $partner->longitude,
            'latitude' => $partner->latitude,
            'created_at' => $partner->created_at,
            'updated_at' => $partner->updated_at,
            'images' => $partner->images,
            // Mark original language for translation
            'original_lang' => 'de',
            // Fields that should be translated
            'translatable_fields' => ['title', 'description', 'category']
        ];

        return Inertia::render('Partners/Show', [
            'partner' => $transformedPartner,
            'meta' => [
                'title' => "findemich - {$partner->title} in {$partner->city}",
                'description' => "View {$partner->title} - {$partner->category} business partner in {$partner->city}. Contact information, services, and location details available.",
                'keywords' => "business partner, {$partner->title}, {$partner->city}, {$partner->category}, local services"
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        $partner->load(['images', 'user']);
        $user = Auth::user();
        
        return Inertia::render('Partners/Edit', [
            'partner' => $partner,
            'user' => $user->only(['city', 'zip_code', 'latitude', 'longitude'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        // Debug: Log incoming request data
        $imageDetails = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                // Skip empty or invalid files
                if ($file && $file->isValid()) {
                    $imageDetails[] = [
                        'index' => $index,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension()
                    ];
                } else {
                    $imageDetails[] = [
                        'index' => $index,
                        'status' => 'invalid_or_empty'
                    ];
                }
            }
        }
        
        \Log::info('Partner update request data:', [
            'partner_id' => $partner->id,
            'partner_title' => $partner->title,
            'request_title' => $request->input('title'),
            'request_all_inputs' => array_keys($request->all()),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'image_details' => $imageDetails,
            'remove_images' => $request->input('remove_images'),
            'has_images_file' => $request->hasFile('images'),
            'request_method' => $request->method(),
            '_method' => $request->input('_method')
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'name_of_owner' => 'nullable|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'images' => 'nullable|array|max:15',
                'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'nullable|integer|exists:partner_images,id',
                'city' => 'required|string|max:255',
                'zip_code' => 'required|string|max:20',
                'longitude' => 'required|numeric|between:-180,180',
                'latitude' => 'required|numeric|between:-90,90',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }
        
        \Log::info('Validation passed successfully');

        // Handle legacy single image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($partner->image) {
                Storage::disk('public')->delete($partner->image);
            }
            
            \Log::info('Processing single image upload for update');
            $imagePath = ImageOptimizer::optimizeImage($request->file('image'));
            $validated['image'] = $imagePath;
            \Log::info('Single image optimized for update:', ['path' => $imagePath]);
        }

        $partner->update($validated);

        // Handle image removal
        if ($request->has('remove_images') && is_array($request->remove_images) && !empty($request->remove_images)) {
            \Log::info('Processing image removals', ['remove_images' => $request->remove_images]);
            $imagesToRemove = PartnerImage::whereIn('id', $request->remove_images)
                ->where('partner_id', $partner->id)
                ->get();
                
            foreach ($imagesToRemove as $imageToRemove) {
                \Log::info('Removing image', ['id' => $imageToRemove->id, 'path' => $imageToRemove->path]);
                Storage::disk('public')->delete($imageToRemove->path);
                $imageToRemove->delete();
            }
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            \Log::info('Processing new images upload');
            $currentImageCount = $partner->images()->count();
            $maxNewImages = min(15 - $currentImageCount, count($request->file('images')));
            
            \Log::info('Image upload details:', [
                'current_count' => $currentImageCount,
                'max_new' => $maxNewImages,
                'files_received' => count($request->file('images'))
            ]);
            
            $sortOrder = $partner->images()->max('sort_order') + 1;
            if ($sortOrder === null) $sortOrder = 1;
            
            $uploadedImages = array_slice($request->file('images'), 0, $maxNewImages);
            
            foreach ($uploadedImages as $index => $image) {
                try {
                    \Log::info("Optimizing image {$index} for update", ['original_name' => $image->getClientOriginalName(), 'size' => $image->getSize()]);
                    $imagePath = ImageOptimizer::optimizeImage($image);
                    $partnerImage = PartnerImage::create([
                        'partner_id' => $partner->id,
                        'path' => $imagePath,
                        'sort_order' => $sortOrder++,
                    ]);
                    \Log::info("Successfully created optimized partner image", ['id' => $partnerImage->id, 'path' => $imagePath]);
                } catch (\Exception $e) {
                    \Log::error("Failed to process image {$index}: " . $e->getMessage());
                }
            }
        }

        \Log::info('Partner update completed successfully', ['partner_id' => $partner->id]);
        
        return redirect()->route('partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    /**
     * Display the partners map page
     */
    public function map()
    {
        $partners = Partner::with(['user', 'images'])
            ->select(['id', 'title', 'name_of_owner', 'category', 'description', 'image', 'city', 'zip_code', 'latitude', 'longitude', 'created_by'])
            ->get();

        return Inertia::render('PartnersMap', [
            'partners' => $partners,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        // Delete associated single image if it exists
        if ($partner->image) {
            Storage::disk('public')->delete($partner->image);
        }
        
        // Delete all associated images
        foreach ($partner->images as $image) {
            Storage::disk('public')->delete($image->path);
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
        $query = Partner::with('images');

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

        // Category filter (supports multiple categories)
        if ($request->has('categories') && is_array($request->categories) && !empty($request->categories)) {
            $query->whereIn('category', $request->categories);
        } elseif ($request->has('category') && $request->category && $request->category !== 'all') {
            // Backward compatibility for single category
            $query->where('category', $request->category);
        }

        // City filter (supports multiple cities)
        if ($request->has('cities') && is_array($request->cities) && !empty($request->cities)) {
            $query->whereIn('city', $request->cities);
        } elseif ($request->has('city') && $request->city && $request->city !== 'all') {
            // Backward compatibility for single city
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
        if ($page == 1 && !$request->has('search') && !$request->has('category') && !$request->has('city') && !$request->has('categories') && !$request->has('cities')) {
            $allPartners = Partner::all();
            $categories = $allPartners->pluck('category')->unique()->filter()->values()->toArray();
            $cities = $allPartners->pluck('city')->unique()->filter()->values()->toArray();
        }

        // Transform partners data to include original content for frontend translation
        $transformedPartners = $partners->getCollection()->map(function ($partner) {
            return [
                'id' => $partner->id,
                'title' => $partner->title,
                'name_of_owner' => $partner->name_of_owner,
                'category' => $partner->category,
                'description' => $partner->description,
                'image' => $partner->image,
                'city' => $partner->city,
                'zip_code' => $partner->zip_code,
                'longitude' => $partner->longitude,
                'latitude' => $partner->latitude,
                'created_at' => $partner->created_at,
                'updated_at' => $partner->updated_at,
                'images' => $partner->images,
                // Mark original language for translation
                'original_lang' => 'de',
                // Fields that should be translated
                'translatable_fields' => ['title', 'description', 'category']
            ];
        });

        return response()->json([
            'data' => $transformedPartners,
            'current_page' => $partners->currentPage(),
            'last_page' => $partners->lastPage(),
            'per_page' => $partners->perPage(),
            'total' => $partners->total(),
            'has_more' => $partners->hasMorePages(),
            'categories' => $categories,
            'cities' => $cities,
        ]);
    }

    /**
     * Search for locations using OpenStreetMap Nominatim API
     */
    public function searchLocation(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:255',
        ]);

        $query = $request->input('q');

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Partner Management App'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $query,
                'limit' => 5,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Search service unavailable'], 503);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Reverse geocode coordinates to get address information
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->input('lat');
        $lng = $request->input('lng');

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Partner Management App'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 18,
                'addressdetails' => 1
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Geocoding service unavailable'], 503);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Geocoding failed'], 500);
        }
    }
}