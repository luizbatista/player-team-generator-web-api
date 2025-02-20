<?php

namespace App\Services;

use App\Models\Player;
use App\Exceptions\PlayerNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Repositories\PlayerRepository;

class PlayerService
{
    public function __construct(
        private PlayerRepository $playerRepository
    ) {}

    public function createPlayer(array $data): Player
    {
        DB::beginTransaction();
        try {
            $player = Player::create([
                'name' => $data['name'],
                'position' => $data['position']
            ]);

            foreach ($data['playerSkills'] as $skill) {
                $player->skills()->create([
                    'skill' => $skill['skill'],
                    'value' => $skill['value']
                ]);
            }

            DB::commit();
            return $player->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updatePlayer(int $id, array $data): Player
    {
        DB::beginTransaction();
        try {
            $player = $this->findPlayerOrFail($id);

            $player->update([
                'name' => $data['name'],
                'position' => $data['position']
            ]);

            $player->skills()->delete();

            foreach ($data['playerSkills'] as $skill) {
                $player->skills()->create([
                    'skill' => $skill['skill'],
                    'value' => $skill['value']
                ]);
            }

            DB::commit();
            return $player->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletePlayer(int $id): void
    {
        DB::beginTransaction();
        try {
            $player = $this->findPlayerOrFail($id);

            $player->skills()->delete();
            $player->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findPlayerOrFail(int $id): Player
    {
        $player = Player::find($id);

        if (!$player) {
            throw new PlayerNotFoundException();
        }

        return $player;
    }

    public function getAllPlayers()
    {
        return Player::with('skills')->get();
    }
}
