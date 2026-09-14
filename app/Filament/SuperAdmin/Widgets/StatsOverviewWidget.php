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
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->color('success'),
            Stat::make('Open Jobs', JobPosting::where('status', JobPostingStatus::Published)->count())
                ->description('Active job postings')
                ->color('primary'),
            Stat::make('Pending Applications', Application::where('status', ApplicationStatus::Pending)->count())
                ->description('Applications awaiting review')
                ->color('warning'),
        ];
    }
}
