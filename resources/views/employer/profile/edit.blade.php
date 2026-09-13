<x-app-layout>  
 <x-slot name="header">  
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">  Edit Employer Profil </h2> </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('employer.profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label
                            for="company_name"
                            :value="__('Company Name')"
                        />

                        <x-text-input
                            id="company_name"
                            name="company_name"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('company_name', $profile->company_name)"
                            required
                            autofocus
                            maxlength="255"
                        />

                        <x-input-error
                            class="mt-2"
                            :messages="$errors->get('company_name')"
                        />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>
                            {{ __('Update Profile') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</x-app-layout>
