<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
{
    public function create()
    {
        return view('invoice.create');
    }

    public function generate(Request $request)
    {
        $data = $request->all();
        $pdf = PDF::loadView('invoice.pdf', compact('data'));
        return $pdf->stream('invoice.pdf'); // or ->download('invoice.pdf')
    }
}
