<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
/**
 * Created by PhpStorm.
 * User: polina
 * Date: 09/04/17
 * Time: 13:28
 */
class UsersControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        factory(User::class, 20)->create(["broker_id" => 1]);
        factory(User::class, 20)->create(["broker_id" => 2]);
        $user = $this->createUserWithPermission('admin');

        $this->actingAs($user);

    }

    private function createUserWithPermission($permissiom)
    {
        $user = factory(\App\User::class)->create();
        $user->assignPermission($permissiom);
        return $user;
    }

    /** @test */
    public function if_fetches_all_users_by_broker()
    {

//        $response = $this->action('GET', 'AppUserController@index');
//        dd($response->getContent());
//        $users = User::all();
//        $this->assertCount(20, $users);
    }
}