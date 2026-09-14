@php
    $statusClasses = match ($application->status) {
        \App\Enums\ApplicationStatus::Submitted =>
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',

        \App\Enums\ApplicationStatus::UnderReview =>
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',

        \App\Enums\ApplicationStatus::Accepted =>
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',

        \App\Enums\ApplicationStatus::Rejected =>
            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',

        \App\Enums\ApplicationStatus::Cancelled =>
            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
@endphp

<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClasses }}">
    {{ str_replace('_', ' ', ucfirst($application->status->value)) }}
</span>