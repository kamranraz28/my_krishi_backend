<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Project;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    //
    public function index($id)
    {
        $project = Project::with('details')->findOrFail($id);
        return view('faq.index',compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|array',
            'answer' => 'required|array',
            'question.*' => 'string|max:255',
            'answer.*' => 'string|max:1000',
        ]);
        //dd($request->all());
        $project_id = $request->project_id;
        $questions = $request->question;
        $answers = $request->answer;

        foreach ($questions as $index => $question) {
            Faq::create([
                'project_id' => $project_id,
                'question' => $question,
                'answer' => $answers[$index] ?? null,
            ]);
        }
        return redirect()->back()->with('success', 'FAQ added successfully.');
    }

    public function edit($id)
    {
        $faq = Faq::with('project.details')->findOrFail($id);
        return view('faq.edit',compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('faqs.index', $faq->project_id)
            ->with('success', 'FAQ updated successfully.');
    }

    public function delete($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return redirect()->back()->with('success', 'FAQ deleted successfully.');
    }
}
