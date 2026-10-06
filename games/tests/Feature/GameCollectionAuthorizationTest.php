<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameCollectionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_game_overview(): void
    {
        $this->get('/games')->assertOk();
    }

    public function test_guests_are_redirected_from_game_mutation_routes(): void
    {
        foreach ([
            ['GET', '/games/create'],
            ['POST', '/games/store'],
            ['GET', '/games/edit/1'],
            ['POST', '/games/update/1'],
            ['POST', '/games/destroy/1'],
        ] as [$method, $uri]) {
            $this->call($method, $uri)->assertRedirect('/login');
        }
    }
}
