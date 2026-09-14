@extends('layouts.app')

@section('content')
    <h1>My Applications</h1>

    @foreach ($applications as $application)
        <div>
            <p>{{ $application->jobPosting->title }}</p>
            <p>{{ $application->status->value }}</p>

            @if (! $application->isCancelled())
                <form
                    method="POST"
                    action="{{ route('employee.applications.cancel', $application) }}"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Cancel
                    </button>
                </form>
            @endif
        </div>
    @endforeach
@endsection