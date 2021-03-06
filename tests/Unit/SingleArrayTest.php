<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bardo
 * Date: 2020-08-31
 * Time: 0:30.
 */

namespace Bardoqi\Sight\Tests\Unit;

use Bardoqi\Sight\Tests\Fixture\UserPresenter;
use Bardoqi\Sight\Tests\TestCase;

/**
 * Class SingleArrayTest.
 */
final class SingleArrayTest extends TestCase
{
    public $userPresenter;

    /** @test */
    public function testPresenterCreate()
    {
        $user_array_string = include dirname(dirname(__DIR__)).'/tests/Fixture/Users.php';
        $user_array = json_decode($user_array_string, true);
        $user = new UserPresenter();
        $users = $user->selectFields(['id', 'username', 'mobile', 'name', 'avatar_id', 'created_at', 'updated_at'])
         ->fromLocal($user_array, 'user')
         ->toArray();
        $this->assertTrue(is_array($users));
    }
}
