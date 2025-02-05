<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $organizations = Organization::paginate(20);
        return view('welcome', compact('organizations'));
    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        // Query with search filter
        $organizations = Organization::whereHas('district', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->orWhere('address', 'like', '%' . $search . '%')
        ->orWhere('name', 'like', '%' . $search . '%')
        ->paginate(20);

        return view('welcome', compact('organizations'));
    }

    public function verifyMember(){
        echo 'feij';
    }
}
