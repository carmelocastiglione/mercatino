<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BookDelivery;
use App\Models\BookListing;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
    /**
     * Display a listing of pending deliveries.
     */
    public function index(): View
    {
        $deliveries = BookDelivery::where('status', 'pending')
            ->with('user', 'book')
            ->latest()
            ->paginate(10);

        return view('staff.deliveries.index', [
            'deliveries' => $deliveries,
        ]);
    }

    /**
     * Display the specified delivery for review.
     */
    public function show(BookDelivery $delivery): View
    {
        return view('staff.deliveries.show', [
            'delivery' => $delivery->load('user', 'book'),
        ]);
    }

    /**
     * Approve the specified delivery.
     */
    public function approve(BookDelivery $delivery): RedirectResponse
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi approvare solo consegne in sospeso');
        }

        // Aggiorna la consegna
        $delivery->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        // Crea una nuova BookListing dal BookDelivery
        BookListing::create([
            'book_id' => $delivery->book_id,
            'seller_id' => $delivery->user_id,
            'condition' => $delivery->condition,
            'price' => $delivery->price,
            'status' => 'available',
        ]);

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Consegna approvata e aggiunta al catalogo!');
    }

    /**
     * Show the form for rejecting a delivery.
     */
    public function rejectForm(BookDelivery $delivery): View
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi rifiutare solo consegne in sospeso');
        }

        return view('staff.deliveries.reject', [
            'delivery' => $delivery->load('user', 'book'),
        ]);
    }

    /**
     * Reject the specified delivery.
     */
    public function reject(Request $request, BookDelivery $delivery): RedirectResponse
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Puoi rifiutare solo consegne in sospeso');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Specifica il motivo del rifiuto',
            'rejection_reason.max' => 'Il motivo non può superare 500 caratteri',
        ]);

        $delivery->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Consegna rifiutata correttamente.');
    }

    /**
     * Reject a delivery (JSON API endpoint)
     */
    public function rejectDeliveryJson(Request $request)
    {
        $deliveryId = $request->query('delivery_id');
        $delivery = BookDelivery::find($deliveryId);

        if (!$delivery) {
            return response()->json(['success' => false, 'message' => 'Consegna non trovata'], 404);
        }

        if ($delivery->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Consegna non in sospeso'], 400);
        }

        $delivery->update([
            'status' => 'rejected',
            'rejection_reason' => 'Rifiutata dallo staff',
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Consegna rifiutata']);
    }

    /**
     * Approve multiple deliveries (JSON API endpoint)
     */
    public function approveBulk(Request $request)
    {
        $deliveryIds = $request->input('delivery_ids', []);

        if (empty($deliveryIds)) {
            return response()->json(['success' => false, 'message' => 'Nessuna consegna specificata'], 400);
        }

        $deliveries = BookDelivery::whereIn('id', $deliveryIds)
            ->where('status', 'pending')
            ->get();

        if ($deliveries->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nessuna consegna pending trovata'], 404);
        }

        $approvedCount = 0;
        foreach ($deliveries as $delivery) {
            $delivery->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // Crea una nuova BookListing dal BookDelivery
            BookListing::create([
                'book_id' => $delivery->book_id,
                'seller_id' => $delivery->user_id,
                'condition' => $delivery->condition,
                'price' => $delivery->price,
                'status' => 'available',
            ]);

            $approvedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$approvedCount consegne approvate",
            'approved_count' => $approvedCount
        ]);
    }
}
