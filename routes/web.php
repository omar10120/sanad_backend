<?php

use App\Http\Controllers\CourseTypeController;
use App\Http\Controllers\CodePackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionGroupController;
use App\Http\Controllers\QuestionReportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectVideoController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ArchivedTeacherController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LessonVideoController;
use App\Http\Controllers\ArchivedLessonVideoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PhoneVerificationCodeController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

require __DIR__.'/auth.php';

// Landing page route (accessible without authentication)
Route::get('/', function () {
    return view('landing');
});

// Dynamic sitemap route
Route::get('/sitemap.xml', function () {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $sitemap .= '<url>';
    $sitemap .= '<loc>' . url('/') . '</loc>';
    $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
    $sitemap .= '<changefreq>weekly</changefreq>';
    $sitemap .= '<priority>1.0</priority>';
    $sitemap .= '</url>';
    
    // Dynamic subjects (if applicable)
    try {
        $types = \App\Models\Type::where('is_active', 1)->get();
        foreach ($types as $type) {
            foreach ($type->subjects as $subject) {
                $sitemap .= '<url>';
                $sitemap .= '<loc>' . url('/subject/' . $subject->slug) . '</loc>';
                $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
                $sitemap .= '<changefreq>monthly</changefreq>';
                $sitemap .= '<priority>0.8</priority>';
                $sitemap .= '</url>';
            }
        }
    } catch (\Exception $e) {
        // If models don't exist yet, skip
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap, 200)->header('Content-Type', 'application/xml');
});

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
            'auth',
            'check.user.status'
        ]
    ], function(){

    Route::post('send-fcm-notification', [FcmController::class, 'sendFcmNotification']);

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
    Route::get('/dashboard/export-statistics', [DashboardController::class, 'exportStatistics'])->name('dashboard.export-statistics');
    Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');

    Route::resource('app-update','App\Http\Controllers\AppUpdateController');

    Route::resource('type','App\Http\Controllers\TypeController');
    Route::patch('/types/{type}/toggle', [TypeController::class, 'toggle'])
        ->name('types.toggle');
    Route::post('/types/reorder', [TypeController::class, 'reorder'])
        ->name('types.reorder');
    Route::resource('archived-type','App\Http\Controllers\ArchivedTypeController');
    Route::get('/type-subject/{type}', [TypeController::class, 'Subjects'])
        ->name('type.subject');

    Route::resource('subject','App\Http\Controllers\SubjectController');
    Route::patch('/subjects/{subject}/toggle', [SubjectController::class, 'toggle'])
        ->name('subjects.toggle');
    Route::post('/subjects/reorder', [SubjectController::class, 'reorder'])
        ->name('subjects.reorder');
    Route::get('/archived-subject/{type_id}', [App\Http\Controllers\ArchivedSubjectController::class, 'index'])
        ->name('archived-subject.type');
    Route::resource('archived-subject','App\Http\Controllers\ArchivedSubjectController');

    Route::resource('lesson','App\Http\Controllers\LessonController');
    Route::patch('/lessons/{lesson}/toggle', [LessonController::class, 'toggle'])
        ->name('lessons.toggle');
    Route::post('/subjects/{subject}/lessons/reorder', [LessonController::class, 'reorder'])
        ->name('lessons.reorder');
    Route::resource('archived-lesson','App\Http\Controllers\ArchivedLessonController');
    Route::get('/subject-lesson/{subject}', [SubjectController::class, 'lessons'])
        ->name('subject.lesson');

    Route::resource('tag','App\Http\Controllers\TagController');
    Route::post('/subjects/{subject}/tags/reorder', [TagController::class, 'reorder'])
        ->name('tags.reorder');
    Route::get('/subject-tag/{subject}', [SubjectController::class, 'tags'])
        ->name('subject.tag');

    Route::get('/course-type', [CourseTypeController::class, 'index'])->name('course-type.index');
    Route::get('/type-subject-video/{type}', [SubjectVideoController::class, 'byType'])
        ->name('type.subject-video');
    Route::post('/subject-video', [SubjectVideoController::class, 'store'])->name('subject-video.store');
    Route::patch('/subject-video/update', [SubjectVideoController::class, 'update'])->name('subject-video.update');
    Route::delete('/subject-video/destroy', [SubjectVideoController::class, 'destroy'])->name('subject-video.destroy');
    Route::patch('/subjects-video/{subjectVideo}/toggle', [SubjectVideoController::class, 'toggle'])
        ->name('subjects-video.toggle');
    Route::post('/subjects-video/reorder', [SubjectVideoController::class, 'reorder'])
        ->name('subjects-video.reorder');

    Route::get('/subject-video-teacher/{subject_video}', [TeacherController::class, 'bySubjectVideo'])
        ->name('subject-video.teacher');
    Route::post('/teacher', [TeacherController::class, 'store'])->name('teacher.store');
    Route::patch('/teacher/update', [TeacherController::class, 'update'])->name('teacher.update');
    Route::delete('/teacher/destroy', [TeacherController::class, 'destroy'])->name('teacher.destroy');
    Route::post('/subject-video/{subjectVideo}/teachers/reorder', [TeacherController::class, 'reorder'])
        ->name('teachers.reorder');
    Route::get('/archived-teacher/{subject_video}', [ArchivedTeacherController::class, 'show'])
        ->name('archived-teacher.subject-video');
    Route::patch('/archived-teacher/update', [ArchivedTeacherController::class, 'update'])
        ->name('archived-teacher.update');
    Route::delete('/archived-teacher/destroy', [ArchivedTeacherController::class, 'destroy'])
        ->name('archived-teacher.destroy');

    Route::get('/teacher-unit/{teacher}', [UnitController::class, 'byTeacher'])
        ->name('teacher.unit');
    Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');
    Route::patch('/unit/update', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/unit/destroy', [UnitController::class, 'destroy'])->name('unit.destroy');
    Route::post('/teachers/{teacher}/units/reorder', [UnitController::class, 'reorder'])
        ->name('units.reorder');

    Route::get('/unit-lesson-video/{unit}', [LessonVideoController::class, 'byUnit'])
        ->name('unit.lesson-video');
    Route::post('/lesson-video', [LessonVideoController::class, 'store'])->name('lesson-video.store');
    Route::patch('/lesson-video/update', [LessonVideoController::class, 'update'])->name('lesson-video.update');
    Route::delete('/lesson-video/destroy', [LessonVideoController::class, 'destroy'])->name('lesson-video.destroy');
    Route::post('/units/{unit}/lessons-video/reorder', [LessonVideoController::class, 'reorder'])
        ->name('lessons-video.reorder');
    Route::get('/archived-lesson-video/{unit}', [ArchivedLessonVideoController::class, 'show'])
        ->name('archived-lesson-video.unit');
    Route::patch('/archived-lesson-video/update', [ArchivedLessonVideoController::class, 'update'])
        ->name('archived-lesson-video.update');
    Route::delete('/archived-lesson-video/destroy', [ArchivedLessonVideoController::class, 'destroy'])
        ->name('archived-lesson-video.destroy');

    Route::get('/tag-subject/{subject}', [TagController::class, 'showBySubject'])
        ->name('tag.showBySubject');
    Route::resource('archived-tag','App\Http\Controllers\ArchivedTagController');
    Route::get('/archived-tag-subject/{subject}', [App\Http\Controllers\ArchivedTagController::class, 'showBySubject'])
        ->name('archived-tag.subject');

    Route::resource('question','App\Http\Controllers\QuestionController');
    Route::get('/archived-question/{question_group_id}', [App\Http\Controllers\ArchivedQuestionController::class, 'index'])
        ->name('archived-question.group');
    Route::resource('archived-question','App\Http\Controllers\ArchivedQuestionController');
    Route::get('question/create/{lesson}',[QuestionController::class,'create'])
        ->name('question.createWithLesson');
    Route::get('question/createInGroup/{group_id}',[QuestionController::class,'createInGroup'])
        ->name('question.createInGroup');
    Route::get('q/{question}/report',[QuestionController::class,'show']);
    Route::get('/subjects/{subject}/lessons-list', [QuestionController::class, 'getLessonsBySubject'])
        ->name('subjects.lessons-list');
    Route::get('/subjects/{subject}/tags-list', [QuestionController::class, 'getTagsBySubject'])
        ->name('subjects.tags-list');
    Route::delete('/questions/{question}/delete-question-photo', [QuestionController::class, 'deleteQuestionPhoto'])
        ->name('questions.delete-question-photo');
    Route::delete('/questions/{question}/delete-hint-photo', [QuestionController::class, 'deleteHintPhoto'])
        ->name('questions.delete-hint-photo');

    Route::get('/question-reports', [QuestionReportController::class, 'index'])->name('admin.question-reports.index');
    Route::get('/question-reports/{report}', [QuestionReportController::class, 'show'])->name('admin.question-reports.show');
    Route::put('/question-reports/{report}/status', [QuestionReportController::class, 'updateStatus'])->name('admin.question-reports.update-status');
    Route::delete('/question-reports/{report}', [QuestionReportController::class, 'destroy'])->name('admin.question-reports.destroy');
    Route::get('/question-reports-statistics', [QuestionReportController::class, 'statistics'])->name('admin.question-reports.statistics');

    Route::resource('question-group','App\Http\Controllers\QuestionGroupController');
    Route::put('/question-group/{questionGroup}/sort', [QuestionController::class, 'moveQuestion']);
    Route::post('/lesson/{lesson}/question-groups/reorder', [App\Http\Controllers\QuestionGroupController::class, 'reorder'])->name('question-groups.reorder');
    Route::post('/question-group/{questionGroup}/questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');
    Route::resource('archived-question-group','App\Http\Controllers\ArchivedQuestionGroupController');
    Route::get('/archived-question-group/{lesson_id}', [App\Http\Controllers\ArchivedQuestionGroupController::class, 'index'])
        ->name('archived-question-group.lesson');

    Route::get('/lessons/{lesson}/question-groups-list', [QuestionGroupController::class, 'index'])
        ->name('lessons.question-groups');


    Route::get('/lesson/{lesson}/question-group', [LessonController::class, 'questionGroups'])
        ->name('lesson.questionGroup');
    Route::put('/lesson/{lesson}/question-group', [QuestionGroupController::class, 'updateOrder']);
    Route::put('/lesson/{lesson}/question-group/sort', [QuestionGroupController::class, 'moveGroup']);


    Route::resource('question-type','App\Http\Controllers\QuestionTypeController');

    Route::resource('code-package', 'App\Http\Controllers\CodePackageController');
    Route::delete('/code/{id}', [CodePackageController::class, 'destroyCode'])->name('code.destroy');
    Route::get('export-package/{packageId}', [CodePackageController::class, 'exportPackage'])->name('code-package.export-excel');
    Route::get('export-package-pdf/{packageId}', [CodePackageController::class, 'exportPackagePdf'])->name('code-package.export-pdf');
    Route::get('/phone-verification-codes', [PhoneVerificationCodeController::class, 'index'])
        ->name('phone-verification-codes.index');

    Route::resource('roles','App\Http\Controllers\RoleController');
    Route::resource('users', 'App\Http\Controllers\UserController');
    Route::resource('archived-user', 'App\Http\Controllers\ArchivedUserController');
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile']);
    Route::post('/updatePassword', [App\Http\Controllers\UserController::class, 'updatePassword']);



    Route::resource('student','App\Http\Controllers\StudentController');
    Route::resource('archived-student', 'App\Http\Controllers\ArchivedStudentController');
    Route::get('/student-current-academic-year', [App\Http\Controllers\StudentController::class, 'currentAcademicYear'])
        ->name('student.current-academic-year');
    Route::delete('/student/{student}/device-id', [App\Http\Controllers\StudentController::class, 'deleteDeviceId'])
        ->name('student.delete-device-id');
    Route::put('/student/{student}/device-limit', [App\Http\Controllers\StudentController::class, 'updateDeviceLimit'])
        ->name('student.update-device-limit');
    Route::delete('/student/{student}/devices/{device}', [App\Http\Controllers\StudentController::class, 'removeDevice'])
        ->name('student.remove-device');
    Route::get('/student/{student}/devices', [App\Http\Controllers\StudentController::class, 'getDevices'])
        ->name('student.devices');


    // Notification routes
    Route::resource('notifications', 'App\Http\Controllers\NotificationController');
    Route::post('notifications/{notification}/send', [App\Http\Controllers\NotificationController::class, 'send'])
        ->name('notifications.send');

    Route::get('/alaa', function () {
        return view('users.mohamad-alaa-alshahrour');
    });

    Route::get('/aaa', function () {
        return view('template.cards');
    });

});

