<?php


// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerControllerCreateTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_should_create_player_successfully()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "attack",
                    "value" => 95
                ],
                [
                    "skill" => "speed",
                    "value" => 90
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'position',
                'playerSkills' => [
                    '*' => [
                        'id',
                        'skill',
                        'value',
                        'playerId'
                    ]
                ]
            ])
            ->assertJson([
                'name' => 'Neymar Jr',
                'position' => 'midfielder'
            ]);

        $this->assertDatabaseHas('players', [
            'name' => 'Neymar Jr',
            'position' => 'midfielder'
        ]);

        $this->assertDatabaseHas('player_skills', [
            'player_id' => $response->json('id'),
            'skill' => 'attack',
            'value' => 95
        ]);

        $this->assertDatabaseHas('player_skills', [
            'player_id' => $response->json('id'),
            'skill' => 'speed',
            'value' => 90
        ]);

        $this->assertDatabaseCount('player_skills', 2);
    }

    public function test_should_validate_required_name()
    {
        $data = [
            "name" => "",
            "position" => "midfielder",
            "playerSkills" => []
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The name field is required.'
            ]);
    }

    public function test_should_validate_required_position()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "",
            "playerSkills" => [
                [
                    "skill" => "speed",
                    "value" => 95
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The position field is required.'
            ]);
    }

    public function test_should_validate_required_skill()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "midfielder",
            "playerSkills" => []
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The player skills field is required.'
            ]);
    }

    public function test_should_validate_invalid_position()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "invalid_position",
            "playerSkills" => [
                [
                    "skill" => "speed",
                    "value" => 95
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid value for position: invalid_position'
            ]);
    }

    public function test_should_validate_invalid_skill()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "invalid_skill",
                    "value" => 95
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid value for skill: invalid_skill'
            ]);
    }

    public function test_should_validate_skill_value_range()
    {
        $data = [
            "name" => "Neymar Jr",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "speed",
                    "value" => 101
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_PLAYER_URI, $data);

        $response->assertStatus(422);
    }

}
