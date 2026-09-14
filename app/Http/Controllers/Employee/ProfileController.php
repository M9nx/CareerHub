<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeProfileRequest;
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
        UpdateEmployeeProfileRequest $request,
        LocalDocumentStorage $documents,
    ): RedirectResponse {
        $profile = $request->user()->employeeProfile;

        if (! $profile) {
            $profile = $request->user()->employeeProfile()->create([
                'cv_path' => '',
            ]);
        }

        $attributes = [];

        if ($request->hasFile('cv')) {
            $this->deleteStoredFile($profile->cv_path);

            $attributes['cv_path'] = $documents->store(
                $request->file('cv'),
                'cvs',
            );
        }

        if ($request->hasFile('application_image')) {
            $this->deleteStoredFile($profile->application_image_path);

            $attributes['application_image_path'] = $documents->store(
                $request->file('application_image'),
                'application-images',
            );
        }

        if ($attributes !== []) {
            $profile->update($attributes);

            return redirect()
                ->route('profile.edit')
                ->with('success', __('Career documents updated successfully.'));
        }

        return redirect()->route('profile.edit');
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
