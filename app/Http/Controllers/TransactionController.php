<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Transaction;


class TransactionController extends Controller
{
    public function show($reference)
    {
        $tripay = new TripayController();
        $detail = $tripay->detailTransaksi($reference);
        return view('transaction.show',compact('detail'));
    }

    public function store(Request $request)
    {
        // Request Transaction In Tripay
        $book = Book::find($request->book_id);
        $method = $request->method;
        
        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $book);

        // Create a new Data in Transaction Model 
        Transaction::create([
            'user_id'       => auth()->user()->id,
            'book_id'       => $book->id,
            'reference'     => $transaction->reference,
            'merchant_ref'     => $transaction->merchant_ref,
            'total_amount'     => $transaction->amount,
            'status'     => $transaction->status,
        ]);


        return redirect()->route('transaction.show',[
            'reference' => $transaction->reference,  
        ]);
    }
}
