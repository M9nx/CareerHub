<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeProfileRequest;
use App\Models\EmployeeProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->employeeProfile;

        if (! $profile) {
            $profile = EmployeeProfile::create([
                'user_id' => $request->user()->id,
            ]);
        }

        return view('employee.profile.edit', compact('profile'));
    }

    public function update(
        UpdateEmployeeProfileRequest $request
    ): RedirectResponse {
        $profile = $request->user()->employeeProfile;

        if (! $profile) {
            $profile = EmployeeProfile::create([
                'user_id' => $request->user()->id,
            ]);
        }

        $profile->update($request->validated());

        return redirect()
            ->route('employee.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
