<?php

namespace App\Http\Controllers;

use App\Models\Table;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $tables = Table::byBroker()->get();

        return view('pages.dashboard', compact('tables'));
    }
}
