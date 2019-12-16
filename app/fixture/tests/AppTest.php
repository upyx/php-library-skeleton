<?php

declare(strict_types=1);

namespace SomeApp\Test;

use SomeApp\App;

class AppTest extends TestCase
{
    public function testGetHello()
    {
        $object = \Mockery::mock(App::class);
        $object->shouldReceive('getHello')->passthru();

        $this->assertSame('Hello, World!', $object->getHello());
    }
}
