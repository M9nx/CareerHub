<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        abort_unless(static::canView(), 403);
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total Users'), User::query()->count())
                ->description(__('All registered users'))
                ->color('success'),
            Stat::make(__('Open Jobs'), JobPosting::query()->where('status', JobPostingStatus::Published)->count())
                ->description(__('Published job postings'))
                ->color('primary'),
            Stat::make(
                __('Pending Applications'),
                Application::query()->where('status', ApplicationStatus::Submitted)->count()
            )
                ->description(__('Applications awaiting review'))
                ->color('warning'),
        ];
    }
}
