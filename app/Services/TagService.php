<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TagService
{
    /**
     * Get all tags ordered by subject_id
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        return Tag::orderBy('subject_id')->orderBy('order')->get();
    }

    /**
     * Get tags by subject ID
     *
     * @param int $subjectId
     * @return Collection
     */
    public function getTagsBySubject(int $subjectId): Collection
    {
        return Tag::where('subject_id', $subjectId)->orderBy('order')->get();
    }

    /**
     * Get tag by ID
     *
     * @param int $id
     * @return Tag|null
     */
    public function findTag(int $id): ?Tag
    {
        return Tag::findOrFail($id);
    }

    /**
     * Get all subjects for tag creation/editing
     *
     * @return Collection
     */
    public function getAllSubjects(): Collection
    {
        return Subject::all();
    }

    /**
     * Get subject by ID
     *
     * @param int $id
     * @return Subject|null
     */
    public function getSubjectById(int $id): ?Subject
    {
        return Subject::find($id);
    }

    /**
     * Create a new tag
     *
     * @param array $data
     * @return Tag
     */
    public function createTag(array $data): Tag
    {
        $data['is_exam'] = $data['is_exam'] ?? false;
        return Tag::create($data);
    }

    /**
     * Update tag
     *
     * @param int $id
     * @param array $data
     * @return Tag|null
     */
    public function updateTag(int $id, array $data): ?Tag
    {
        $tag = Tag::findOrFail($id);

        if (!$tag) {
            return null;
        }

        $data['is_exam'] = $data['is_exam'] ?? false;
        $tag->update($data);
        return $tag;
    }

    /**
     * Delete tag
     *
     * @param int $id
     * @return bool
     */
    public function deleteTag(int $id): bool
    {
        $tag = Tag::findOrFail($id);

        if (!$tag) {
            return false;
        }

        return $tag->delete();
    }

    /**
     * Get tag with questions
     *
     * @param int $id
     * @return Tag|null
     */
    public function getTagWithQuestions(int $id): ?Tag
    {
        return Tag::with('questions')->find($id);
    }

    /**
     * Check if tag exists
     *
     * @param int $id
     * @return bool
     */
    public function tagExists(int $id): bool
    {
        return Tag::where('id', $id)->exists();
    }

    /**
     * Get all archived tags with subject relationship
     *
     * @return Collection
     */
    public function getArchivedTags(): Collection
    {
        return Tag::onlyTrashed()->with('subject')->get();
    }

    /**
     * Get archived tags by subject ID
     *
     * @param int $subjectId
     * @return Collection
     */
    public function getArchivedTagsBySubject(int $subjectId): Collection
    {
        return Tag::onlyTrashed()->where('subject_id', $subjectId)->with('subject')->get();
    }

    /**
     * Restore a deleted tag
     *
     * @param int $id
     * @return bool
     */
    public function restoreTag(int $id): bool
    {
        $tag = Tag::onlyTrashed()->find($id);

        if (!$tag) {
            return false;
        }

        return $tag->restore();
    }

    /**
     * Permanently delete a tag
     *
     * @param int $id
     * @return bool
     */
    public function forceDeleteTag(int $id): bool
    {
        $tag = Tag::onlyTrashed()->find($id);

        if (!$tag) {
            return false;
        }

        return $tag->forceDelete();
    }
}
