<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateEmployerProfileRequest;
use App\Services\LocalDocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    public function update(
        UpdateEmployerProfileRequest $request,
        LocalDocumentStorage $documents,
    ): RedirectResponse {
        $profile = $request->user()->employerProfile()->firstOrCreate([], [
            'company_name' => '',
        ]);

        $attributes = $request->safe()->only([
            'company_name',
            'industry',
            'company_size',
            'location',
            'website',
            'about',
        ]);

        if ($request->hasFile('logo')) {
            if (filled($profile->logo_path)) {
                Storage::disk('public')->delete($profile->logo_path);
            }

            $attributes['logo_path'] = $documents->store($request->file('logo'), 'company-logos');
        }

        $profile->update($attributes);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Profile updated successfully.'));
    }
}
