<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

test('employee can create a published post', function () {
    $employee = actingAsEmployee();

    $this->post(route('employee.posts.store'), [
        'title' => 'Employee Career Post',
        'body' => 'This is an employee career post.',
        'publish' => '1',
    ])
        ->assertRedirect(route('employee.posts.index'))
        ->assertSessionHas('success', __('Post created successfully.'));

    $this->assertDatabaseHas('posts', [
        'author_id' => $employee->id,
        'author_role' => UserRole::Employee->value,
        'title' => 'Employee Career Post',
        'body' => 'This is an employee career post.',
        'status' => PostStatus::Published->value,
        'is_active' => true,
    ]);
});

test('published employee post is visible on the shared feed', function () {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->published()->create([
        'author_role' => UserRole::Employee,
        'title' => 'Employee Feed Post',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Employee Feed Post');
});

test('employee can update own post', function () {
    $employee = actingAsEmployee();
    $post = Post::factory()->for($employee, 'author')->create([
        'author_role' => UserRole::Employee,
        'title' => 'Old Employee Post',
    ]);

    $this->put(route('employee.posts.update', $post), [
        'title' => 'Updated Employee Post',
        'body' => 'Updated employee post body.',
        'publish' => '1',
    ])->assertRedirect(route('employee.posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'author_id' => $employee->id,
        'title' => 'Updated Employee Post',
        'body' => 'Updated employee post body.',
        'status' => PostStatus::Published->value,
    ]);
});

test('employee can delete own post', function () {
    $employee = actingAsEmployee();
    $post = Post::factory()->for($employee, 'author')->create([
        'author_role' => UserRole::Employee,
    ]);

    $this->delete(route('employee.posts.destroy', $post))
        ->assertRedirect(route('employee.posts.index'));

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

test('employee cannot update another users post', function () {
    actingAsEmployee();
    $otherEmployee = User::factory()->employee()->create();
    $post = Post::factory()->for($otherEmployee, 'author')->create([
        'author_role' => UserRole::Employee,
    ]);

    $this->put(route('employee.posts.update', $post), [
        'title' => 'Unauthorized Update',
        'body' => 'This update should not be allowed.',
        'publish' => '1',
    ])->assertForbidden();

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
        'title' => 'Unauthorized Update',
    ]);
});

test('employee post show route is not registered', function () {
    $employee = actingAsEmployee();
    $post = Post::factory()->for($employee, 'author')->create([
        'author_role' => UserRole::Employee,
    ]);

    $this->get('/employee/posts/'.$post->id)->assertMethodNotAllowed();
});

test('employee posts index lists own posts', function () {
    $employee = actingAsEmployee();
    Post::factory()->for($employee, 'author')->create([
        'author_role' => UserRole::Employee,
        'title' => 'Own Employee Post',
    ]);

    $this->get(route('employee.posts.index'))
        ->assertOk()
        ->assertSee('Own Employee Post')
        ->assertSee(route('employee.posts.create'), false);
});
