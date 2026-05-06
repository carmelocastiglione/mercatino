{{-- This view provides JSON data for the AJAX request --}}
<div data-seller>{{ json_encode([
    'id' => $seller->id,
    'name' => $seller->name,
    'surname' => $seller->surname,
    'email' => $seller->email,
    'code' => $seller->code,
    'total_sales' => number_format($seller->getTotalSalesAmount(), 2),
    'total_withdrawn' => number_format($seller->getTotalWithdrawnAmount(), 2),
    'available_balance' => number_format($seller->getAvailableBalance(), 2),
]) }}</div>

<div data-sold-books>{{ json_encode($soldBooks->map(function($listing) {
    return [
        'id' => $listing->id,
        'title' => $listing->book->title,
        'author' => $listing->book->author,
        'price' => number_format($listing->price, 2),
        'condition' => $listing->condition,
        'status' => $listing->status,
    ];
})->values()->all()) }}</div>

<div data-unsold-books>{{ json_encode($unsoldBooks->map(function($listing) {
    return [
        'id' => $listing->id,
        'title' => $listing->book->title,
        'author' => $listing->book->author,
        'price' => number_format($listing->price, 2),
        'condition' => $listing->condition,
        'status' => $listing->status,
    ];
})->values()->all()) }}</div>
