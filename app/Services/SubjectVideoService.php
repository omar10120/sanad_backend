<?php

namespace App\Services;

use App\Http\Resources\TypeHasSubjectVideoResource;
use App\Models\SubjectVideo;
use App\Models\Type;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SubjectVideoService
{
    public function getAllSubjectVideos(): Collection
    {
        return SubjectVideo::orderBy('name')->get();
    }

    public function getTypeWithSubjectVideos(int $typeId): Type
    {
        return Type::with(['subjectVideos.teachers', 'subjectVideos.types'])->findOrFail($typeId);
    }

    public function createSubjectVideo(array $data): SubjectVideo
    {
        $subjectVideo = SubjectVideo::create($data);

        if (isset($data['types'])) {
            $subjectVideo->types()->sync($data['types']);
        }

        return $subjectVideo;
    }

    public function findSubjectVideo(int $id): SubjectVideo
    {
        return SubjectVideo::findOrFail($id);
    }

    public function updateSubjectVideo(int $id, array $data): bool
    {
        $subjectVideo = SubjectVideo::findOrFail($id);
        $subjectVideo->update($data);

        if (isset($data['types'])) {
            $subjectVideo->types()->sync($data['types']);
        }

        return true;
    }

    public function deleteSubjectVideo(int $id): array
    {
        $subjectVideo = SubjectVideo::findOrFail($id);

        if (!$subjectVideo->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Subject_video_has_related_data'),
            ];
        }

        try {
            $subjectVideo->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Subject_video_delete_successfully'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function toggleSubjectVideoStatus(SubjectVideo $subjectVideo): bool
    {
        return $subjectVideo->update(['is_active' => !$subjectVideo->is_active]);
    }

    public function getAllTypes(): Collection
    {
        return Type::orderBy('order')->get();
    }

     /**
     * Get type_has_subject relationships for a student.
     */


     public function getTypeHasSubjectVideoRelationshipsByType(int $typeId): AnonymousResourceCollection
     {
         $relationships = DB::table('type_has_subject_video')
             ->join('types', 'type_has_subject_video.type_id', '=', 'types.id')
             ->where('type_id', $typeId)
             ->join('subjects_video', 'type_has_subject_video.subject_video_id', '=', 'subjects_video.id')
             ->where('subjects_video.is_active', 1)
             ->select(
                 'type_has_subject_video.type_id',
                 'type_has_subject_video.subject_video_id',
                 'types.id as type_id',
                 'types.name as type_name',
                 'subjects_video.id as subject_video_id',
                 'subjects_video.name as subject_video_name',
             )
             ->get();
 
             return TypeHasSubjectVideoResource::collection($relationships);
     }
 
}
