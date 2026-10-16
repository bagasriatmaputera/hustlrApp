<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('IPS - JOBTRACKER - Can Create Job Tracker') ?? false;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'contract_status' => ['required', 'string', 'in:contract,full_time,part_time,freelance,internship'],
            'apply_status' => ['required', 'string', 'in:applied,hr_review,interview,interview_user,offering_letter,accepted,rejected'],
            'salary' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'workplace_type' => ['required', 'string', 'in:remote,hybrid,onsite'],
            'job_url' => ['nullable', 'url', 'max:500'],
            'benefits' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'applied_at' => ['nullable', 'date'],
        ];
    }
}
