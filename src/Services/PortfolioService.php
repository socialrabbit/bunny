<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Bunny\Models\Portfolio;
use Bunny\Models\Project;
use Bunny\Models\Skill;
use Bunny\Models\Experience;
use Bunny\Models\Education;
use Bunny\Models\Certificate;
use Bunny\Events\PortfolioUpdated;

class PortfolioService
{
    protected $cache;
    protected $storage;

    public function __construct()
    {
        $this->cache = Cache::tags(['portfolio']);
        $this->storage = Storage::disk('public');
    }

    /**
     * Create or update portfolio
     */
    public function createOrUpdate(array $data, $userId)
    {
        $portfolio = Portfolio::updateOrCreate(
            ['user_id' => $userId],
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'theme' => $data['theme'] ?? 'default',
                'custom_css' => $data['custom_css'] ?? null,
                'custom_js' => $data['custom_js'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'is_published' => $data['is_published'] ?? false,
                'custom_domain' => $data['custom_domain'] ?? null,
            ]
        );

        $this->cache->flush();
        event(new PortfolioUpdated($portfolio));

        return $portfolio;
    }

    /**
     * Add project to portfolio
     */
    public function addProject(array $data, $portfolioId)
    {
        $project = Project::create([
            'portfolio_id' => $portfolioId,
            'title' => $data['title'],
            'description' => $data['description'],
            'technologies' => $data['technologies'],
            'url' => $data['url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'live_url' => $data['live_url'] ?? null,
            'featured_image' => $this->handleImageUpload($data['featured_image'] ?? null),
            'gallery' => $this->handleMultipleImages($data['gallery'] ?? []),
            'order' => $data['order'] ?? 0,
            'is_featured' => $data['is_featured'] ?? false,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'] ?? 'completed',
        ]);

        $this->cache->flush();
        return $project;
    }

    /**
     * Add skill to portfolio
     */
    public function addSkill(array $data, $portfolioId)
    {
        $skill = Skill::create([
            'portfolio_id' => $portfolioId,
            'name' => $data['name'],
            'category' => $data['category'],
            'level' => $data['level'],
            'order' => $data['order'] ?? 0,
            'is_featured' => $data['is_featured'] ?? false,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
        ]);

        $this->cache->flush();
        return $skill;
    }

    /**
     * Add experience to portfolio
     */
    public function addExperience(array $data, $portfolioId)
    {
        $experience = Experience::create([
            'portfolio_id' => $portfolioId,
            'company' => $data['company'],
            'position' => $data['position'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'location' => $data['location'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_featured' => $data['is_featured'] ?? false,
        ]);

        $this->cache->flush();
        return $experience;
    }

    /**
     * Add education to portfolio
     */
    public function addEducation(array $data, $portfolioId)
    {
        $education = Education::create([
            'portfolio_id' => $portfolioId,
            'institution' => $data['institution'],
            'degree' => $data['degree'],
            'field' => $data['field'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'location' => $data['location'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_featured' => $data['is_featured'] ?? false,
        ]);

        $this->cache->flush();
        return $education;
    }

    /**
     * Add certificate to portfolio
     */
    public function addCertificate(array $data, $portfolioId)
    {
        $certificate = Certificate::create([
            'portfolio_id' => $portfolioId,
            'name' => $data['name'],
            'issuer' => $data['issuer'],
            'issue_date' => $data['issue_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'credential_id' => $data['credential_id'] ?? null,
            'credential_url' => $data['credential_url'] ?? null,
            'image' => $this->handleImageUpload($data['image'] ?? null),
            'order' => $data['order'] ?? 0,
            'is_featured' => $data['is_featured'] ?? false,
        ]);

        $this->cache->flush();
        return $certificate;
    }

    /**
     * Get portfolio with all related data
     */
    public function getPortfolio($userId)
    {
        return $this->cache->remember("portfolio.{$userId}", 3600, function () use ($userId) {
            return Portfolio::with([
                'projects' => function ($query) {
                    $query->orderBy('order')->orderBy('created_at', 'desc');
                },
                'skills' => function ($query) {
                    $query->orderBy('order');
                },
                'experiences' => function ($query) {
                    $query->orderBy('order')->orderBy('start_date', 'desc');
                },
                'education' => function ($query) {
                    $query->orderBy('order')->orderBy('start_date', 'desc');
                },
                'certificates' => function ($query) {
                    $query->orderBy('order')->orderBy('issue_date', 'desc');
                },
            ])->where('user_id', $userId)->first();
        });
    }

    /**
     * Handle single image upload
     */
    protected function handleImageUpload($image)
    {
        if (!$image) return null;

        if (is_string($image)) {
            return $image;
        }

        $path = $image->store('portfolio/images', 'public');
        return $this->storage->url($path);
    }

    /**
     * Handle multiple image uploads
     */
    protected function handleMultipleImages($images)
    {
        if (empty($images)) return [];

        return collect($images)->map(function ($image) {
            return $this->handleImageUpload($image);
        })->filter()->values()->toArray();
    }

    /**
     * Generate portfolio statistics
     */
    public function generateStatistics($portfolioId)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);

        return [
            'total_projects' => $portfolio->projects->count(),
            'featured_projects' => $portfolio->projects->where('is_featured', true)->count(),
            'total_skills' => $portfolio->skills->count(),
            'total_experience' => $portfolio->experiences->count(),
            'total_education' => $portfolio->education->count(),
            'total_certificates' => $portfolio->certificates->count(),
            'years_of_experience' => $this->calculateYearsOfExperience($portfolio->experiences),
            'top_skills' => $this->getTopSkills($portfolio->skills),
            'recent_projects' => $portfolio->projects->take(3),
        ];
    }

    /**
     * Calculate total years of experience
     */
    protected function calculateYearsOfExperience($experiences)
    {
        return $experiences->sum(function ($experience) {
            $start = \Carbon\Carbon::parse($experience->start_date);
            $end = $experience->is_current ? now() : \Carbon\Carbon::parse($experience->end_date);
            return $start->diffInYears($end);
        });
    }

    /**
     * Get top skills
     */
    protected function getTopSkills($skills)
    {
        return $skills->sortByDesc('level')
            ->take(5)
            ->values();
    }

    /**
     * Export portfolio data
     */
    public function exportPortfolio($portfolioId)
    {
        $portfolio = $this->getPortfolio($portfolioId);
        
        return [
            'portfolio' => $portfolio->toArray(),
            'statistics' => $this->generateStatistics($portfolioId),
            'exported_at' => now(),
        ];
    }

    /**
     * Import portfolio data
     */
    public function importPortfolio(array $data, $userId)
    {
        $portfolio = $this->createOrUpdate($data['portfolio'], $userId);

        foreach ($data['projects'] ?? [] as $project) {
            $this->addProject($project, $portfolio->id);
        }

        foreach ($data['skills'] ?? [] as $skill) {
            $this->addSkill($skill, $portfolio->id);
        }

        foreach ($data['experiences'] ?? [] as $experience) {
            $this->addExperience($experience, $portfolio->id);
        }

        foreach ($data['education'] ?? [] as $education) {
            $this->addEducation($education, $portfolio->id);
        }

        foreach ($data['certificates'] ?? [] as $certificate) {
            $this->addCertificate($certificate, $portfolio->id);
        }

        return $portfolio;
    }
} 