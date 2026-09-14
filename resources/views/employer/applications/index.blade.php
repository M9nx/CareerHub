@extends('layouts.app')

@section('content')
    <h1>Applications</h1>

    @foreach ($applications as $application)
        <div>
            <p>{{ $application->employee->name }}</p>
            <p>{{ $application->status->value }}</p>

            <form
                method="POST"
                action="{{ route('employer.applications.update', $application) }}"
            >
                @csrf
                @method('PATCH')

                <select name="status">
                    <option value="under_review">Under Review</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>

                <button type="submit">
                    Update
                </button>
            </form>
        </div>
    @endforeach
@endsection