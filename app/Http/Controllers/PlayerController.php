<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Services\PlayerService;
use App\Exceptions\PlayerNotFoundException;
use App\Http\Resources\PlayerResource;
use App\Http\Requests\PlayerRequest;
use Illuminate\Support\Facades\Log;
class PlayerController extends Controller
{
    public function __construct(
        private PlayerService $playerService
    ) {}

    public function index()
    {
        try {
            $players = $this->playerService->getAllPlayers();
            return PlayerResource::collection($players);
        } catch (\Exception $e) {
            Log::error('Failed to list players', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to list players'
            ], 500);
        }
    }

    public function show($id)
    {
        $player = $this->playerService->findPlayerOrFail($id);
        return new PlayerResource($player);
    }

    public function store(PlayerRequest $request)
    {
        $player = $this->playerService->createPlayer($request->validated());
        return new PlayerResource($player);
    }

    public function update(PlayerRequest $request, $id)
    {
        $player = $this->playerService->updatePlayer($id, $request->validated());
        return new PlayerResource($player);
    }

    public function destroy($id)
    {
        $this->playerService->deletePlayer($id);
        return response()->json(null, 204);
    }
}
