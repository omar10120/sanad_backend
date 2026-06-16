<?php

namespace App\Services;

use App\Models\Code;
use App\Models\CodePackage;
use App\Models\CodePackageSubject;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CodeService
{
    public function getAllPackages(): Collection
    {
        return CodePackage::withCount('codes')
            ->with(['codePackageSubjects.subject', 'codePackageSubjects.unit'])
            ->get();
    }

    public function getAllSubjects(): Collection
    {
        return Subject::orderBy('name')->get();
    }

    public function getAllUnits(): Collection
    {
        return Unit::orderBy('order')->get();
    }

    public function getAllTeachers(): Collection
    {
        return Teacher::orderBy('name')->get();
    }

    public function findPackage($id): ?CodePackage
    {
        return CodePackage::with(['codes.student', 'codePackageSubjects.subject', 'codePackageSubjects.unit'])
            ->findOrFail($id);
    }

    public function createPackage(array $packageData, array $packageItems, int $codesCount): CodePackage
    {
        return DB::transaction(function () use ($packageData, $packageItems, $codesCount) {
            $package = CodePackage::create($packageData);
            $this->syncPackageSubjects($package, $packageItems);
            $this->generateCodes($package->id, $codesCount);

            return $package->load(['codePackageSubjects.subject', 'codePackageSubjects.unit']);
        });
    }

    public function updatePackage(int $packageId, array $packageData, array $packageItems): CodePackage
    {
        return DB::transaction(function () use ($packageId, $packageData, $packageItems) {
            $package = CodePackage::findOrFail($packageId);
            $package->update($packageData);
            $this->syncPackageSubjects($package, $packageItems);

            return $package->load(['codePackageSubjects.subject', 'codePackageSubjects.unit']);
        });
    }

    public function syncPackageSubjects(CodePackage $package, array $packageItems): void
    {
        $package->codePackageSubjects()->delete();

        foreach ($packageItems as $item) {
            CodePackageSubject::create([
                'code_package_id' => $package->id,
                'subject_id' => $item['subject_id'],
                'unit_id' => $item['unit_id'],
            ]);
        }
    }

    public function formatPackageSubjectsForDisplay(CodePackage $package): array
    {
        return $package->codePackageSubjects
            ->groupBy('subject_id')
            ->map(function ($items) {
                $subject = $items->first()->subject;

                return [
                    'subject_id' => $subject?->id,
                    'subject_name' => $subject?->name,
                    'units' => $items->map(fn ($item) => [
                        'id' => $item->unit?->id,
                        'name' => $item->unit?->name,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function generateCodes($packageId, $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            do {
                $randomCode = Str::random(8);
            } while (Code::where('code', $randomCode)->exists());

            Code::create([
                'code' => $randomCode,
                'package_id' => $packageId,
            ]);
        }
    }

    public function deletePackage(int $packageId): bool
    {
        $package = CodePackage::findOrFail($packageId);

        return (bool) $package->delete();
    }

    public function deleteCode(int $codeId): bool
    {
        $code = Code::findOrFail($codeId);

        return (bool) $code->delete();
    }

    public function checkCode($code): ?Code
    {
        return Code::where('code', $code)->first();
    }

    public function assignCodeToStudent(Code $code, int $studentId): bool
    {
        if ($code->student_id && $code->student_id != $studentId) {
            return false;
        }

        $code->student_id = $studentId;

        return $code->save();
    }

    public function getStudentCodes(int $studentId): Collection
    {
        return Code::where('student_id', $studentId)
            ->whereHas('package', function ($query) {
                $query->where('expires_at', '>', now());
            })
            ->with(['package.codePackageSubjects.subject', 'package.codePackageSubjects.unit'])
            ->get();
    }

    public function isCodeUsedByAnotherStudent(Code $code, int $studentId): bool
    {
        return $code->student_id && $code->student_id != $studentId;
    }

    public function studentHasSubjectUnitAccess(int $studentId, int $subjectId, int $unitId): bool
    {
        $subject = Subject::find($subjectId);

        if (!$subject) {
            return false;
        }

        return $subject->checkStudentAccess($studentId, $unitId);
    }
}
