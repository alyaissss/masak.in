<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    #[DataProvider('roleDashboardRoutes')]
    public function test_authenticated_users_are_redirected_to_their_role_dashboard(string $role, string $route): void
    {
        $this->actingAs(new User(['role' => $role]))
            ->get('/login')
            ->assertRedirect(route($route));
    }

    public static function roleDashboardRoutes(): array
    {
        return [
            'admin' => [User::ROLE_ADMIN, 'admin.dashboard'],
            'chef' => [User::ROLE_CHEF, 'chef.dashboard'],
            'learner' => [User::ROLE_LEARNER, 'learner.dashboard'],
        ];
    }
}
