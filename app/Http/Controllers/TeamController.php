<?php

namespace App\Http\Controllers;

use App\Services\TeamService;
use App\Http\Resources\TeamPlayerResource;
use App\Http\Requests\TeamProcessRequest;

class TeamController extends Controller
{
    public function __construct(
        private TeamService $teamService
    ) {}

    public function process(TeamProcessRequest $request)
    {
        $requestData = $request->validated();
        $players = $this->teamService->processTeamSelection(['requirements' => $requestData]);
        return TeamPlayerResource::collection($players);
    }
}
