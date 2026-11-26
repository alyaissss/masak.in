<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MobileNavigationTest extends TestCase
{
    #[DataProvider('roleNavigationItems')]
    public function test_mobile_navigation_contains_links_for_each_user_role(string $role, array $items): void
    {
        $this->actingAs(new User(['role' => $role]));
        $html = view('layouts.app')->render();

        $this->assertStringContainsString('aria-label="Navigasi utama"', $html);
        foreach ($items as $item) {
            $this->assertStringContainsString('data-testid="mobile-nav-'.$item.'"', $html);
        }
    }

    public static function roleNavigationItems(): array
    {
        return [
            'admin' => [User::ROLE_ADMIN, ['dashboard', 'data-chef', 'data-learner', 'data-kelas', 'pembayaran']],
            'chef' => [User::ROLE_CHEF, ['dashboard', 'kelas-saya', 'forum-diskusi']],
            'learner' => [User::ROLE_LEARNER, ['dashboard', 'katalog-kelas', 'pembayaran-saya']],
        ];
    }
}
