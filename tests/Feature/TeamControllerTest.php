<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamControllerTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_should_find_best_player_for_position_and_skill()
    {
        $player1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $player1->skills()->create(['skill' => 'speed', 'value' => 85]);

        $player2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $player2->skills()->create(['skill' => 'speed', 'value' => 90]);

        $requirements = [
            [
                'position' => 'defender',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ]
        ];

        $response = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', $player2->name);
    }

    public function test_should_allow_same_skill_different_positions()
    {
        $defender = Player::create(['name' => 'Defender', 'position' => 'defender']);
        $defender->skills()->create(['skill' => 'speed', 'value' => 90]);

        $midfielder = Player::create(['name' => 'Midfielder', 'position' => 'midfielder']);
        $midfielder->skills()->create(['skill' => 'speed', 'value' => 85]);

        $requirements = [
            [
                'position' => 'defender',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ],
            [
                'position' => 'midfielder',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ]
        ];

        $response = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', $defender->name)
            ->assertJsonPath('1.name', $midfielder->name);
    }

    public function test_should_select_player_with_highest_skill_when_required_skill_not_found()
    {
        $player1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $player1->skills()->createMany([
            ['skill' => 'speed', 'value' => 70],
            ['skill' => 'strength', 'value' => 80]
        ]);

        $player2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $player2->skills()->createMany([
            ['skill' => 'stamina', 'value' => 60],
            ['skill' => 'attack', 'value' => 90]
        ]);

        $player3 = Player::create(['name' => 'Player 3', 'position' => 'midfielder']);
        $player3->skills()->createMany([
            ['skill' => 'stamina', 'value' => 60],
            ['skill' => 'defense', 'value' => 90]
        ]);

        $requirements = [
            [
                'position' => 'defender',
                'mainSkill' => 'defense',
                'numberOfPlayers' => 1
            ]
        ];

        $response = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', $player2->name);
    }

    public function test_should_return_error_when_insufficient_players()
    {
        $player = Player::create(['name' => 'Defender', 'position' => 'defender']);
        $player->skills()->create(['skill' => 'speed', 'value' => 90]);

        $requirements = [
            [
                'position' => 'defender',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 2
            ]
        ];

        $response = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Insufficient number of players for position: defender'
            ]);
    }
}
