<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Http\Resources\QuestionGroupResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionTagResource;
use App\Http\Resources\QuestionTypeResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\TagResource;
use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiSubjectController extends Controller
{
    use ApiResponseTrait;

    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function index(): JsonResponse
    {
        $subjects = Auth::user()->type->subjects->where('is_active',1);
        $question_types = $this->subjectService->getAllQuestionTypes();
        $type_has_subject = $this->subjectService->getTypeHasSubjectRelationshipsByType(Auth::user()->type->id);

        $subjects = SubjectResource::collection($subjects);
        $question_types = QuestionTypeResource::collection($question_types);
        $data = [
            'subjects' => $subjects,
            'question_types' => $question_types,
            'subject_certificate' => $type_has_subject,
        ];
        return $this->apiResponse($data, 'جميع المواد وأنواع الأسئلة', 200);
    }

    public function allSubjectsForAdmin(): JsonResponse
    {
        $subjects = $this->subjectService->getAllSubjects();
        $question_types = $this->subjectService->getAllQuestionTypes();
        $type_has_subject = $this->subjectService->getTypeHasSubjectRelationships();

        $subjects = SubjectResource::collection($subjects);
        $question_types = QuestionTypeResource::collection($question_types);
        $data = [
            'subjects' => $subjects,
            'question_types' => $question_types,
            'subject_certificate' => $type_has_subject,
        ];
        return $this->apiResponse($data, 'جميع المواد وأنواع الأسئلة', 200);
    }

    public function show($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);
        if (!$subject) {
            return $this->apiResponse(null, 'المادة غير موجودة', 400);
        }
        $subject = new SubjectResource($subject);
        return $this->apiResponse($subject, 'المادة ' . $subject->id, 200);
    }

    public function lessons($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);
        if (!$subject) {
            return $this->apiResponse(null, 'المادة غير موجودة', 400);
        }
        $lessons = LessonResource::collection($subject->lessons);
        return $this->apiResponse($lessons, 'الدروس في المادة ' . $subject->id, 200);
    }

    public function tags($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);
        if (!$subject) {
            return $this->apiResponse(null, 'المادة غير موجودة', 400);
        }
        $tags = $this->subjectService->getSubjectTags($id, false);
        $tags = TagResource::collection($tags);
        return $this->apiResponse($tags, 'العلامات في المادة ' . $subject->id, 200);
    }

    public function exams($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);
        if (!$subject) {
            return $this->apiResponse(null, 'المادة غير موجودة', 400);
        }
        $tags = $this->subjectService->getSubjectTags($id, true);
        $tags = TagResource::collection($tags);
        return $this->apiResponse($tags, 'الامتحانات في المادة ' . $subject->id, 200);
    }

    public function questions_edited($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);
        if (!$subject) {
            return $this->apiResponse(null, 'المادة غير موجودة', 400);
        }
        $questions = $this->subjectService->getEditedQuestions($id);
        $questions = QuestionResource::collection($questions);
        return $this->apiResponse($questions, 'الأسئلة المعدلة في المادة ' . $subject->id, 200);
    }

    public function all_subject_data($id): JsonResponse
    {
        $subject = $this->subjectService->findSubject($id);

            if(!$subject->is_active)
                return $this->apiResponse(null, 'المادة غير موجودة', 400);

        // Check if subject is locked (you can implement your own logic here)
        $is_locked = !($subject->checkStudentAccess(Auth::user()->id));

        $data = $this->subjectService->getAllSubjectData($id, $is_locked);

        // Transform data using resources
        $transformedData = [
            'subjects' => new SubjectResource($data['subjects'], $is_locked),
            'lessons' => LessonResource::collection($data['lessons']),
            'question_groups' => QuestionGroupResource::collection($data['question_groups']),
            'questions' => QuestionResource::collection($data['questions']),
            'tags' => TagResource::collection($data['tags']),
            'question_tags' => QuestionTagResource::collection($data['question_tags']),
            'is_locked' => $data['is_locked'],
        ];

        $message = $is_locked
            ? 'بيانات الدرس الأول في المادة ' . $subject->id
            : 'جميع البيانات في المادة ' . $subject->id;

        return $this->apiResponse($transformedData, $message, 200);
    }
}
