<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GitHubService
{
    /**
     * The GitHub API base URL.
     *
     * @var string
     */
    protected $apiUrl = 'https://api.github.com';

    /**
     * The repository owner.
     *
     * @var string
     */
    protected $owner = 'socialrabbit';

    /**
     * The repository name.
     *
     * @var string
     */
    protected $repo = 'bunny';

    /**
     * Check if the repository is starred by the current user.
     *
     * @param  string  $token
     * @return bool
     */
    public function isStarred(string $token): bool
    {
        $response = Http::withToken($token)
            ->get("{$this->apiUrl}/user/starred/{$this->owner}/{$this->repo}");

        return $response->status() === 204;
    }

    /**
     * Star the repository.
     *
     * @param  string  $token
     * @return bool
     */
    public function star(string $token): bool
    {
        $response = Http::withToken($token)
            ->put("{$this->apiUrl}/user/starred/{$this->owner}/{$this->repo}");

        return $response->status() === 204;
    }

    /**
     * Get repository statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return Cache::remember('github_stats', 3600, function () {
            $response = Http::get("{$this->apiUrl}/repos/{$this->owner}/{$this->repo}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'stars' => $data['stargazers_count'] ?? 0,
                    'forks' => $data['forks_count'] ?? 0,
                    'watchers' => $data['watchers_count'] ?? 0,
                ];
            }

            return [
                'stars' => 0,
                'forks' => 0,
                'watchers' => 0,
            ];
        });
    }

    /**
     * Get the repository URL.
     *
     * @return string
     */
    public function getRepositoryUrl(): string
    {
        return "https://github.com/{$this->owner}/{$this->repo}";
    }
} 