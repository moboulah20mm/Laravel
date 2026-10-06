<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecretPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/geheim')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_the_secret_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/geheim')
            ->assertOk()
            ->assertSee('Geheime pagina');
    }
}
