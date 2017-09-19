 <?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    /** @test */
    public function it_redirects_unauthenticated_users_to_login_page()
    {
        $this->visit('/')->seePageIs('/login');

    }
}
