<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerControllerListingTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_should_list_empty_players()
    {
        $response = $this->getJson(self::REQ_PLAYER_URI);

        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_should_list_players_with_skills()
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

        $response = $this->getJson(self::REQ_PLAYER_URI);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
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
                ]
            ])
            ->assertJson([
                [
                    'name' => 'Neymar Jr',
                    'position' => 'midfielder',
                    'playerSkills' => [
                        [
                            'skill' => 'speed',
                            'value' => 95
                        ],
                        [
                            'skill' => 'attack',
                            'value' => 90
                        ]
                    ]
                ]
            ]);
    }
}
