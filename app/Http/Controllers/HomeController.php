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
            $query->where('name', 'like', '%' . $search . '%'); // Searching district by name
        })
        ->orWhere('address', 'like', '%' . $search . '%') // Searching in address
        ->orWhere('name', 'like', '%' . $search . '%') // Searching in organization name
        ->paginate(20);
     // Adjust the number of items per page

        return view('welcome', compact('organizations'));
    }
}
