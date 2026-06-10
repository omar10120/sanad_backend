<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiCodeController;
use App\Http\Controllers\Api\ApiLessonController;
use App\Http\Controllers\Api\ApiQuestionController;
use App\Http\Controllers\Api\ApiSubjectController;
use App\Http\Controllers\Api\ApiTagController;
use App\Http\Controllers\Api\ApiTypeController;
use App\Http\Controllers\Api\ApiAppUpdateController;
use App\Http\Middleware\CheckSanctumToken;
use Illuminate\Support\Facades\Route;

Route::post('/app-update/check', [ApiAppUpdateController::class, 'check']);

Route::prefix('auth')->group(function () {
    Route::post('/send-verification-code', [ApiAuthController::class, 'sendVerificationCode']);
    Route::post('/verify-phone', [ApiAuthController::class, 'verifyPhone']);
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/reset-password', [ApiAuthController::class, 'resetPasswordWithPhone']);
    Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware(CheckSanctumToken::class);
    Route::get('/profile', [ApiAuthController::class, 'profile'])->middleware(CheckSanctumToken::class);
    Route::post('/update', [ApiAuthController::class, 'update'])->middleware(CheckSanctumToken::class);
    Route::post('/send-phone-change-code', [ApiAuthController::class, 'sendPhoneChangeVerificationCode'])->middleware(CheckSanctumToken::class);
    Route::post('/change-phone', [ApiAuthController::class, 'changePhoneNumber'])->middleware(CheckSanctumToken::class);
});

Route::get('type/', [ApiTypeController::class, 'index']);

Route::group(
    [
        'middleware' => [
            CheckSanctumToken::class,
        ]
    ], function(){
    Route::prefix('type')->group(function () {
        Route::get('/{id}', [ApiTypeController::class, 'show']);
        Route::get('/{id}/subjects', [ApiTypeController::class, 'subjects']);
    });

    Route::prefix('subject')->group(function () {
        Route::get('/', [ApiSubjectController::class, 'index']);
        Route::get('/all', [ApiSubjectController::class, 'allSubjectsForAdmin']);
        Route::get('/{id}', [ApiSubjectController::class, 'show']);
        Route::get('/{id}/sync', [ApiSubjectController::class, 'all_subject_data']);
        Route::get('/{id}/lessons', [ApiSubjectController::class, 'lessons']);
        Route::get('/{id}/tags', [ApiSubjectController::class, 'tags']);
        Route::get('/{id}/exams', [ApiSubjectController::class, 'exams']);
        Route::get('/{id}/questions_edited', [ApiSubjectController::class, 'questions_edited']);
    });

    Route::prefix('lesson')->group(function () {
        Route::get('/', [ApiLessonController::class, 'index']);
        Route::get('/{id}', [ApiLessonController::class, 'show']);
        Route::get('/{id}/questions', [ApiLessonController::class, 'questions']);
        Route::get('/{id}/questions/{type_id}', [ApiLessonController::class, 'questions_in_type']);
    });

    Route::prefix('tag')->group(function () {
        Route::get('/', [ApiTagController::class, 'index']);
        Route::get('/{id}', [ApiTagController::class, 'show']);
        Route::get('/{id}/questions', [ApiTagController::class, 'questions']);
    });

    Route::prefix('question')->group(function () {
        Route::get('/', [ApiQuestionController::class, 'index']);
        Route::get('/{uuid}', [ApiQuestionController::class, 'show']);
        Route::post('/report', [ApiQuestionController::class, 'storeReport']);
    });

    Route::prefix('code')->group(function () {
        Route::get('/', [ApiCodeController::class, 'Codes']);
        Route::post('/check', [ApiCodeController::class, 'checkCode']);
    });
});


