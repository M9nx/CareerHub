<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateEmployerProfileRequest;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    public function update(
        UpdateEmployerProfileRequest $request
    ): RedirectResponse {
        $request->user()->employerProfile()->updateOrCreate(
            [],
            $request->validated()
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Profile updated successfully.'));
    }
}
