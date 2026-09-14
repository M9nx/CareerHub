<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-6 rounded-md bg-red-50 p-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employee.posts.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Title')" />

                            <x-text-input
                                id="title"
                                name="title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('title')"
                                required
                                autofocus
                            />

                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="body" :value="__('Body')" />

                            <textarea
                                id="body"
                                name="body"
                                rows="8"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >{{ old('body') }}</textarea>

                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <label for="publish" class="inline-flex items-center">
                                <input
                                    id="publish"
                                    name="publish"
                                    type="checkbox"
                                    value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked(old('publish'))
                                >

                                <span class="ms-2 text-sm text-gray-600">
                                    {{ __('Publish post') }}
                                </span>
                            </label>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a
                                href="{{ route('employee.posts.index') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900"
                            >
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Create Post') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>