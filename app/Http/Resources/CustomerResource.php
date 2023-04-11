<?php

namespace App\Http\Resources;

use GDebrauwer\Hateoas\Traits\HasLinks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    use HasLinks;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf' => $this->cpf,
            'bithdate' => $this->birthdate,
            'address' => [
                'address' => $this->address->address,
                'city' => $this->address->city,
                'state' => $this->address->state->acronym
            ],
            'gender' => $this->gender->abbreviation,
            'links' => $this->links(),
        ];
    }
}
