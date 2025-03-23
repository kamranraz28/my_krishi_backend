<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Booking;
use App\Models\Projectagent;
use App\Models\User;

class UserRepository{

    public function cacheAgent()
    {
        // Cache all agents
        $agents = User::where('level',300)->get();
        Cache::forever('agents', $agents);
    }

    public function debugAgentCache()
    {
        // Retrieve all agents from the cache
        $agents = Cache::get('agents');

        // Dump the entire cache
        dd($agents);
    }
    public function getAllCachedAgents()
    {
        return Cache::get('agents');
    }

    public function newAgentCache($newAgent)
    {
        // ✅ Correct the model: Fetch the full agent using User model, not Project model
        $fullAgent = User::find($newAgent->id);

        // ✅ Check if the agent was found
        if (!$fullAgent) {
            return;
        }

        // ✅ Retrieve the current cached agents or initialize an empty collection
        $cachedAgents = Cache::get('agents', collect());

        // ✅ Ensure it's a collection
        if (!($cachedAgents instanceof \Illuminate\Support\Collection)) {
            $cachedAgents = collect($cachedAgents);
        }

        // ✅ Append the new agent to the existing cache
        $cachedAgents->push($fullAgent);

        // ✅ Store the updated agents collection back in the cache
        Cache::forever('agents', $cachedAgents);
    }


    public function forgetAllAgentsCache()
    {
        Cache::forget('agents');
    }

}
