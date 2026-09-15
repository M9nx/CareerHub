<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileEducationRequest;
use App\Http\Requests\UpdateProfileEducationRequest;
use App\Models\ProfileEducation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProfileEducationController extends Controller
{
    public function store(StoreProfileEducationRequest $request): RedirectResponse
    {
        $request->user()->profileEducations()->create($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Education added.'));
    }

    public function update(
        UpdateProfileEducationRequest $request,
        ProfileEducation $education,
    ): RedirectResponse {
        $education->update($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Education updated.'));
    }

    public function destroy(ProfileEducation $education): RedirectResponse
    {
        Gate::authorize('delete', $education);

        $education->delete();

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Education removed.'));
    }
}
