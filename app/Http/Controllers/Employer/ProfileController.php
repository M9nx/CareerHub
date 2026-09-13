<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateEmployerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->employerProfile;

        if (! $profile) {
            $profile = $request->user()->employerProfile()->create([
                'company_name' => '',
            ]);
        }

        return view('employer.profile.edit', compact('profile'));
    }

    public function update(
        UpdateEmployerProfileRequest $request
    ): RedirectResponse {
        $request->user()->employerProfile()->updateOrCreate(
            [],
            $request->validated()
        );

        return redirect()
            ->route('employer.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
