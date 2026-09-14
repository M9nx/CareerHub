<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeProfileRequest;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    public function update(UpdateEmployeeProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->employeeProfile;

        if (! $profile) {
            $profile = $request->user()->employeeProfile()->create([
                'cv_path' => '',
            ]);
        }

        $profile->update($request->validated());

        return redirect()->route('profile.edit');
    }
}
