<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\Availability\AggregatorService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function byIsbn(Request $request, AggregatorService $aggregator)
    {
        $data = $request->validate([
            'isbn' => 'required|string',
        ]);

        $isbn = preg_replace('/[^0-9Xx]/', '', $data['isbn']);

        $book = Book::where('isbn_13', $isbn)
            ->orWhere('isbn_10', $isbn)
            ->first();

        $availability = $aggregator->aggregateByIsbn($isbn);

        return response()->json([
            'book'        => $book,
            'availability'=> $availability,
        ]);
    }

    public function byTitle(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|min:2',
        ]);

        $q = $data['title'];

        $books = Book::where('title', 'like', "%{$q}%")
            ->orderBy('title')
            ->limit(20)
            ->get();

        return response()->json([
            'books' => $books,
        ]);
    }
}
