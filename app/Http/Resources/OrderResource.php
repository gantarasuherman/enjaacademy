<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'amount' => $this->amount,
            'status' => $this->status,
            'checkout_url' => $this->checkout_url,
            'qr_url' => $this->qr_url,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'module' => $this->whenLoaded('learningModule', fn () => [
                'id' => $this->learningModule->id,
                'name' => $this->learningModule->name,
                'slug' => $this->learningModule->slug,
            ]),
        ];
    }
}
