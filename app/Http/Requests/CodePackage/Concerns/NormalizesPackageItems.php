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
            foreach ($this->input('package_items', []) as $item) {
                $subjectId = ! empty($item['subject_id']) ? (int) $item['subject_id'] : null;
                $unitIds = collect($item['unit_ids'] ?? $item['unit_id'] ?? [])
                    ->flatten()
                    ->filter()
                    ->unique()
                    ->values();

                foreach ($unitIds as $unitId) {
                    $items[] = [
                        'subject_id' => $subjectId,
                        'unit_id' => (int) $unitId,
                    ];
                }
            }
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
