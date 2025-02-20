<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamPlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $skills = $this->skills;

        return [
            'name' => $this->name,
            'position' => $this->position,
            'playerSkills' => $skills->map(function ($skill) {
                return [
                    'skill' => $skill->skill,
                    'value' => $skill->value
                ];
            })
        ];
    }
}
