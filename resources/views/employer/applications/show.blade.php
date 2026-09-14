@extends('layouts.app')

@section('content')
    <h1>Application Details</h1>

    <p>{{ $application->employee->name }}</p>
    <p>{{ $application->status->value }}</p>
    <p>{{ $application->cover_letter }}</p>
@endsection