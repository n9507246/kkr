<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KkrReportController extends Controller
{
    public function index(){
        return view('all-work-performed');
    }
}
