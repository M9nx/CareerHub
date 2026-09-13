@extends('layouts.app')

@section('content')

<div class="container py-5">

    <h1>Edit Employer Profile</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employer.profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="company_name" class="form-label">
                Company Name
            </label>

            <input
                type="text"
                id="company_name"
                name="company_name"
                class="form-control"
                value="{{ old('company_name', $profile->company_name) }}"
                required
                maxlength="255"
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Update Profile
        </button>
    </form>

</div>

@endsection