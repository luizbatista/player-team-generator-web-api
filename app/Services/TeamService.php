<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use Illuminate\Support\Collection;
use App\Exceptions\ProcessTeamSelectionException;

class TeamService
{
    public function __construct(
        private PlayerRepository $playerRepository
    ) {}

    public function processTeamSelection(array $data): Collection
    {
        $selectedPlayers = collect();
        $usedPlayerIds = [];

        foreach ($data['requirements'] as $requirement) {
            $players = $this->playerRepository
                ->findByPositionAndSkill(
                    $requirement['position'],
                    $requirement['mainSkill'],
                    $usedPlayerIds
                )
                ->take($requirement['numberOfPlayers']);

            if ($players->count() < $requirement['numberOfPlayers']) {
                $remainingCount = $requirement['numberOfPlayers'] - $players->count();
                $usedPlayerIds = array_merge($usedPlayerIds, $players->pluck('id')->toArray());

                $playersWithAnySkill = $this->playerRepository
                    ->findByPositionWithAnySkill($requirement['position'], $usedPlayerIds)
                    ->take($remainingCount);

                $players = $players->concat($playersWithAnySkill);
            }

            if ($players->count() < $requirement['numberOfPlayers']) {
                throw new ProcessTeamSelectionException(
                    "Insufficient number of players for position: {$requirement['position']}",
                    422
                );
            }

            $usedPlayerIds = array_merge($usedPlayerIds, $players->pluck('id')->toArray());
            $selectedPlayers = $selectedPlayers->concat($players);
        }

        return $selectedPlayers;
    }
}
