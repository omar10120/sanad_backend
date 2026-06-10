<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Http\Resources\QuestionResource;
use App\Models\Type;
use App\Services\LessonService;
use Illuminate\Http\Request;

class ApiLessonController extends Controller
{
    use ApiResponseTrait;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function index(){
        $lessons = LessonResource::collection($this->lessonService->getAllLessons());
        return $this->apiResponse($lessons, 'جميع الدروس', 200);
    }

    public function show($id){
        $lesson = $this->lessonService->findLesson($id);
        if(!$lesson){
            return $this->apiResponse(null, 'الدرس غير موجود!', 400);
        }
        $lesson = new LessonResource($lesson);
        return $this->apiResponse($lesson, 'الدرس '.$lesson->id, 200);
    }

    public function questions($id){
        $lesson = $this->lessonService->findLesson($id);
        if(!$lesson){
            return $this->apiResponse(null, 'الدرس غير موجود!', 400);
        }
        $questions = QuestionResource::collection($this->lessonService->getLessonQuestions($id));
        return $this->apiResponse($questions, 'الأسئلة في الدرس '.$lesson->id, 200);
    }

    public function questions_in_type($id, $type_id){
        $lesson = $this->lessonService->findLesson($id);
        $type = Type::find($type_id);
        if(!$lesson){
            return $this->apiResponse(null, 'الدرس غير موجود!', 400);
        }
        if(!$type){
            return $this->apiResponse(null, 'النوع غير موجود!', 400);
        }
        
        $questions = $this->lessonService->getLessonQuestionsByType($id, $type_id);
        if(!sizeof($questions)){
            return $this->apiResponse(null, 'هذا النوع غير متوفر في هذا الدرس!', 400);
        }
        $questions = QuestionResource::collection($questions);
        return $this->apiResponse($questions, 'الأسئلة في الدرس '.$lesson->id, 200);
    }
}
