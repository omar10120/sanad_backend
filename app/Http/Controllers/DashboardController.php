<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Question;
use App\Models\Student;
use App\Models\QuestionReport;
use App\Models\Type;
use App\Models\Lesson;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard with comprehensive statistics
     * 
     * @param Request $request
     * @return Renderable
     */
    public function dashboard(Request $request): Renderable
    {
        // Get academic year from request, default to current year (2025-2026)
        $academicYear = $request->get('academic_year', '2025-2026');
        
        // Validate academic year
        if (!in_array($academicYear, ['2024-2025', '2025-2026'])) {
            $academicYear = '2025-2026';
        }

        // Cache statistics for 1 hour to improve performance (with academic year in cache key)
        $cacheKey = 'dashboard_stats_' . $academicYear;
        $statistics = Cache::remember($cacheKey, 3600, function () use ($academicYear) {
            return $this->getDashboardStatistics($academicYear);
        });

        return view('dash', compact('statistics', 'academicYear'));
    }

    /**
     * Get comprehensive dashboard statistics
     *
     * @param string|null $academicYear
     * @return array
     */
    private function getDashboardStatistics(?string $academicYear = null): array
    {
        return [
            // General Overview Cards
            'total_subjects' => $this->getTotalSubjects(),
            'total_questions' => $this->getTotalQuestions(),
            'total_lessons' => $this->getTotalLessons(),
            'total_students' => $this->getTotalStudents($academicYear),

            // Student Distribution by Cities
            'students_by_city' => $this->getStudentsByCity($academicYear),

            // Student Distribution by Certificate Types
            'students_by_type' => $this->getStudentsByType($academicYear),

            // Subject Statistics
            'popular_subjects' => $this->getPopularSubjects(),
            'questions_per_subject' => $this->getQuestionsPerSubject(),
            'subscribers_per_subject' => $this->getSubscribersPerSubject($academicYear),
            'lessons_per_subject' => $this->getLessonsPerSubject(),

            // Recent Activity
            'recent_students_1' => $this->getRecentStudents(1, $academicYear),
            'recent_students_7' => $this->getRecentStudents(7, $academicYear),
            'recent_students_30' => $this->getRecentStudents(30, $academicYear),
            'recent_students' => $this->getRecentStudents(30, $academicYear), // Keep for backward compatibility if needed
            'pending_reports' => $this->getPendingReports(),
            'resolved_reports' => $this->getResolvedReports(),
            'recent_reports' => $this->getRecentReports(),
        ];
    }

    /**
     * Get total active subjects count
     */
    private function getTotalSubjects(): int
    {
        return Subject::where('is_active', true)->count();
    }

    /**
     * Get total questions count
     */
    private function getTotalQuestions(): int
    {
        return Question::count();
    }

    /**
     * Get total active Lessons count
     */
    private function getTotalLessons(): int
    {
        return Lesson::where('is_active', true)->count();
    }

    /**
     * Get total students count
     *
     * @param string|null $academicYear
     * @return int
     */
    private function getTotalStudents(?string $academicYear = null): int
    {
        $query = Student::query();
        
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }
        
        return $query->count();
    }


    /**
     * Get student distribution by cities
     *
     * @param string|null $academicYear
     * @return array
     */
    private function getStudentsByCity(?string $academicYear = null): array
    {
        $query = Student::select('city', DB::raw('count(*) as count'))
            ->whereNotNull('city');
            
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }
        
        $cityData = $query->groupBy('city')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();

        // Add city names in Arabic
        $citiesWithNames = [];
        foreach ($cityData as $city) {
            $citiesWithNames[] = [
                'city_key' => $city['city'],
                'city_name' => Student::Cities[$city['city']] ?? $city['city'],
                'count' => $city['count']
            ];
        }

        return $citiesWithNames;
    }

    /**
     * Get student distribution by certificate types
     *
     * @param string|null $academicYear
     * @return array
     */
    private function getStudentsByType(?string $academicYear = null): array
    {
        $query = Student::join('types', 'students.type_id', '=', 'types.id')
            ->select('types.name', 'types.id', DB::raw('count(*) as count'));
            
        if ($academicYear) {
            $query->where('students.academic_year', $academicYear);
        }
        
        return $query->groupBy('types.id', 'types.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get most popular subjects by enrollment
     */
    private function getPopularSubjects(): array
    {
        return Subject::withCount(['codePackages as enrolled_students' => function ($query) {
                $query->join('codes', 'code_packages.id', '=', 'codes.package_id')
                    ->whereNotNull('codes.student_id');
            }])
            ->where('is_active', true)
            ->orderBy('enrolled_students', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get questions count per subject
     */
    private function getQuestionsPerSubject(): array
    {
        return Subject::withCount(['lessons as questions_count' => function ($query) {
                $query->join('question_groups', 'lessons.id', '=', 'question_groups.lesson_id')
                    ->join('questions', 'question_groups.id', '=', 'questions.question_group_id');
            }])
            ->where('is_active', true)
            ->orderBy('questions_count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get subscribers count per subject (students using codes)
     *
     * @param string|null $academicYear
     * @return array
     */
    private function getSubscribersPerSubject(?string $academicYear = null): array
    {
        return Subject::withCount(['codePackages as subscribers_count' => function ($query) use ($academicYear) {
                $query->join('codes', 'code_packages.id', '=', 'codes.package_id')
                    ->join('students', 'codes.student_id', '=', 'students.id')
                    ->whereNotNull('codes.student_id');
                    
                if ($academicYear) {
                    $query->where('students.academic_year', $academicYear);
                }
            }])
            ->where('is_active', true)
            ->orderBy('subscribers_count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get lessons count per subject
     */
    private function getLessonsPerSubject(): array
    {
        return Subject::withCount(['lessons'])
            ->where('is_active', true)
            ->orderBy('lessons_count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get recent students count
     *
     * @param int $days
     * @param string|null $academicYear
     * @return int
     */
    private function getRecentStudents(int $days = 30, ?string $academicYear = null): int
    {
        $query = Student::where('created_at', '>=', Carbon::now()->subDays($days));
        
        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }
        
        return $query->count();
    }


    /**
     * Get pending question reports count
     */
    private function getPendingReports(): int
    {
        return QuestionReport::where('status', 'pending')->count();
    }

    /**
     * Get resolved question reports count
     */
    private function getResolvedReports(): int
    {
        return QuestionReport::where('status', 'resolved')->count();
    }
    /**
     * Get recent question reports (last 7 days)
     */
    private function getRecentReports(): array
    {
        return QuestionReport::with(['question', 'student', 'subject'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    /**
     * Clear dashboard statistics cache
     */
    public function clearCache(Request $request)
    {
        $academicYear = $request->get('academic_year', '2025-2026');
        Cache::forget('dashboard_stats_' . $academicYear);
        Cache::forget('dashboard_stats_2024-2025');
        Cache::forget('dashboard_stats_2025-2026');
        return redirect()->back()->with('success', 'Dashboard cache cleared successfully!');
    }
    
    /**
     * Get dashboard statistics as JSON (for AJAX requests)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics(Request $request)
    {
        $academicYear = $request->get('academic_year', '2025-2026');
        
        // Validate academic year
        if (!in_array($academicYear, ['2024-2025', '2025-2026'])) {
            $academicYear = '2025-2026';
        }
        
        $cacheKey = 'dashboard_stats_' . $academicYear;
        $statistics = Cache::remember($cacheKey, 3600, function () use ($academicYear) {
            return $this->getDashboardStatistics($academicYear);
        });
        
        return response()->json($statistics);
    }

    /**
     * Export dashboard statistics as JSON
     */
    public function exportStatistics()
    {
        $statistics = $this->getDashboardStatistics();

        return response()->json($statistics)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="dashboard_statistics_' . date('Y-m-d_H-i-s') . '.json"');
    }
}
