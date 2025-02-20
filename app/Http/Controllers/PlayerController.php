<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Models\Player;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PlayerResource;
use App\Http\Requests\PlayerRequest;

class PlayerController extends Controller
{
    public function index()
    {
        try {
            $players = Player::with('skills')->get();
            return PlayerResource::collection($players);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to list players'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $player = Player::findOrFail($id);
            return new PlayerResource($player);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Player not found'
            ], 404);
        }
    }

    public function store(PlayerRequest $request)
    {
        try {
            DB::beginTransaction();

            $player = Player::create([
                'name' => $request->name,
                'position' => $request->position
            ]);

            foreach ($request->playerSkills as $skill) {
                $player->skills()->create([
                    'skill' => $skill['skill'],
                    'value' => $skill['value']
                ]);
            }

            DB::commit();

            return new PlayerResource($player->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create player'
            ], 500);
        }
    }

    public function update(PlayerRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $player = Player::findOrFail($id);

            $player->update([
                'name' => $request->name,
                'position' => $request->position
            ]);

            $player->skills()->delete();

            foreach ($request->playerSkills as $skill) {
                $player->skills()->create([
                    'skill' => $skill['skill'],
                    'value' => $skill['value']
                ]);
            }

            DB::commit();

            return new PlayerResource($player->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update player'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $player = Player::findOrFail($id);
            $player->skills()->delete();
            $player->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete player'
            ], 500);
        }
    }
}
