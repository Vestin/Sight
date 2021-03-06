<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bardo
 * Date: 2020-08-31
 * Time: 0:31.
 */

namespace Bardoqi\Sight\Tests\Unit;

use Bardoqi\Sight\Tests\Fixture\UserPresenter;
use Bardoqi\Sight\Tests\TestCase;

/**
 * Class HasOneTest.
 */
final class HasOneTest extends TestCase
{
    /** @test */
    public function testPresenterJoin()
    {
        $user_array_string = include dirname(dirname(__DIR__)).'/tests/Fixture/Users.php';
        $user_array = json_decode($user_array_string, true);

        $user = new UserPresenter();
        $users = $user->selectFields(['id', 'username', 'mobile', 'name', 'avatar_id', 'img_url', 'created_at', 'updated_at'])
            ->fromLocal($user_array, 'user');

        $avatar_ids = $users->pluck('avatar_id');

        $image_array_string = include dirname(dirname(__DIR__)).'/tests/Fixture/Images.php';
        $image_array = json_decode($image_array_string, true);

        $users = $users->innerJoinForeign($image_array, 'images')
            ->onRelation('avatar_id', 'images', 'id')
            ->toArray();

        $this->assertTrue(is_array($users));
        $this->assertTrue(isset($users[0]['img_url']));
    }

    /** @test */
    public function testItemPresenter()
    {
        $user_array_string = include dirname(dirname(__DIR__)).'/tests/Fixture/Users.php';
        $user_array = json_decode($user_array_string, true);

        $user = new UserPresenter();
        $users = $user->selectFields(['id', 'username', 'mobile', 'name', 'avatar_id', 'img_url', 'created_at', 'updated_at'])
            ->fromLocalItem($user_array[0], 'user');

        $avatar_ids = $users->pluck('avatar_id');

        $image_array_string = include dirname(dirname(__DIR__)).'/tests/Fixture/Images.php';
        $image_array = json_decode($image_array_string, true);

        $users = $users->innerJoinForeign($image_array, 'images')
            ->onRelation('avatar_id', 'images', 'id')
            ->toItemArray();

        $this->assertTrue(is_array($users));
        $this->assertTrue(isset($users['img_url']));
    }
}
