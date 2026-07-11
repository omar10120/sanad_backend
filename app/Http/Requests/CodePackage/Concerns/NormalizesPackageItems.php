<?php

namespace App\Http\Requests\CodePackage\Concerns;

use Illuminate\Validation\Validator;

trait NormalizesPackageItems
{
    protected function prepareForValidation(): void
    {
        $withCourse = $this->boolean('include_with_course');
        $withoutCourse = $this->boolean('include_without_course');
        $items = [];

        if ($withCourse) {
            $courseItems = collect($this->input('package_items', []))
                ->filter(fn ($item) => ! empty($item['subject_id']) && ! empty($item['unit_id']))
                ->map(fn ($item) => [
                    'subject_id' => (int) $item['subject_id'],
                    'unit_id' => (int) $item['unit_id'],
                ])
                ->values()
                ->all();

            $this->merge(['_course_package_items' => $courseItems]);

            $items = array_merge($items, $courseItems);
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

    protected function packageModeRules(): array
    {
        return [
            'include_with_course' => ['sometimes', 'boolean'],
            'include_without_course' => ['sometimes', 'boolean'],
            'package_items' => ['nullable', 'array'],
            'package_items.*.subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'package_items.*.unit_id' => ['nullable', 'integer', 'exists:units,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $withCourse = $this->boolean('include_with_course');
            $withoutCourse = $this->boolean('include_without_course');

            // if (! $withCourse && ! $withoutCourse) {
            //     $validator->errors()->add(
            //         'include_with_course',
            //         trans('main_trans.At_least_one_content_type_required')
            //     );

            //     return;
            // }

            if ($withCourse) {
                $courseItems = collect($this->input('_course_package_items', []));

                // if ($courseItems->isEmpty()) {
                //     $validator->errors()->add(
                //         'package_items',
                //         trans('main_trans.At_least_one_subject_unit_required')
                //     );
                // }
            }

            if ($withoutCourse) {
                $subjectIds = collect($this->input('subject_ids', []))->filter();

                if ($subjectIds->isEmpty()) {
                    $validator->errors()->add(
                        'subject_ids',
                        trans('main_trans.At_least_one_subject_required')
                    );
                }
            }
        });
    }
}
