<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerControllerUpdateTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_should_update_player_successfully()
    {
        $player = Player::create([
            'name' => 'Neymar Jr',
            'position' => 'midfielder'
        ]);

        $player->skills()->createMany([
            [
                'skill' => 'speed',
                'value' => 95
            ],
            [
                'skill' => 'attack',
                'value' => 90
            ]
        ]);

        $data = [
            "name" => "Neymar Updated",
            "position" => "forward",
            "playerSkills" => [
                [
                    "skill" => "attack",
                    "value" => 60
                ],
                [
                    "skill" => "speed",
                    "value" => 80
                ]
            ]
        ];

        $response = $this->putJson(self::REQ_PLAYER_URI . $player->id, $data);

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
                'name' => 'Neymar Updated',
                'position' => 'forward'
            ]);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'name' => 'Neymar Updated',
            'position' => 'forward'
        ]);
    }

    public function test_should_return_404_when_player_not_found()
    {
        $data = [
            "name" => "test",
            "position" => "defender",
            "playerSkills" => [
                [
                    "skill" => "attack",
                    "value" => 60
                ]
            ]
        ];

        $response = $this->putJson(self::REQ_PLAYER_URI . '999', $data);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Player not found'
            ]);
    }

    public function test_should_validate_invalid_position()
    {
        $player = Player::create([
            'name' => 'Neymar Jr',
            'position' => 'midfielder'
        ]);

        $data = [
            "name" => "test",
            "position" => "invalid_position",
            "playerSkills" => [
                [
                    "skill" => "attack",
                    "value" => 60
                ]
            ]
        ];

        $response = $this->putJson(self::REQ_PLAYER_URI . $player->id, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid value for position: invalid_position'
            ]);
    }

    public function test_should_validate_invalid_skill()
    {
        $player = Player::create([
            'name' => 'Neymar Jr',
            'position' => 'midfielder'
        ]);

        $data = [
            "name" => "test",
            "position" => "defender",
            "playerSkills" => [
                [
                    "skill" => "invalid_skill",
                    "value" => 60
                ]
            ]
        ];

        $response = $this->putJson(self::REQ_PLAYER_URI . $player->id, $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid value for skill: invalid_skill'
            ]);
    }
}
