<?php

namespace App\Transformers;

class CompanyInvitationTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'sent_at' => $this->created_at->diffForHumans(),
        ];
    }

    public function withCompany()
    {
        return $this->state(function (array $attributes) {
            return [];
        });
    }
}
