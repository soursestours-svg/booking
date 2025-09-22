<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $locale, Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        return view('booking.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $locale, Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'guests_count' => 'required|integer|min:1|max:10',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate total price based on days and guests
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate);
        $totalPrice = $service->price * $days * $validated['guests_count'];

        $bookingData = array_merge($validated, [
            'service_id' => $service->id,
            'user_id' => auth()->id() ?? 1, // Temporary for demo
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $booking = Booking::create($bookingData);

        // Перенаправляем на страницу оплаты
        \Log::info('Redirecting to payment for booking', ['booking_id' => $booking->id]);
        return redirect()->route('payment.create', ['locale' => app()->getLocale(), 'booking' => $booking])
            ->with('info', 'Переходим к оплате бронирования');
    }

    /**
     * Display success page after booking.
     */
    public function success(string $locale, Booking $booking)
    {
        return view('booking.success', compact('booking'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
