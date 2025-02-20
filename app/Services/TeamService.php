<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use Illuminate\Support\Collection;
use App\Exceptions\ProcessTeamSelectionException;
use Illuminate\Support\Facades\Log;

class TeamService
{
    public function __construct(
        private PlayerRepository $playerRepository
    ) {}

    public function processTeamSelection(array $data): Collection
    {
        try {
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

            $usedPlayerIds = array_merge($usedPlayerIds, $players->pluck('id')->toArray());
                $selectedPlayers = $selectedPlayers->concat($players);
            }

            return $selectedPlayers;
        } catch (\Exception $e) {
            throw new ProcessTeamSelectionException();
        }
    }
}
