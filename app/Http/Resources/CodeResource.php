<?php

namespace App\Http\Resources;

use App\Services\CodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->package->loadMissing(['codePackageSubjects.subject', 'codePackageSubjects.unit']);

        return [
            'id' => $this->id,
            'code' => $this->code,
            'package_id' => $this->package_id,
            'subjects' => app(CodeService::class)->formatPackageSubjectsForDisplay($this->package),
            'expires_at' => $this->package->expires_at,
        ];
    }
}
