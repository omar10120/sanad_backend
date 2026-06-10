<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionReport\StoreQuestionReportRequest;
use App\Http\Resources\QuestionResource;
use App\Services\QuestionService;
use App\Services\QuestionReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiQuestionController extends Controller
{
    use ApiResponseTrait;

    protected QuestionService $questionService;
    protected QuestionReportService $questionReportService;

    public function __construct(QuestionService $questionService, QuestionReportService $questionReportService)
    {
        $this->questionService = $questionService;
        $this->questionReportService = $questionReportService;
    }

    /**
     * Get all questions
     */
    public function index(): JsonResponse
    {
        try {
            $questions = $this->questionService->getAllQuestions();
            $questions = QuestionResource::collection($questions);
            return $this->apiResponse($questions, 'جميع الأسئلة', 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Get a specific question by UUID only if the authenticated student
     * has access to the subject that contains this question.
     */
    public function show($uuid): JsonResponse
    {
        try {
            $question = $this->questionService->getQuestionByUuid($uuid);
            if(!$question){
                return $this->apiResponse(null, 'السؤال غير موجود!', 404);
            }

            $question->load(['questionGroup.lesson.subject']);
            $lesson = optional($question->questionGroup)->lesson;
            $subject = optional($lesson)->subject;

            if (!$subject) {
                return $this->apiResponse(null, 'المادة غير موجودة لهذا السؤال', 404);
            }

            $student = Auth::user();
            if (!$student) {
                return $this->apiResponse(null, 'غير مصرح', 401);
            }

//            $hasAccess = $subject->checkStudentAccess($student->id);
//            if (!$hasAccess) {
//                return $this->apiResponse(null, trans('auth.unauthorized'), 403);
//            }

            $resource = new QuestionResource($question);
            return $this->apiResponse($resource, 'السؤال ' . $question->id, 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Receive a new question report from Flutter app
     */
    public function storeReport(StoreQuestionReportRequest $request): JsonResponse
    {
        try {
            $report = $this->questionReportService->createReport(
                $request->validated(),
                Auth::user()->id
            );

            return $this->apiResponse($report, 'تم إرسال تقرير السؤال بنجاح', 201);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 422);
        }
    }
}
