<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileExperienceRequest;
use App\Http\Requests\UpdateProfileExperienceRequest;
use App\Models\ProfileExperience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProfileExperienceController extends Controller
{
    public function store(StoreProfileExperienceRequest $request): RedirectResponse
    {
        $request->user()->profileExperiences()->create($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Experience added.'));
    }

    public function update(
        UpdateProfileExperienceRequest $request,
        ProfileExperience $experience,
    ): RedirectResponse {
        $experience->update($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Experience updated.'));
    }

    public function destroy(ProfileExperience $experience): RedirectResponse
    {
        Gate::authorize('delete', $experience);

        $experience->delete();

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Experience removed.'));
    }
}
