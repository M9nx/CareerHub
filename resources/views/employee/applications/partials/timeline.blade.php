@php
    use App\Enums\ApplicationStatus;

    $steps = match ($application->status) {
        ApplicationStatus::Submitted => [
            ['label' => __('Submitted'), 'state' => 'current'],
        ],
        ApplicationStatus::UnderReview => [
            ['label' => __('Submitted'), 'state' => 'done'],
            ['label' => __('Under Review'), 'state' => 'current'],
        ],
        ApplicationStatus::Accepted => [
            ['label' => __('Submitted'), 'state' => 'done'],
            ['label' => __('Under Review'), 'state' => 'done'],
            ['label' => __('Accepted'), 'state' => 'current'],
        ],
        ApplicationStatus::Rejected => [
            ['label' => __('Submitted'), 'state' => 'done'],
            ['label' => __('Under Review'), 'state' => 'done'],
            ['label' => __('Rejected'), 'state' => 'current'],
        ],
        ApplicationStatus::Cancelled => [
            ['label' => __('Submitted'), 'state' => 'done'],
            ['label' => __('Cancelled'), 'state' => 'current'],
        ],
    };
@endphp

<div class="space-y-3">
    <h3 class="text-lg font-semibold">
        {{ __('Application Timeline') }}
    </h3>

    <ol class="space-y-3 border-l border-gray-200 pl-4 dark:border-gray-700">
        @foreach ($steps as $step)
            <li
                @class([
                    'relative text-sm',
                    'font-semibold text-gray-900 dark:text-gray-100' => $step['state'] === 'current',
                    'text-gray-600 dark:text-gray-400' => $step['state'] === 'done',
                ])
            >
                <span
                    @class([
                        'absolute -left-[1.3rem] top-1.5 h-2.5 w-2.5 rounded-full',
                        'bg-indigo-600 dark:bg-indigo-400' => $step['state'] === 'current',
                        'bg-gray-400 dark:bg-gray-500' => $step['state'] === 'done',
                    ])
                ></span>

                {{ $step['label'] }}
            </li>
        @endforeach
    </ol>

    @if ($application->isCancelled() && $application->cancelled_at)
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Cancelled at:') }}
            {{ $application->cancelled_at->toDayDateTimeString() }}
        </div>
    @endif
</div>
