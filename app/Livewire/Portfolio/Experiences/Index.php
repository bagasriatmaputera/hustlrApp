<?php

namespace App\Livewire\Portfolio\Experiences;

use App\Models\Portfolio\PortfolioExperience;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio - Experiences')]
class Index extends Component
{
    /**
     * @return Collection<int, PortfolioExperience>
     */
    #[Computed]
    public function experiences(): Collection
    {
        return PortfolioExperience::with('jobDescs')->orderBy('order')->get();
    }

    public function delete(string $experienceId): void
    {
        PortfolioExperience::findOrFail($experienceId)->delete();
        Flux::toast(variant: 'success', text: __('Experience deleted successfully.'));
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.experiences.index');
    }
}
