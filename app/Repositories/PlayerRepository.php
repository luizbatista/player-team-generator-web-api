<?php

namespace App\Repositories;

use App\Models\Player;
use Illuminate\Support\Collection;

class PlayerRepository
{
    public function findByPositionAndSkill(string $position, string $skill, array $excludeIds = []): Collection
    {
        return Player::where('position', $position)
            ->whereNotIn('players.id', $excludeIds)
            ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
            ->where('player_skills.skill', $skill)
            ->select('players.*')
            ->orderByDesc('player_skills.value')
            ->get();
    }

    public function findByPositionWithAnySkill(string $position, array $excludeIds = []): Collection
    {
        return Player::where('position', $position)
            ->whereNotIn('players.id', $excludeIds)
            ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
            ->select('players.*')
            ->orderByDesc('player_skills.value')
            ->get()
            ->unique('id');
    }
}
