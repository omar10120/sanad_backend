<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Type;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService
{
    /**
     * Get all students
     *
     * @return Collection
     */
    public function getAllStudents(): Collection
    {
        return Student::all();
    }

    /**
     * Get students by academic year
     *
     * @param string $academicYear
     * @return Collection
     */
    public function getStudentsByAcademicYear(string $academicYear): Collection
    {
        return Student::where('academic_year', $academicYear)->get();
    }

    /**
     * Get all types for student creation/editing
     *
     * @return Collection
     */
    public function getAllTypes(): Collection
    {
        return Type::all();
    }

    /**
     * Get student by ID
     *
     * @param int $id
     * @return Student|null
     */
    public function findStudent($id): ?Student
    {
        return Student::findOrFail($id);
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null): bool
    {
        $query = Student::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Validate password
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password): bool
    {
        return Str::length($password) >= 8;
    }

    /**
     * Validate password confirmation
     *
     * @param string $password
     * @param string $passwordConfirmation
     * @return bool
     */
    public function validatePasswordConfirmation($password, $passwordConfirmation): bool
    {
        return $password === $passwordConfirmation;
    }

    /**
     * Create a new student
     *
     * @param array $studentData
     * @param Request $request
     * @return Student
     */
    public function createStudent($studentData, Request $request): Student
    {
        // Hash password
        $studentData['password'] = Hash::make($studentData['password']);
        
        // Set default max_devices if not provided
        $studentData['max_devices'] = $studentData['max_devices'] ?? 1;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $studentData['photo'] = $request->file('photo')->getClientOriginalName();
        } else {
            $studentData['photo'] = null;
        }
        
     
     

        $studentData['phone_verified_at'] = now();
        $student = Student::create($studentData);

        // Move photo to storage if uploaded
        if ($request->hasFile('photo')) {
            $photoName = $studentData['photo'];
            $request->photo->move(public_path('assets/image/Students/' . $student->id), $photoName);
        }

        return $student;
    }

    /**
     * Update an existing student
     *
     * @param Student $student
     * @param array $studentData
     * @param Request $request
     * @return Student
     */
    public function updateStudent(Student $student, $studentData, Request $request): Student
    {
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $photoName = $image->getClientOriginalName();

            // Delete old photo if exists
            $path = 'assets/image/Students/' . $student->id . '/' . $student->photo;
            if (file_exists($path)) {
                unlink($path);
            }

            $student->update(['photo' => $photoName]);
            $request->photo->move(public_path('assets/image/Students/' . $student->id), $photoName);
        }

        $student->markPhoneAsVerified();

        // Update other fields
        $student->update([
            'first_name' => $studentData['first_name'],
            'father_name' => $studentData['father_name'],
            'last_name' => $studentData['last_name'],
            'email' => $studentData['email'],
            'phone' => $studentData['phone'],
            'city' => $studentData['city'],
            'school' => $studentData['school'],
            'type_id' => $studentData['type_id'],
            'status' => $studentData['status'],
            'max_devices' => $studentData['max_devices'] ?? $student->max_devices,
        ]);

        return $student;
    }

    /**
     * Delete a student
     *
     * @param int $id
     * @return bool
     */
    public function deleteStudent($id): bool
    {
        $student = Student::findOrFail($id);

        // Delete photo if exists
        $path = 'assets/image/Students/' . $id . '/' . $student->photo;
        if (file_exists($path)) {
            unlink($path);
        }

        return $student->delete();
    }

    /**
     * Delete device_id for a student
     *
     * @param int $id
     * @return bool
     */
    public function deleteDeviceId($id): bool
    {
        $student = Student::findOrFail($id);
        return $student->update(['device_id' => null]);
    }
}
