<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionReport\StoreQuestionReportRequest;
use App\Http\Requests\QuestionReport\UpdateQuestionReportStatusRequest;
use App\Models\QuestionReport;
use App\Services\QuestionReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionReportController extends Controller
{
    protected QuestionReportService $questionReportService;

    public function __construct(QuestionReportService $questionReportService)
    {
        $this->questionReportService = $questionReportService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'report_type', 'question_id']);
        $reports = $this->questionReportService->getFilteredReports($filters);

        return view('question-reports.index', compact('reports'));
    }

    public function store(StoreQuestionReportRequest $request)
    {
        try {
            $report = $this->questionReportService->createReport(
                $request->validated(),
                Auth::guard('student')->id()
            );

            return redirect()->route('admin.question-reports.index')->with('success', 'تم إرسال التقرير بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.question-reports.index')->with('error', $e->getMessage());
        }
    }

    public function show(QuestionReport $report)
    {
        $report = $this->questionReportService->getReport($report->id);
        return view('question-reports.show', compact('report'));
    }

    public function updateStatus(UpdateQuestionReportStatusRequest $request, QuestionReport $report)
    {
        $updatedReport = $this->questionReportService->updateReportStatus(
            $report->id,
            $request->validated(),
            Auth::id()
        );

        return redirect()->route('admin.question-reports.index')->with('success', 'تم تحديث حالة التقرير بنجاح');
    }

    public function destroy(QuestionReport $report)
    {
        $this->questionReportService->deleteReport($report->id);

        return redirect()->route('admin.question-reports.index')->with('success', 'تم حذف التقرير بنجاح');
    }

    public function statistics()
    {
        $stats = $this->questionReportService->getStatistics();

        return view('question-reports.statistics', compact('stats'));
    }
}
