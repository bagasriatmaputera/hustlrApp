<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="mx-auto flex min-h-screen max-w-md flex-col items-center justify-center gap-4 px-6 text-center">
            @php($user = auth()->user())

            @if ($user?->isRejected())
                <flux:heading size="xl">{{ __('Access denied') }}</flux:heading>
                <flux:text>
                    {{ __('Your request for access was declined. Please contact the owner if you believe this is a mistake.') }}
                </flux:text>
            @else
                <flux:heading size="xl">{{ __('Waiting for approval') }}</flux:heading>
                <flux:text>
                    {{ __('Your account was created and your module request was sent to the owner. You will be able to use the app once it is approved.') }}
                </flux:text>

                @if (! empty($user?->requested_modules))
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach ($user->requested_modules as $module)
                            <flux:badge>{{ __(\App\PermissionManager\ModuleRegistry::all()[$module] ?? $module) }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <flux:button type="submit" variant="filled">{{ __('Log out') }}</flux:button>
            </form>
        </div>

        @fluxScripts
    </body>
</html>
