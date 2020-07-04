<?php

namespace Tests;

use EasyRSA\Config;
use EasyRSA\Interfaces\ConfigInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \EasyRSA\Interfaces\ConfigInterface
     */
    private $object;

    public function setUp(): void
    {
        $this->object = new Config();
    }

    public function testConstructorException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Config([
            'wrong' => 'value',
        ]);
    }

    public function testConstructor(): void
    {
        $object = new Config();
        $this->assertInstanceOf(ConfigInterface::class, $object);
    }

    public function testSetException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->set('wrong', 'test');
    }

    public function testSet(): void
    {
        $this->object->set('archive', './tests/archive.tar.gz');
        $this->assertStringContainsString('archive.tar.gz', $this->object->archive);

        $this->object->set('archive', './archive.tar.gz', false);
        $this->assertStringContainsString('archive.tar.gz', $this->object->archive);
        $this->assertEquals('./archive.tar.gz', $this->object->archive);
    }

    public function testGetException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->get('wrong');
    }

    public function testGet(): void
    {
        $test1 = $this->object->get('archive');
        $test2 = $this->object->get('scripts');
        $test3 = $this->object->get('certs');

        $this->assertEquals('.' . DIRECTORY_SEPARATOR . 'easy-rsa.tar.gz', $test1);
        $this->assertEquals('.' . DIRECTORY_SEPARATOR . 'easy-rsa', $test2);
        $this->assertEquals('.', $test3);
    }

    public function testResolvePath(): void
    {
        $test1 = $this->object->resolvePath('/home/test');
        $test2 = $this->object->resolvePath('./test', '/home');
        $test3 = $this->object->resolvePath('./../test', '/home');

        $this->assertEquals('/home/test', $test1);
        $this->assertEquals('/home/test', $test2);
        $this->assertEquals('/test', $test3);
    }
}
