<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $organizations = Organization::with('district')->get();
        return view('welcome', compact('organizations'));
    }
}
