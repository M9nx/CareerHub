<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateEmployeeProfileRequest;
use App\Services\LocalDocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function downloadCv(Request $request): StreamedResponse
    {
        $profile = $request->user()->employeeProfile;

        abort_unless(
            $profile !== null && filled($profile->cv_path),
            404,
        );

        abort_unless(
            Storage::disk('public')->exists($profile->cv_path),
            404,
        );

        $downloadName = sprintf(
            '%s-cv.%s',
            Str::slug($request->user()->name) ?: 'employee',
            pathinfo($profile->cv_path, PATHINFO_EXTENSION) ?: 'pdf',
        );

        return Storage::disk('public')->download($profile->cv_path, $downloadName);
    }

    public function downloadApplicationImage(Request $request): StreamedResponse
    {
        $profile = $request->user()->employeeProfile;

        abort_unless(
            $profile !== null && filled($profile->application_image_path),
            404,
        );

        abort_unless(
            Storage::disk('public')->exists($profile->application_image_path),
            404,
        );

        $downloadName = sprintf(
            '%s-application-image.%s',
            Str::slug($request->user()->name) ?: 'employee',
            pathinfo($profile->application_image_path, PATHINFO_EXTENSION) ?: 'jpg',
        );

        return Storage::disk('public')->download(
            $profile->application_image_path,
            $downloadName,
        );
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
