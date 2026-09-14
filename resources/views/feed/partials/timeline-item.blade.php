@php
    use App\Enums\UserRole;
    use App\Support\Timeline\TimelineItemType;
@endphp

@switch($item->type)
    @case(TimelineItemType::Post)
        @php($post = $item->subject)
        <article class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                        {{ __('Post') }}
                    </p>
                    <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $post->title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('By :name', ['name' => $item->actor?->name ?? __('Unknown author')]) }}
                    </p>
                </div>

                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                    {{ $post->author_role?->label() ?? __('Author') }}
                </span>
            </div>

            <p class="mt-4 whitespace-pre-line text-gray-700 dark:text-gray-300">
                {{ \Illuminate\Support\Str::limit($post->body, 180) }}
            </p>
        </article>
        @break

    @case(TimelineItemType::JobPublished)
        @php($job = $item->subject)
        <article class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                {{ __('Job published') }}
            </p>

            <div class="mt-2 flex items-start justify-between gap-4">
                <div>
                    @if (auth()->user()?->role === UserRole::Employer && $job->employer_id === auth()->id())
                        <a
                            href="{{ route('employer.jobs.edit', $job) }}"
                            class="text-lg font-semibold text-gray-900 underline-offset-4 hover:underline dark:text-gray-100"
                        >
                            {{ $job->title }}
                        </a>
                    @elseif (auth()->user()?->role === UserRole::Employee)
                        <a
                            href="{{ route('employee.jobs.show', $job) }}"
                            class="text-lg font-semibold text-gray-900 underline-offset-4 hover:underline dark:text-gray-100"
                        >
                            {{ $job->title }}
                        </a>
                    @else
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $job->title }}
                        </h3>
                    @endif

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('By :name', ['name' => $item->actor?->name ?? __('Unknown employer')]) }}
                    </p>
                </div>

                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                    {{ __('Employer') }}
                </span>
            </div>

            <p class="mt-4 text-gray-700 dark:text-gray-300">
                {{ \Illuminate\Support\Str::limit($job->description, 180) }}
            </p>
        </article>
        @break

    @case(TimelineItemType::ApplicationEvent)
        @php($application = $item->subject)
        <article class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-amber-600 dark:text-amber-400">
                {{ __('Application update') }}
            </p>

            <div class="mt-2 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $application->jobPosting?->title ?? __('Unknown job') }}
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        @if (auth()->user()?->role === UserRole::Employer)
                            {{ __('Applicant: :name', ['name' => $item->actor?->name ?? __('Unknown applicant')]) }}
                        @else
                            {{ __('Your application') }}
                        @endif
                    </p>
                </div>

                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                    {{ str($application->status->name)->headline() }}
                </span>
            </div>

            <div class="mt-4">
                @if (auth()->user()?->role === UserRole::Employer)
                    <a
                        href="{{ route('employer.applications.show', $application) }}"
                        class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                    >
                        {{ __('Review application') }}
                    </a>
                @elseif (auth()->user()?->role === UserRole::Employee)
                    <a
                        href="{{ route('employee.applications.show', $application) }}"
                        class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                    >
                        {{ __('View application') }}
                    </a>
                @endif
            </div>
        </article>
        @break
@endswitch
