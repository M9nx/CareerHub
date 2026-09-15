@if (session('success'))
    <p class="app-alert-success" role="status">
        {{ session('success') }}
    </p>
@endif

@if (session('error'))
    <div class="app-alert-error" role="alert">
        {{ session('error') }}
    </div>
@endif
