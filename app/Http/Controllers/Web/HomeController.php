<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the Homepage / Landing Page.
     */
    public function index(Request $request): View
    {
        $subjects = Schema::hasTable('subjects')
            ? Subject::withCount('topics')->get()
            : collect();

        $plans = Schema::hasTable('plans')
            ? Plan::orderBy('price')->get()
            : collect();

        return view('home', [
            'subjects' => $subjects,
            'plans' => $plans,
            'user' => $request->user(),
        ]);
    }
}
