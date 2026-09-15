<?php

use App\Enums\ConnectionStatus;
use App\Models\Connection;
use App\Models\User;

test('user can send a connection request to another persona', function () {
    $requester = actingAsEmployee(['name' => 'Requester']);
    $addressee = User::factory()->employer()->create(['name' => 'Addressee']);

    $this->from(route('people.show', $addressee))
        ->post(route('network.connect', $addressee))
        ->assertRedirect(route('people.show', $addressee))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('connections', [
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
        'status' => ConnectionStatus::Pending->value,
    ]);
});

test('addressee can accept a pending connection', function () {
    $requester = User::factory()->employee()->create();
    $addressee = actingAsEmployer();
    $connection = Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->post(route('network.connections.accept', $connection))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($connection->fresh()->status)->toBe(ConnectionStatus::Accepted);
});

test('addressee can ignore a pending connection', function () {
    $requester = User::factory()->employee()->create();
    $addressee = actingAsEmployer();
    $connection = Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->post(route('network.connections.reject', $connection))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($connection->fresh()->status)->toBe(ConnectionStatus::Rejected);
});

test('requester can withdraw a pending connection', function () {
    $requester = actingAsEmployee();
    $addressee = User::factory()->employer()->create();
    $connection = Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->post(route('network.connections.withdraw', $connection))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($connection->fresh()->status)->toBe(ConnectionStatus::Withdrawn);
});

test('either party can remove an accepted connection', function () {
    $requester = actingAsEmployee();
    $addressee = User::factory()->employer()->create();
    $connection = Connection::factory()->accepted()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->delete(route('network.connections.destroy', $connection))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('connections', [
        'id' => $connection->id,
    ]);
});

test('user cannot accept a request meant for someone else', function () {
    $requester = User::factory()->employee()->create();
    $addressee = User::factory()->employer()->create();
    actingAsEmployee();
    $connection = Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->post(route('network.connections.accept', $connection))
        ->assertForbidden();
});

test('user cannot connect to themselves', function () {
    $user = actingAsEmployee();

    $this->from(route('people.show', $user))
        ->post(route('network.connect', $user))
        ->assertRedirect(route('people.show', $user))
        ->assertSessionHasErrors('user');
});

test('duplicate open connection requests are rejected', function () {
    $requester = actingAsEmployee();
    $addressee = User::factory()->employer()->create();

    Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->from(route('people.show', $addressee))
        ->post(route('network.connect', $addressee))
        ->assertRedirect(route('people.show', $addressee))
        ->assertSessionHasErrors('user');
});
