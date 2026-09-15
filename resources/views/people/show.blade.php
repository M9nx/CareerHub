<x-app.page :title="$person->name" :eyebrow="__('Profile')">
    <div class="space-y-6">
        <section class="app-card overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                    @if ($person->avatarUrl())
                        <img
                            src="{{ $person->avatarUrl() }}"
                            alt=""
                            class="h-24 w-24 rounded-full object-cover ring-1 ring-[var(--app-border)]"
                        />
                    @else
                        <div class="app-avatar h-24 w-24 text-2xl">
                            {{ str($person->name)->substr(0, 1)->upper() }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-2xl font-medium text-[var(--app-text)]">
                                {{ $person->name }}
                            </h2>
                            <span class="app-badge">{{ $person->role?->label() }}</span>
                        </div>

                        @if (filled($person->headline))
                            <p class="mt-2 text-base text-[var(--app-text)] opacity-90">
                                {{ $person->headline }}
                            </p>
                        @endif

                        @if (filled($person->location))
                            <p class="mt-2 text-sm text-[var(--app-text-muted)]">
                                {{ $person->location }}
                            </p>
                        @endif

                        @if (auth()->id() === $person->id)
                            <a href="{{ route('profile.edit') }}" class="app-link mt-4 inline-flex">
                                {{ __('Edit profile') }}
                            </a>
                        @else
                            <x-app.connection-actions :person="$person" :connection="$connection" />
                        @endif
                    </div>
                </div>

                @if (filled($person->about))
                    <div class="swiss-rule my-6"></div>
                    <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('About') }}
                    </h3>
                    <p class="mt-3 max-w-[65ch] whitespace-pre-line text-sm leading-relaxed text-[var(--app-text)] opacity-90">
                        {{ $person->about }}
                    </p>
                @endif
            </div>
        </section>

        @if ($person->employerProfile)
            @php($company = $person->employerProfile)
            <section class="app-card p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    @if ($company->logoUrl())
                        <img
                            src="{{ $company->logoUrl() }}"
                            alt=""
                            class="h-14 w-14 rounded-md object-cover ring-1 ring-[var(--app-border)]"
                        />
                    @endif
                    <div class="min-w-0">
                        <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                            {{ __('Company') }}
                        </h3>
                        <p class="mt-2 text-lg font-medium text-[var(--app-text)]">
                            {{ $company->company_name }}
                        </p>
                        <div class="mt-2 space-y-1 text-sm text-[var(--app-text-muted)]">
                            @if (filled($company->industry))
                                <p>{{ $company->industry }}</p>
                            @endif
                            @if (filled($company->company_size))
                                <p>{{ __('Size: :size', ['size' => $company->company_size]) }}</p>
                            @endif
                            @if (filled($company->location))
                                <p>{{ $company->location }}</p>
                            @endif
                            @if (filled($company->website))
                                <p>
                                    <a href="{{ $company->website }}" class="app-link" target="_blank" rel="noopener noreferrer">
                                        {{ $company->website }}
                                    </a>
                                </p>
                            @endif
                        </div>
                        @if (filled($company->about))
                            <p class="mt-4 max-w-[65ch] whitespace-pre-line text-sm leading-relaxed opacity-90">
                                {{ $company->about }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <section class="space-y-4">
            <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('Recent posts') }}
            </h3>

            @forelse ($recentPosts as $post)
                <article class="app-card p-5">
                    <time
                        datetime="{{ $post->created_at?->toIso8601String() }}"
                        class="text-sm text-[var(--app-text-muted)]"
                    >
                        {{ $post->created_at?->diffForHumans() }}
                    </time>
                    @if ($post->title && ! $post->isShared())
                        <h4 class="mt-2 font-medium text-[var(--app-text)]">{{ $post->title }}</h4>
                    @endif
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed opacity-90">
                        {{ \Illuminate\Support\Str::limit($post->body, 320) }}
                    </p>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No published posts yet.') }}</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app.page>
