<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display the home page with popular services.
     */
    public function home()
    {
        $popularServices = Service::with('reviews')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('popularServices'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with(['partner', 'reviews'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('services.index', compact('services'));
    }

    /**
     * Search services.
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        $priceRange = $request->input('price_range');
        $rating = $request->input('rating');

        $query = Service::with(['partner', 'reviews'])->where('is_active', true);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($priceRange) {
            $prices = explode('-', $priceRange);
            if (count($prices) === 2) {
                $query->whereBetween('price', [$prices[0], $prices[1]]);
            } else {
                $query->where('price', '>=', $prices[0]);
            }
        }

        if ($rating) {
            $query->withAvg('reviews', 'rating')->having('reviews_avg_rating', '>=', $rating);
        }

        $services = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        return view('services.search', compact('services', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $locale, Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        $service->load(['partner', 'reviews' => function($query) {
            $query->where('is_approved', true)
                  ->where('is_visible', true)
                  ->with('user')
                  ->latest()
                  ->take(10);
        }]);

        $images = $service->getMedia('images');

        // Расчет среднего рейтинга
        $averageRating = $service->reviews->avg('rating');
        $reviewsCount = $service->reviews->count();

        return view('services.show_enhanced', compact('service', 'averageRating', 'reviewsCount', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
