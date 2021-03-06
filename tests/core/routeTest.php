<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wolff\Core\Route;
use Wolff\Exception\InvalidArgumentException;

class RouteTest extends TestCase
{


    public function setUp(): void
    {
        Route::get('/', function () {
            return 'in root';
        });

        Route::get('plain:home', function () {
            return 'hello world';
        });

        Route::get('418', function () {
            return '418';
        });

        Route::get('home2', function () {
            return 'redirected';
        });

        Route::get('home/{id}', function () {
            return 'Parameter: ' . $_GET['id'];
        }, 200);

        Route::get('optional/{id2?}', function () {
            return 'Parameter: ' . ($_GET['id2'] ?? '');
        });

        Route::any('blog/{page?}/dark', function () {
            return 'in page ' . $_GET['page'];
        });

        Route::code(404, function ($req, $res) {
            $req->foo = 'bar';
            $res->foo = 'bar';
        });
    }


    public function testInit()
    {
        $this->assertTrue(Route::exists('home'));
        $this->assertTrue(Route::exists('418'));
        $this->assertTrue(Route::exists('home/{}'));
        $this->assertNotEmpty(Route::getRoutes());

        //Redirects
        $this->assertNull(Route::getRedirection('invalid_redirect'));
        $this->assertEmpty(Route::getRedirects());
        Route::redirect('page1', 'home2');
        $this->assertNotEmpty(Route::getRedirects());
        $this->assertEquals([
            'from' => 'page1',
            'to'   => 'home2',
            'code' => 301,
        ], Route::getRedirection('page1'));
        Route::redirect('post/redirect/*', 'home2');
        $this->assertEquals([
            'from' => 'post/redirect/*',
            'to'   => 'home2',
            'code' => 301,
        ], Route::getRedirection('post/redirect/sub'));
        $this->assertNull(Route::getRedirection('invalid_redirect'));
        $this->assertEquals('redirected', @Route::getFunction('home2')());

        //Route functions
        $this->assertNull(Route::invalid_method());
        $this->assertEquals('in root', @Route::getFunction('')());
        $this->assertEquals('418', @Route::getFunction('418')());
        $this->assertEquals('Parameter: 15048', @Route::getFunction('home/15048')());
        $this->assertEquals('Parameter: ', @Route::getFunction('optional/')());
        $this->assertEquals('Parameter: 123', @Route::getFunction('optional/123')());
        $this->assertEquals('in page 12', @Route::getFunction('blog/12/dark')());
        $this->assertNull(@Route::getFunction('blog/12/'));
        $this->assertNull(@Route::getFunction('blog/12/white'));
        $this->assertNull(@Route::getFunction('home/123/another'));

        //Code
        $req = new \Wolff\Core\Http\Request([], [], [], [], []);
        $res = new \Wolff\Core\Http\Response;
        $this->assertNull(Route::execCode(202, $req, $res));
        Route::execCode(404, $req, $res);
        $this->assertEquals('bar', $req->foo);
        $this->assertEquals('bar', $res->foo);

        //Blocked
        $this->assertEmpty(Route::getBlocked());
        Route::block('main_page');
        $this->assertNotEmpty(Route::getBlocked());
        Route::block('home/*');
        $this->assertTrue(Route::isBlocked('home/testing'));
        $this->assertFalse(Route::isBlocked('home2/testing'));
        Route::block('*');
        $this->assertTrue(Route::isBlocked('home'));
        $this->assertTrue(Route::isBlocked('another_route'));
        $this->expectException(InvalidArgumentException::class);
        Route::get([ 'route' ], []);
        Route::get('route');
    }
}
