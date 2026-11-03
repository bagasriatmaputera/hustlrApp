<?php

namespace App\Livewire\Portfolio\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioProject;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Portfolio - Add Project')]
class Create extends Component
{
    use AuthorizesPermissions, WithFileUploads;

    public string $title_id = '';

    public string $title_en = '';

    public string $description_id = '';

    public string $description_en = '';

    public string $image = '';

    /** @var mixed */
    public $image_file = null;

    public string $gif_image = '';

    /** @var mixed */
    public $gif_image_file = null;

    public string $problem_solution_en = '';

    public string $problem_solution_id = '';

    public string $challenges_learnings_en = '';

    public string $challenges_learnings_id = '';

    /**
     * @var array<int, array{image: string, caption_en: string, caption_id: string, file?: mixed}>
     */
    public array $media_gallery = [];

    public string $tech_stack_raw = '';

    public string $live_url = '';

    public string $repo_url = '';

    public bool $featured = false;

    public bool $is_live = false;

    public bool $is_repo = false;

    public int $order = 0;

    public function mount(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');
    }

    public function addMediaGalleryItem(): void
    {
        $this->media_gallery[] = [
            'image' => '',
            'caption_en' => '',
            'caption_id' => '',
            'file' => null,
        ];
    }

    public function removeMediaGalleryItem(int $index): void
    {
        unset($this->media_gallery[$index]);
        $this->media_gallery = array_values($this->media_gallery);
    }

    public function save(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');

        $this->validate([
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_id' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'gif_image' => ['nullable', 'string', 'max:500'],
            'gif_image_file' => ['nullable', 'file', 'mimes:gif,webp,mp4', 'max:10240'],
            'media_gallery' => ['array'],
            'media_gallery.*.image' => ['nullable', 'string', 'max:500'],
            'media_gallery.*.caption_en' => ['nullable', 'string', 'max:255'],
            'media_gallery.*.caption_id' => ['nullable', 'string', 'max:255'],
            'media_gallery.*.file' => ['nullable', 'file', 'image', 'max:5120'],
            'problem_solution_en' => ['nullable', 'string'],
            'problem_solution_id' => ['nullable', 'string'],
            'challenges_learnings_en' => ['nullable', 'string'],
            'challenges_learnings_id' => ['nullable', 'string'],
            'tech_stack_raw' => ['required', 'string'],
            'live_url' => ['nullable', 'url', 'max:500'],
            'repo_url' => ['nullable', 'url', 'max:500'],
            'featured' => ['boolean'],
            'is_live' => ['boolean'],
            'is_repo' => ['boolean'],
            'order' => ['integer', 'min:0'],
        ]);

        $disk = (string) config('filesystems.default', 's3');

        $coverImage = $this->image ?: null;
        if ($this->image_file) {
            $coverImage = $this->image_file->store('portfolio/projects', $disk);
        }

        $gifImage = $this->gif_image ?: null;
        if ($this->gif_image_file) {
            $gifImage = $this->gif_image_file->store('portfolio/projects', $disk);
        }

        $techStack = array_values(array_filter(array_map('trim', explode(',', $this->tech_stack_raw))));

        $mediaGallery = collect($this->media_gallery)
            ->map(function (array $item) use ($disk): ?array {
                $itemImage = trim($item['image']);
                if (isset($item['file']) && $item['file']) {
                    $itemImage = $item['file']->store('portfolio/projects/gallery', $disk);
                }

                if ($itemImage === '') {
                    return null;
                }

                return [
                    'image' => $itemImage,
                    'caption' => [
                        'en' => trim($item['caption_en']),
                        'id' => trim($item['caption_id']),
                    ],
                ];
            })
            ->filter()
            ->values()
            ->all();

        $problemSolution = ($this->problem_solution_id !== '' || $this->problem_solution_en !== '')
            ? ['id' => $this->problem_solution_id, 'en' => $this->problem_solution_en]
            : null;

        $challengesLearnings = ($this->challenges_learnings_id !== '' || $this->challenges_learnings_en !== '')
            ? ['id' => $this->challenges_learnings_id, 'en' => $this->challenges_learnings_en]
            : null;

        PortfolioProject::create([
            'title' => ['id' => $this->title_id, 'en' => $this->title_en],
            'description' => ['id' => $this->description_id, 'en' => $this->description_en],
            'image' => $coverImage,
            'gif_image' => $gifImage,
            'media_gallery' => $mediaGallery,
            'problem_solution' => $problemSolution,
            'challenges_learnings' => $challengesLearnings,
            'tech_stack' => $techStack,
            'live_url' => $this->live_url ?: null,
            'repo_url' => $this->repo_url ?: null,
            'featured' => $this->featured,
            'is_live' => $this->is_live,
            'is_repo' => $this->is_repo,
            'order' => $this->order,
        ]);

        Flux::toast(variant: 'success', text: __('Project created successfully.'));

        $this->redirectRoute('portfolio.projects.index', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.projects.create');
    }
}
