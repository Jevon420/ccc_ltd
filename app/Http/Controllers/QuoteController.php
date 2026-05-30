<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('quotes.view'), 403);

        return view('dashboard.quotes.index');
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('quotes.create'), 403);

        return view('dashboard.quotes.create');
    }

    public function show(Quote $quote): View
    {
        abort_unless(auth()->user()->can('quotes.view'), 403);

        return view('dashboard.quotes.show', compact('quote'));
    }
}
