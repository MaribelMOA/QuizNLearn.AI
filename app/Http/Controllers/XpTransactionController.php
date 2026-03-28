<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreXpTransactionRequest;
use App\Http\Requests\UpdateXpTransactionRequest;
use App\Models\XpTransaction;

class XpTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $xpTransactions = XpTransaction::all();

        return response()->json($xpTransactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreXpTransactionRequest $request)
    {
        $xpTransaction = XpTransaction::create($request->validated());

        return response()->json($xpTransaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(XpTransaction $xpTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateXpTransactionRequest $request, XpTransaction $xpTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(XpTransaction $xpTransaction)
    {
        //
    }
}
