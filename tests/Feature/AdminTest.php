<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_shows_users_page_to_user_with_admin_permission()
    {
        $user = $this->createUserWithPermission('admin');

        $this->actingAs($user)
            ->get("/admin/users");
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_blocks_users_page_to_user_without_admin_permission()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->get("/admin/users");
        $this->assertRedirectedTo('/');
    }

    private function createUserWithPermission($permission)
    {
        $user = factory(\App\User::class)->create();
        $user->assignPermission($permission);
        return $user;
    }
}
