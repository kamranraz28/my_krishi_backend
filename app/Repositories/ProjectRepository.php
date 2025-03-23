<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Booking;
use App\Models\Projectagent;
use App\Models\User;

class ProjectRepository
{
    /**
     * Get project details with caching.
     */
    public function cacheAllProjects()
    {
        // Cache all projects for a specific duration (e.g., 60 minutes)
        $projects = Project::with('details')->get();
        Cache::forever('all_projects', $projects);
    }

    // Get all cached projects
    public function getAllCachedProjects()
    {
        return Cache::get('all_projects');  // Retrieve all projects from the cache
    }

    // Cache a specific project
    public function cacheProject($id)
    {
        // Try to fetch the project from the cache first
        $project = Cache::get("project_{$id}");

        // If project is not in cache, fetch it from the database and cache it
        if (!$project) {
            $project = Project::with('details')->findOrFail($id);

            // Store the project in the cache for 60 minutes
            Cache::put("project_{$id}", $project, 60); // Cache for 60 minutes
        }

        return $project; // Return the cached or newly fetched project
    }


    // Get a specific project from cache
    public function getProjectFromCache($id)
    {
        return Cache::get("project_{$id}");  // Get the specific project from cache
    }

    // Invalidate cache for a specific project
    public function forgetProjectCache($id)
    {
        Cache::forget("project_{$id}");
    }

    // Invalidate all project caches
    public function forgetAllProjectsCache()
    {
        Cache::forget('all_projects');
    }

    // Cache the project details after modifying
    public function refreshProjectCache($id)
    {
        // Remove the old cache for the specific project
        $this->forgetProjectCache($id);

        // Fetch the updated project with details
        $updatedProject = Project::with('details')->findOrFail($id);

        // Cache the updated project
        Cache::forever("project_{$id}", $updatedProject);

        // Update the `all_projects` cache
        $cachedProjects = Cache::get('all_projects', collect());

        // Remove the old version of the project from cache
        $cachedProjects = $cachedProjects->reject(function ($project) use ($id) {
            return $project->id == $id;
        });

        // Add the updated project
        $cachedProjects->push($updatedProject);

        // Store the updated projects list in cache
        Cache::forever('all_projects', $cachedProjects);
    }
    public function debugProjectCache()
    {
        // Retrieve all projects from the cache
        $allProjects = Cache::get('all_projects');

        // Dump the entire cache
        dd($allProjects);
    }

    //Add new project in cache
    public function newProjectCache($newProject)
    {
        // Reload project with details
        $fullProject = Project::with('details')->find($newProject->id);

        if (!$fullProject) {
            return; // If project is not found, exit early
        }

        // Retrieve the current cached projects or initialize an empty collection
        $cachedProjects = Cache::get('all_projects', collect());

        // Ensure it's a collection
        if (!($cachedProjects instanceof \Illuminate\Support\Collection)) {
            $cachedProjects = collect($cachedProjects);
        }

        // Append the full project (with details) to the existing cache
        $cachedProjects->push($fullProject);

        // Store the updated projects collection back in the cache
        Cache::forever('all_projects', $cachedProjects);
    }




}
