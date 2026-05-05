<?php
namespace App\Http\Controllers;
use App\Models\ProductQuestion;
use App\Models\ProductAnswer;
use Illuminate\Http\Request;


class ProductQuestionController extends Controller
{
    public function index()
    {
        $questions = ProductQuestion::with([
            'product:id,name',
            'user:id,name',
            'answers.user:id,name'
        ])
            ->latest()
            ->paginate(20);

        return view('admin.product_questions.index', compact('questions'));
    }


    public function answer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:product_questions,id',
            'answer' => 'required|string'
        ]);

        ProductAnswer::create([
            'product_question_id' => $request->question_id,
            'user_id' => auth()->id() ?? 0,
            'answer' => $request->answer,
            'is_approved' => true
        ]);

        ProductQuestion::where('id', $request->question_id)
            ->update(['is_answered' => true]);

        return back()->with('success', __('admin.Answer submitted successfully'));
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:product_questions,id',
            'status' => 'required|boolean'
        ]);

        $question = ProductQuestion::findOrFail($request->question_id);

        $question->update([
            'is_approved' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => __('admin.status_updated')
        ]);
    }
}
