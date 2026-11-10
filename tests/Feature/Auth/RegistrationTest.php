<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Test User',
            'nim_nik' => '2201019999',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'putra',
            'no_telepon' => '081234567890',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin_asrama',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'nim_nik' => '2201019999',
            'nik' => '3201010101010001',
            'role' => 'penghuni',
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }
}
