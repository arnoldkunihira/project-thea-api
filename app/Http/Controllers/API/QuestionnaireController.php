<?php

namespace App\Http\Controllers\API;

use App\Helpers\APIHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionnaireController extends Controller
{
    public const RECORDS_PER_PAGE = 20;

    /**
     * Display a listing of a questionnaire resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questionnaires = Questionnaire::select('questionnaires.*', 'subjects.first_name', 'subjects.last_name', 'projects.name')
            ->leftJoin('subjects', 'questionnaires.subject_id', '=', 'subjects.id')
            ->leftJoin('projects', 'questionnaires.project_id', '=', 'projects.id')
            ->orderBy('questionnaires.test_date', 'ASC')
            ->paginate(self::RECORDS_PER_PAGE);

        $questionnairesCollection = QuestionnaireResource::collection($questionnaires);
        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaires retrieved successfully', $questionnairesCollection);
        return response()->json($apiResponse, 200);
    }

    /**
     * Store a newly created questionnaires resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validateData = [
            'project_id' => 'exists:App\Models\Project,id',
            'subject_id' => 'exists:App\Models\Subject,id',
            'status_id' => 'exists:App\Models\Status,id',
            'test_date' => 'required|date',
            'status_update_date' => 'date',
            'created_with' => 'string|max:4',
            'updated_with' => 'string|max:4',
        ];

        $validator = Validator::make($data, $validateData);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $questionnaire = Questionnaire::create($data);
        $questionnaireResource = new QuestionnaireResource($questionnaire);
        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaire created successfully', $questionnaireResource);
        return response()->json($apiResponse, 201);
    }

    /**
     * Display a specified Questionnaire.
     *
     * @param  \App\Models\Questionnaire  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Questionnaire $question)
    {
        $questionnaireResource = new QuestionnaireResource($question);
        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaire retrieved successfully', $questionnaireResource);
        return response()->json($apiResponse, 200);
    }

    /**
     * Update a specified Questionnaire in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Questionnaire  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Questionnaire $question)
    {
        $question->update($request->all());
        $updatedQuestionnaire = new QuestionnaireResource($question);

        if (!$updatedQuestionnaire) {
            $apiResponse = APIHelpers::formatAPIResponse(true, 'Questionnaire update failed', null);
            return response()->json($apiResponse, 400);
        }

        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaire updated successfully', $updatedQuestionnaire);
        return response()->json($apiResponse, 200);
    }

    /**
     * Remove a specified Questionnaire from storage.
     *
     * @param  \App\Models\Questionnaire  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questionnaire $question)
    {
        $question->delete();
        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaire deleted successfully', null);
        return response()->json($apiResponse, 204);
    }

    /**
     * Get tests for anonymous subject
     *
     * @param  String  $unique_id
     * @return \Illuminate\Http\Response
     */
    public function get_questionnaires($unique_id)
    {
        $tests = Questionnaire::select('questionnaires.*', 'subjects.first_name', 'subjects.last_name', 'projects.name')
            ->where('questionnaires.unique_id', $unique_id)
            ->leftJoin('subjects', 'questionnaires.subject_id', '=', 'subjects.id')
            ->leftJoin('projects', 'questionnaires.project_id', '=', 'projects.id')
            ->orderBy('questionnaires.test_date', 'ASC')
            ->paginate(self::RECORDS_PER_PAGE);

        $questionnaireCollection = QuestionnaireResource::collection($tests);
        $apiResponse = APIHelpers::formatAPIResponse(false, 'Questionnaires retrieved successfully', $questionnaireCollection);
        return response()->json($apiResponse, 200);
    }
}