<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'domain_id' => $this->id,
            'domain_name' => $this->domain_name,
            'status' => $this->status,
            'date_last_crawled' => $this->date_last_crawled,
            'no_of_items' => $this->no_of_items,
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'domain_items' => $this->whenLoaded('domainItems', function () {
                return DomainItemResource::collection($this->domain_items);
            })
        ];
    }
}
