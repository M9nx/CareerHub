<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileSkillRequest;
use App\Http\Requests\UpdateProfileSkillRequest;
use App\Models\ProfileSkill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProfileSkillController extends Controller
{
    public function store(StoreProfileSkillRequest $request): RedirectResponse
    {
        $user = $request->user();
        $nextSort = (int) $user->profileSkills()->max('sort') + 1;

        $user->profileSkills()->create([
            ...$request->validated(),
            'sort' => $nextSort,
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Skill added.'));
    }

    public function update(
        UpdateProfileSkillRequest $request,
        ProfileSkill $skill,
    ): RedirectResponse {
        $skill->update($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Skill updated.'));
    }

    public function destroy(ProfileSkill $skill): RedirectResponse
    {
        Gate::authorize('delete', $skill);

        $skill->delete();

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Skill removed.'));
    }
}
