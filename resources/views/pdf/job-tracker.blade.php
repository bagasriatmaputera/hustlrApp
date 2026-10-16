<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Job Application Tracker</title>
    <style>
        @page { size: a4 landscape; margin: 1cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        p.sub { font-size: 9px; color: #6b7280; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; letter-spacing: .05em; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 8px; }
    </style>
</head>
<body>
    <h1>Job Application Tracker</h1>
    <p class="sub">
        @if ($user)
            {{ $user->full_name ?? $user->name }} ({{ $user->email }}) &middot;
        @endif
        {{ __('Exported on') }} {{ now()->format('d M Y H:i:s') }} &middot;
        {{ $jobs->count() }} {{ __('records') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>{{ __('Company') }}</th>
                <th>{{ __('Position') }}</th>
                <th>{{ __('Contract') }}</th>
                <th>{{ __('Apply Status') }}</th>
                <th>{{ __('Salary') }}</th>
                <th>{{ __('Location') }}</th>
                <th>{{ __('Workplace') }}</th>
                <th>{{ __('Applied') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jobs as $job)
                <tr>
                    <td>{{ $job->company_name }}</td>
                    <td>{{ $job->position }}</td>
                    <td>{{ $job->contract_status }}</td>
                    <td>{{ \App\Models\JobApplication::getApplyStatusConfig($job->apply_status)['label'] }}</td>
                    <td>{{ $job->salary }}</td>
                    <td>{{ $job->location }}</td>
                    <td>{{ ucfirst($job->workplace_type) }}</td>
                    <td>{{ $job->applied_at?->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:16px;">{{ __('No records.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
