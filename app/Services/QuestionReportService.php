<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class QuestionReportService
{
    /**
     * Get filtered question reports with pagination
     */
    public function getFilteredReports(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = QuestionReport::with(['question', 'student', 'reviewer']);

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by report type
        if (isset($filters['report_type'])) {
            $query->where('report_type', $filters['report_type']);
        }

        // Filter by question
        if (isset($filters['question_id'])) {
            $query->where('question_id', $filters['question_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new question report
     */
    public function createReport(array $data, int $studentId): QuestionReport
    {
        $question = Question::with('questionGroup.lesson')->findOrFail($data['question_id']);
        $subjectId = optional($question->questionGroup->lesson)->subject_id;

        if (!$subjectId) {
            throw new \Exception('لا يمكن تحديد المادة المرتبطة بالسؤال، يرجى مراجعة الإدارة.');
        }

        $reportData = [
            'question_id' => $data['question_id'],
            'report_type' => $data['report_type'],
            'description' => $data['description'],
            'student_id' => $studentId,
            'subject_id' => $subjectId,
        ];

        return QuestionReport::create($reportData);
    }

    /**
     * Get a single question report with relationships
     */
    public function getReport(int $reportId): QuestionReport
    {
        return QuestionReport::with(['question', 'student', 'reviewer'])->findOrFail($reportId);
    }

    /**
     * Update report status
     */
    public function updateReportStatus(int $reportId, array $data, int $reviewerId): QuestionReport
    {
        $report = QuestionReport::findOrFail($reportId);
        
        $report->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        return $report->fresh();
    }

    /**
     * Delete a question report
     */
    public function deleteReport(int $reportId): bool
    {
        $report = QuestionReport::findOrFail($reportId);
        return $report->delete();
    }

    /**
     * Get statistics for question reports
     */
    public function getStatistics(): array
    {
        return [
            'total' => QuestionReport::count(),
            'pending' => QuestionReport::pending()->count(),
            'resolved' => QuestionReport::resolved()->count(),
            'by_type' => QuestionReport::selectRaw('report_type, count(*) as count')
                ->groupBy('report_type')
                ->pluck('count', 'report_type')
                ->toArray(),
        ];
    }

    /**
     * Get reports by student
     */
    public function getReportsByStudent(int $studentId, int $perPage = 20): LengthAwarePaginator
    {
        return QuestionReport::with(['question', 'subject'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get reports by question
     */
    public function getReportsByQuestion(int $questionId, int $perPage = 20): LengthAwarePaginator
    {
        return QuestionReport::with(['student', 'reviewer'])
            ->where('question_id', $questionId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
} 