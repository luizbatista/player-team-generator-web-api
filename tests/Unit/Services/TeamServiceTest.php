<?php

namespace Tests\Unit\Services;

use App\Services\TeamService;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    private TeamService $teamService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamService = app(TeamService::class);
    }

    public function test_should_select_team_with_required_skills()
    {
        $player1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $player1->skills()->createMany([
            ['skill' => 'defense', 'value' => 90],
            ['skill' => 'speed', 'value' => 80]
        ]);

        $player2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $player2->skills()->createMany([
            ['skill' => 'defense', 'value' => 85],
            ['skill' => 'speed', 'value' => 75]
        ]);

        $data = [
            'requirements' => [
                [
                    'position' => 'defender',
                    'mainSkill' => 'defense',
                    'numberOfPlayers' => 2
                ]
            ]
        ];

        $result = $this->teamService->processTeamSelection($data);

        $this->assertCount(2, $result);
        $this->assertEquals($player1->id, $result[0]->id);
        $this->assertEquals($player2->id, $result[1]->id);
    }

    public function test_should_select_players_with_any_skill_when_main_skill_not_found()
    {
        $player1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $player1->skills()->create(['skill' => 'speed', 'value' => 90]);

        $data = [
            'requirements' => [
                [
                    'position' => 'defender',
                    'mainSkill' => 'defense',
                    'numberOfPlayers' => 1
                ]
            ]
        ];

        $result = $this->teamService->processTeamSelection($data);

        $this->assertCount(1, $result);
        $this->assertEquals($player1->id, $result[0]->id);
    }
}
