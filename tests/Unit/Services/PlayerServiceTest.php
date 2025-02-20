<?php

namespace Tests\Unit\Services;

use App\Services\PlayerService;
use App\Models\Player;
use App\Exceptions\PlayerNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlayerService $playerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->playerService = app(PlayerService::class);
    }

    public function test_should_create_player_with_skills()
    {
        $data = [
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
        ];

        $player = $this->playerService->createPlayer($data);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('Neymar Jr', $player->name);
        $this->assertEquals('midfielder', $player->position->value);
        $this->assertCount(2, $player->skills);
    }

    public function test_should_throw_exception_when_player_not_found()
    {
        $this->expectException(PlayerNotFoundException::class);
        $this->playerService->findPlayerOrFail(999);
    }

    public function test_should_delete_player_and_skills()
    {
        $player = Player::create([
            'name' => 'Neymar Jr',
            'position' => 'midfielder'
        ]);

        $player->skills()->createMany([
            ['skill' => 'speed', 'value' => 95],
            ['skill' => 'attack', 'value' => 90]
        ]);

        $this->playerService->deletePlayer($player->id);

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('player_skills', ['player_id' => $player->id]);
    }
}
