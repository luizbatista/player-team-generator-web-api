<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerControllerDeleteTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_should_delete_player_successfully()
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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . env('API_TOKEN')
        ])->deleteJson(self::REQ_PLAYER_URI . $player->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('players', [
            'id' => $player->id
        ]);

        $this->assertDatabaseMissing('player_skills', [
            'player_id' => $player->id
        ]);
    }

    public function test_should_return_404_when_player_not_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . env('API_TOKEN')
        ])->deleteJson(self::REQ_PLAYER_URI . '999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Player not found'
            ]);
    }
}
