<?php

namespace App\Http\Requests\CodePackage\Concerns;

trait NormalizesPackageItems
{
    protected function prepareForValidation(): void
    {
        $withCourse = $this->boolean('include_with_course');
        $withoutCourse = $this->boolean('include_without_course');
        $items = [];

        if ($withCourse) {
            $items = array_merge(
                $items,
                collect($this->input('package_items', []))
                    ->filter(fn ($item) => ! empty($item['unit_id']))
                    ->map(fn ($item) => [
                        'subject_id' => ! empty($item['subject_id']) ? (int) $item['subject_id'] : null,
                        'unit_id' => (int) $item['unit_id'],
                    ])
                    ->values()
                    ->all()
            );
        }

        if ($withoutCourse) {
            $items = array_merge(
                $items,
                collect($this->input('subject_ids', []))
                    ->filter()
                    ->unique()
                    ->map(fn ($subjectId) => [
                        'subject_id' => (int) $subjectId,
                        'unit_id' => null,
                    ])
                    ->values()
                    ->all()
            );
        }

        $this->merge(['package_items' => $items]);
    }
}
