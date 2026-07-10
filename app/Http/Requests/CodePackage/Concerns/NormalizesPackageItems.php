<?php

namespace App\Http\Requests\CodePackage\Concerns;

trait NormalizesPackageItems
{
    protected function prepareForValidation(): void
    {
        if ($this->input('package_mode') !== 'without_course') {
            return;
        }

        $subjectIds = $this->input('subject_ids', []);

        if (! is_array($subjectIds)) {
            return;
        }

        $this->merge([
            'package_items' => collect($subjectIds)
                ->filter()
                ->unique()
                ->map(fn ($subjectId) => [
                    'subject_id' => (int) $subjectId,
                    'unit_id' => null,
                ])
                ->values()
                ->all(),
        ]);
    }

    protected function packageModeRules(): array
    {
        $rules = [
            'package_mode' => ['required', 'in:with_course,without_course'],
        ];

        if ($this->input('package_mode') === 'without_course') {
            $rules['subject_ids'] = ['nullable', 'array', 'min:1'];
            $rules['subject_ids.*'] = ['required', 'integer', 'exists:subjects,id'];
            $rules['package_items'] = ['nullable', 'array', 'min:1'];
            $rules['package_items.*.subject_id'] = ['nullable', 'integer', 'exists:subjects,id'];
            $rules['package_items.*.unit_id'] = ['nullable'];

            return $rules;
        }

        $rules['package_items'] = ['nullable', 'array', 'min:1'];
        $rules['package_items.*.subject_id'] = ['nullable', 'integer', 'exists:subjects,id'];
        $rules['package_items.*.unit_id'] = ['nullable', 'integer', 'exists:units,id'];

        return $rules;
    }
}
