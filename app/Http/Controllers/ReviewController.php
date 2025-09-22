<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Для админ-панели - все отзывы
        if (Auth::user()->hasRole("admin")) {
            $reviews = Review::with(["service", "user", "booking"])
                ->latest()
                ->paginate(20);
        } else {
            // Для партнеров - только отзывы на их услуги
            $reviews = Review::with(["service", "user", "booking"])
                ->whereHas("service", function ($query) {
                    $query->where("partner_id", Auth::id());
                })
                ->latest()
                ->paginate(20);
        }

        return view("reviews.index", compact("reviews"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Service $service)
    {
        // Проверяем, может ли пользователь оставить отзыв
        if (!Review::canUserReviewService(Auth::id(), $service->id)) {
            return redirect()->route("services.show", $service)
                ->with("error", "Вы можете оставить отзыв только после завершенного бронирования этой услуги.");
        }

        // Проверяем, не оставлял ли пользователь уже отзыв
        $existingReview = Review::where("user_id", Auth::id())
            ->where("service_id", $service->id)
            ->first();

        if ($existingReview) {
            return redirect()->route("reviews.edit", $existingReview);
        }

        return view("reviews.create", compact("service"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Service $service)
    {
        // Проверяем, может ли пользователь оставить отзыв
        if (!Review::canUserReviewService(Auth::id(), $service->id)) {
            return redirect()->route("services.show", $service)
                ->with("error", "Вы можете оставить отзыв только после завершенного бронирования этой услуги.");
        }

        // Проверяем, не оставлял ли пользователь уже отзыв
        $existingReview = Review::where("user_id", Auth::id())
            ->where("service_id", $service->id)
            ->first();

        if ($existingReview) {
            return redirect()->route("reviews.edit", $existingReview);
        }

        $validated = $request->validate([
            "rating" => "required|integer|min:1|max:5",
            "comment" => "required|string|min:10|max:1000",
        ]);

        // Находим последнее завершенное бронирование для этой услуги
        $booking = Booking::where("user_id", Auth::id())
            ->where("service_id", $service->id)
            ->where("status", Booking::STATUS_CONFIRMED)
            ->where("payment_status", Booking::PAYMENT_PAID)
            ->latest()
            ->first();

        $review = Review::create([
            "service_id" => $service->id,
            "user_id" => Auth::id(),
            "booking_id" => $booking?->id,
            "rating" => $validated["rating"],
            "comment" => $validated["comment"],
            "is_approved" => false, // Требует модерации
            "is_visible" => false, // Не виден до одобрения
        ]);

        return redirect()->route("services.show", $service)
            ->with("success", "Отзыв успешно отправлен на модерацию. Спасибо!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $this->authorize("view", $review);
        
        $review->load(["service", "user", "booking"]);
        
        return view("reviews.show", compact("review"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $this->authorize("update", $review);
        
        return view("reviews.edit", compact("review"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $this->authorize("update", $review);

        $validated = $request->validate([
            "rating" => "required|integer|min:1|max:5",
            "comment" => "required|string|min:10|max:1000",
        ]);

        $review->update($validated);

        return redirect()->route("services.show", $review->service)
            ->with("success", "Отзыв успешно обновлен.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $this->authorize("delete", $review);
        
        $review->delete();

        return redirect()->back()
            ->with("success", "Отзыв успешно удален.");
    }

    /**
     * Одобрить отзыв (для модерации)
     */
    public function approve(Review $review)
    {
        $this->authorize("approve", $review);

        $review->update([
            "is_approved" => true,
            "is_visible" => true,
        ]);

        return redirect()->back()
            ->with("success", "Отзыв одобрен и теперь виден пользователям.");
    }

    /**
     * Скрыть отзыв (для модерации)
     */
    public function hide(Review $review)
    {
        $this->authorize("approve", $review);

        $review->update(["is_visible" => false]);

        return redirect()->back()
            ->with("success", "Отзыв скрыт от пользователей.");
    }
}