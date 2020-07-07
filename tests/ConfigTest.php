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

    public function getDataProvider(): array
    {
        return [
            ['key' => 'archive', 'result' => '.' . DIRECTORY_SEPARATOR . 'easy-rsa.tar.gz'],
            ['key' => 'scripts', 'result' => '.' . DIRECTORY_SEPARATOR . 'easy-rsa'],
            ['key' => 'certs', 'result' => '.' . DIRECTORY_SEPARATOR . 'easy-rsa-certs'],
        ];
    }

    /**
     * @dataProvider getDataProvider
     *
     * @param string           $key
     * @param string|bool|null $result
     */
    public function testGet(string $key, $result): void
    {
        $test = $this->object->get($key);
        $this->assertEquals($result, $test);
    }

    public function resolvePathDataProvider(): array
    {
        return [
            ['path' => '/home/test', 'result' => '/home/test'],
            ['path' => '/home/../test', 'result' => '/test'],
            ['path' => '/test', 'result' => '/test'],
            ['path' => '/../test', 'result' => 'test'],
        ];
    }

    /**
     * @dataProvider resolvePathDataProvider
     *
     * @param string $path
     * @param string $result
     */
    public function testResolvePath(string $path, string $result): void
    {
        $test = $this->object->resolvePath($path);
        $this->assertEquals($result, $test);
    }
}
