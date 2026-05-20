<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'no_doc' => $this->no_doc,
            'title' => $this->title,
            'status' => $this->status,
            'is_approved' => $this->isApproved(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'uploader' => $this->latestVersion?->user?->name,
            // Tambahkan field lain yang dibutuhkan UI
        ];
    }
}
