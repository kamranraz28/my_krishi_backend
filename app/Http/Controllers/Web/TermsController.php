<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    //
    public function index()
    {
        $terms = Term::orderBy('created_at', 'desc')->get();
        return view('terms.index', compact('terms'));
    }


    public function create()
    {
        return view('terms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create or update the Term record
        Term::create([
            'title' => $request->title,
            'content' => $request->content,  // raw HTML from TinyMCE
        ]);

        return redirect()->route('terms.index')->with('success', 'Terms saved successfully!');
    }
    public function show($id)
    {
        $term = Term::findOrFail($id);
        return view('terms.show', compact('term'));
    }

}
