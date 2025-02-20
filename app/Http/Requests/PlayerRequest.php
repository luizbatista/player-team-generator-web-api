<?php

namespace App\Http\Requests;

use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlayerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'position' => 'required|string|in:' . implode(',', $this->getValidPositions()),
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => 'required|string|in:' . implode(',', $this->getValidSkills()),
            'playerSkills.*.value' => 'required|integer|min:0|max:100'
        ];
    }

    private function getValidPositions(): array
    {
        return array_map(fn($case) => $case->value, PlayerPosition::cases());
    }

    private function getValidSkills(): array
    {
        return array_map(fn($case) => $case->value, PlayerSkill::cases());
    }

    public function messages()
    {
        return [
            'position.in' => 'Invalid value for position: :input',
            'playerSkills.min' => 'The player must have at least one skill',
            'playerSkills.*.skill.in' => 'Invalid value for skill: :input'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors()->first()
            ], 422)
        );
    }
}
