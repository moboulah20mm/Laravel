<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GameCollectionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_all_game_pages(): void
    {
        foreach ([
            ['GET', '/games'],
            ['GET', '/games/show/1'],
            ['GET', '/games/create'],
            ['POST', '/games/store'],
            ['GET', '/games/edit/1'],
            ['POST', '/games/update/1'],
            ['POST', '/games/destroy/1'],
        ] as [$method, $uri]) {
            $this->call($method, $uri)->assertRedirect('/login');
        }
    }

    public function test_customers_can_view_only_the_game_overview(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create(['name' => 'klant', 'guard_name' => 'web']));
        $game = Game::create([
            'game_name' => 'Test Game',
            'platform' => 'PC',
            'genre' => 'RPG',
            'rating' => 8,
        ]);

        $this->actingAs($customer)->get('/games')->assertOk();

        foreach ([
            ['GET', "/games/show/{$game->id}"],
            ['GET', '/games/create'],
            ['POST', '/games/store'],
            ['GET', "/games/edit/{$game->id}"],
            ['POST', "/games/update/{$game->id}"],
            ['POST', "/games/destroy/{$game->id}"],
        ] as [$method, $uri]) {
            $this->call($method, $uri)->assertForbidden();
        }
    }

    public function test_admins_can_access_all_game_pages_and_actions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $game = Game::create([
            'game_name' => 'Test Game',
            'platform' => 'PC',
            'genre' => 'RPG',
            'rating' => 8,
        ]);

        $this->actingAs($admin);

        $this->get('/games')->assertOk();
        $this->get("/games/show/{$game->id}")->assertOk();
        $this->get('/games/create')->assertOk();
        $this->get("/games/edit/{$game->id}")->assertOk();
        $this->post('/games/store', [
            'game_name' => 'New Game',
            'platform' => 'PC',
            'genre' => 'Action',
            'rating' => 9,
        ])->assertRedirect('/games');
        $this->post("/games/update/{$game->id}", [
            'game_name' => 'Updated Game',
            'platform' => 'PC',
            'genre' => 'RPG',
            'rating' => 7,
        ])->assertRedirect('/games');
        $this->post("/games/destroy/{$game->id}")->assertRedirect('/games');
    }
}
