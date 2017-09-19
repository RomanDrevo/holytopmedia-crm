<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailsReportsController extends Controller
{
    public function index()
    {
        return view('pages.marketing.reports.index');
    }
}
