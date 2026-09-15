<x-app.page :title="__('Saved')" :eyebrow="__('My items')">
    @if (session('success'))
        <div class="app-card mb-6 p-4 text-sm text-[var(--app-success)]">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-8">
        <section class="space-y-3">
            <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('Saved jobs') }}
            </h2>

            @forelse ($savedJobs as $savedJob)
                @php($job = $savedJob->jobPosting)
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <div class="min-w-0">
                        <p class="font-medium text-[var(--app-text)]">{{ $job?->title ?? __('Deleted job') }}</p>
                        <p class="text-sm text-[var(--app-text-muted)]">
                            {{ $job?->employer?->employerProfile?->company_name }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($job)
                            <a href="{{ route('employee.jobs.show', $job) }}" class="app-btn-secondary">{{ __('View') }}</a>
                        @endif
                        <form method="POST" action="{{ route('saved.jobs.destroy', $job) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="app-btn-secondary">{{ __('Remove') }}</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No saved jobs yet.') }}</p>
                </div>
            @endforelse
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('Saved posts') }}
            </h2>

            @forelse ($savedPosts as $savedPost)
                @php($post = $savedPost->post)
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <div class="min-w-0">
                        <p class="font-medium text-[var(--app-text)]">
                            {{ \Illuminate\Support\Str::limit($post?->title ?: ($post?->body ?? __('Deleted post')), 80) }}
                        </p>
                        <p class="text-sm text-[var(--app-text-muted)]">{{ $post?->author?->name }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($post)
                            <a href="{{ route('feed.index') }}#post-{{ $post->id }}" class="app-btn-secondary">{{ __('View') }}</a>
                            <form method="POST" action="{{ route('saved.posts.destroy', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="app-btn-secondary">{{ __('Remove') }}</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No saved posts yet.') }}</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app.page>
