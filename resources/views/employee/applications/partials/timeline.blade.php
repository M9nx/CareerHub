```blade
<div class="space-y-3">
    <h3 class="text-lg font-semibold">
        {{ __('Application Timeline') }}
    </h3>

    <ul class="space-y-2">
        <li>{{ __('Submitted') }}</li>
        <li>{{ __('Under Review') }}</li>
        <li>{{ __('Accepted') }}</li>
        <li>{{ __('Rejected') }}</li>
        <li>{{ __('Cancelled') }}</li>
    </ul>

    @if ($application->isCancelled() && $application->cancelled_at)
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Cancelled at:') }}
            {{ $application->cancelled_at->toDateTimeString() }}
        </div>
    @endif
</div>
```
