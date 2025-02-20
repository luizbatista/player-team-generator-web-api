<?php

namespace App\Http\Requests;

use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TeamProcessRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            '*' => 'required|array',
            '*.position' => 'required|string|in:' . implode(',', $this->getValidPositions()),
            '*.mainSkill' => 'required|string|in:' . implode(',', $this->getValidSkills()),
            '*.numberOfPlayers' => 'required|integer|min:1'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $requirements = $this->all();
            $combinations = [];

            foreach ($requirements as $index => $requirement) {
                $key = $requirement['position'] . '_' . $requirement['mainSkill'];

                if (in_array($key, $combinations)) {
                    $validator->errors()->add(
                        $index,
                        'Duplicate position and skill combination is not allowed'
                    );
                    return;
                }

                $combinations[] = $key;
            }
        });
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
            '*.position.in' => 'Invalid value for position: :input',
            '*.mainSkill.in' => 'Invalid value for skill: :input',
            '*.numberOfPlayers.min' => 'Number of players must be at least 1'
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
