<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use App\Http\Resources\TeamPlayerResource;
use Illuminate\Support\Collection;
use App\Http\Requests\TeamProcessRequest;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function process(TeamProcessRequest $request)
    {
        try {
            $requirements = $request->all();
            $selectedPlayers = new Collection();
            $usedPlayerIds = [];

            foreach ($requirements as $requirement) {
                // If there are no players with the specific skill, try to find players with any skill
                $players = Player::where('position', $requirement['position'])
                    ->whereNotIn('players.id', $usedPlayerIds)
                    ->join('player_skills', function ($join) use ($requirement) {
                        $join->on('players.id', '=', 'player_skills.player_id')
                            ->where('player_skills.skill', $requirement['mainSkill']);
                    })
                    ->orderByDesc('player_skills.value')
                    ->select('players.*')
                    ->take($requirement['numberOfPlayers'])
                    ->get();

                // If there are no players with the specific skill, try to find players with any skill
                if ($players->isEmpty() || $players->count() < $requirement['numberOfPlayers']) {
                    $usedPlayerIds = array_merge($usedPlayerIds, $players->pluck('id')->toArray());

                    $playersWithAnySkill = Player::where('position', $requirement['position'])
                        ->whereNotIn('players.id', $usedPlayerIds)
                        ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
                        ->select('players.*')
                        ->orderByDesc('player_skills.value')
                        ->get()
                        ->unique('id')
                        ->take($requirement['numberOfPlayers'] - $players->count());
                    $players = $players->concat($playersWithAnySkill);
                }

                if ($players->count() < $requirement['numberOfPlayers']) {
                    return response()->json([
                        'message' => "Insufficient number of players for position: {$requirement['position']}"
                    ], 400);
                }

                $usedPlayerIds = array_merge($usedPlayerIds, $players->pluck('id')->toArray());
                $selectedPlayers = $selectedPlayers->concat($players);
            }

            return TeamPlayerResource::collection($selectedPlayers);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process team selection'
            ], 500);
        }
    }
}
