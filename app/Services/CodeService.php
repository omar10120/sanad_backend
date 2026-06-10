<?php

namespace App\Services;

use App\Models\Code;
use App\Models\CodePackage;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CodeService
{
    /**
     * Get all code packages with code count
     *
     * @return Collection
     */
    public function getAllPackages(): Collection
    {
        return CodePackage::withCount('codes')->get();
    }

    /**
     * Get all subjects for package creation/editing
     *
     * @return Collection
     */
    public function getAllSubjects(): Collection
    {
        return Subject::all();
    }

    /**
     * Get package by ID
     *
     * @param int $id
     * @return CodePackage|null
     */
    public function findPackage($id): ?CodePackage
    {
        return CodePackage::findOrFail($id);
    }

    /**
     * Create a new code package with generated codes
     *
     * @param array $packageData
     * @param array $subjectIds
     * @param int $codesCount
     * @return CodePackage
     */
    public function createPackage(array $packageData, array $subjectIds, int $codesCount): CodePackage
    {
        return DB::transaction(function () use ($packageData, $subjectIds, $codesCount) {
            $package = CodePackage::create($packageData);
            $package->subjects()->sync($subjectIds);
            $this->generateCodes($package->id, $codesCount);

            return $package;
        });
    }

    /**
     * Generate unique codes for a package
     *
     * @param int $packageId
     * @param int $count
     * @return void
     */
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

    /**
     * Delete a code package
     *
     * @param int $packageId
     * @return bool
     */
    public function deletePackage(int $packageId): bool
    {
        $package = CodePackage::findOrFail($packageId);
        if ($package) {
            return $package->delete();
        }
        return false;
    }

    /**
     * Update a code package
     *
     * @param int $packageId
     * @param array $packageData
     * @param array $subjectIds
     * @return CodePackage
     */
    public function updatePackage(int $packageId, array $packageData, array $subjectIds): CodePackage
    {
        return DB::transaction(function () use ($packageId, $packageData, $subjectIds) {
            // Find and update the package
            $package = CodePackage::findOrFail($packageId);
            $package->update($packageData);

            // Sync subjects
            $package->subjects()->sync($subjectIds);

            return $package;
        });
    }

    /**
     * Delete a specific code
     *
     * @param int $codeId
     * @return bool
     */
    public function deleteCode(int $codeId): bool
    {
        $code = Code::findOrFail($codeId);
        if ($code) {
            return $code->delete();
        }
        return false;
    }

    /**
     * Check if a code exists and is valid
     *
     * @param string $code
     * @return Code|null
     */
    public function checkCode($code): ?Code
    {
        return Code::where('code', $code)->first();
    }

    /**
     * Assign a code to a student
     *
     * @param Code $code
     * @param int $studentId
     * @return bool
     */
    public function assignCodeToStudent(Code $code, int $studentId): bool
    {
        if ($code->student_id && $code->student_id != $studentId) {
            return false; // Code already used by another student
        }

        $code->student_id = $studentId;
        return $code->save();
    }

    /**
     * Get codes used by a student
     *
     * @param int $studentId
     * @return Collection
     */
    public function getStudentCodes(int $studentId): Collection
    {
        return Code::where('student_id', $studentId)
            ->whereHas('package', function ($query) {
                $query->where('expires_at', '>', now());
            })
            ->get();
    }

    /**
     * Check if code is already used by another student
     *
     * @param Code $code
     * @param int $studentId
     * @return bool
     */
    public function isCodeUsedByAnotherStudent(Code $code, int $studentId): bool
    {
        return $code->student_id && $code->student_id != $studentId;
    }
}
