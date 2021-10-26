<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;


class TransactionController extends Controller
{
    public function show()
    {
         return view('transaction.show');
    }

    public function store(Request $request)
    {
        $book = Book::find($request->book_id);
        $method = $request->method;
        
        $tripay = new TripayController();
        $tripay->requestTransaction($method, $book);
    }
}
