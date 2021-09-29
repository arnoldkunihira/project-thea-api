<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DataType;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Questionnaire $questionnaire, Request $request)
    {
        $validationRules = [
            'datatype_id' => 'exists:App\Models\DataType,id',
            'title' => 'required|string|max:55',
            'attributes' => 'json',
        ];

        $validateData = $request->validate($validationRules);

        if (!$validateData) {
            return Redirect::route('questionnaires.edit', ['questionnaire' => $questionnaire])->with('fail', 'There was a validation error.');
        }

        $validateData['created_by'] = Auth::id();
        $validateData['updated_by'] = Auth::id();
        $validateData['questionnaire_id'] = $questionnaire->id;

        Question::create($validateData);
        return Redirect::route('questionnaires.edit', ['questionnaire' => $questionnaire])->with('success', 'Question successfully created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Questionnaire $questionnaire, Question $question)
    {
        $dataTypes = DataType::all();

        return Inertia::render('Questions/Edit', [
            'question' => $question,
            'questionnaire' => $questionnaire,
            'dataTypes' => $dataTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Questionnaire $questionnaire, Question $question, Request $request)
    {
        $validationRules = [
            'title' => 'required|string|max:55',
            'datatype_id' => 'exists:App\Models\DataType,id',
            'attributes' => 'json',
        ];

        $validateData = $request->validate($validationRules);

        $validateData['updated_by'] = Auth::id();

        $question->update($validateData);

        return Redirect::route('questionnaires.edit', ['questionnaire' => $questionnaire])->with('success', 'Question successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questionnaire $questionnaire, Question $question)
    {
        $question->delete();
        return Redirect::route('questionnaires.edit', ['questionnaire' => $questionnaire])->with('success', 'Question successfully archived.');
    }

    /**
     * Restore the specified resource from trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Questionnaire $questionnaire, $id)
    {
        $question = Question::withTrashed()->find($id);
        $question->restore();
        return Redirect::route('questionnaires.edit', ['questionnaire' => $questionnaire])->with('success', 'Question successfully restored.');
    }
}
