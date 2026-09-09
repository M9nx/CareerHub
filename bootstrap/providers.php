<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\SuperAdminPanelProvider;

return [
    AppServiceProvider::class,
    // Deprecated: legacy /admin panel — replaced by SuperAdminPanelProvider (issue #10).
    // App\Providers\Filament\AdminPanelProvider::class,
    SuperAdminPanelProvider::class,
];
