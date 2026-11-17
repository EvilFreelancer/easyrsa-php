<?php

namespace Tests\EasyRSA;

use EasyRSA\Worker;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class WorkerTest extends TestCase
{
    /**
     * @var array<string, string>
     */
    private $savedEnv = [];

    /**
     * @var array<string, mixed>
     */
    private $savedServer = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Snapshot the environment keys we may touch so tests stay isolated
        foreach (['EASYRSA_PKI', 'EASYRSA_DN', 'EASYRSA_REQ_CN', 'EASYRSA_KEY_SIZE', 'EASYRSA_PASSIN', 'CA_PASS'] as $key) {
            $this->savedEnv[$key] = getenv($key);
            $this->savedServer[$key] = $_SERVER[$key] ?? null;
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->savedEnv as $key => $value) {
            if (false === $value) {
                putenv($key);
                unset($_ENV[$key]);
            } else {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }

        foreach ($this->savedServer as $key => $value) {
            if (null === $value) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }

        parent::tearDown();
    }

    private function worker(): Worker
    {
        return new Worker([
            'scripts' => '/opt/easy-rsa',
            'certs'   => '/tmp/easy-rsa-certs',
        ]);
    }

    public function testConstructorExportsPki(): void
    {
        $this->worker();

        $this->assertSame('/tmp/easy-rsa-certs', getenv('EASYRSA_PKI'));
        $this->assertSame('/tmp/easy-rsa-certs', $_ENV['EASYRSA_PKI']);
    }

    public function testSetEnvExportsOnlyEasyrsaVars(): void
    {
        $exported = $this->worker()->setEnv([
            'EASYRSA_DN'    => 'cn_only',
            'EASYRSA_KEY_SIZE' => '2048',
            'NOT_EASYRSA'   => 'ignored',
        ]);

        $this->assertSame('cn_only', getenv('EASYRSA_DN'));
        $this->assertSame('2048', getenv('EASYRSA_KEY_SIZE'));
        $this->assertFalse(getenv('NOT_EASYRSA'));

        $this->assertArrayHasKey('EASYRSA_DN', $exported);
        $this->assertArrayNotHasKey('NOT_EASYRSA', $exported);
    }

    public function testLoadVarsParsesFileAndIgnoresComments(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'vars');
        file_put_contents($file, implode("\n", [
            '# a comment',
            '',
            'EASYRSA_DN="org"',
            "EASYRSA_REQ_CN='ChangeMe'",
            'EASYRSA_KEY_SIZE=4096',
            'SOMETHING_ELSE=nope',
        ]));

        try {
            $loaded = $this->worker()->loadVars($file);
        } finally {
            unlink($file);
        }

        $this->assertSame('org', getenv('EASYRSA_DN'));
        $this->assertSame('ChangeMe', getenv('EASYRSA_REQ_CN'));
        $this->assertSame('4096', getenv('EASYRSA_KEY_SIZE'));
        $this->assertFalse(getenv('SOMETHING_ELSE'));

        $this->assertSame(
            ['EASYRSA_DN' => 'org', 'EASYRSA_REQ_CN' => 'ChangeMe', 'EASYRSA_KEY_SIZE' => '4096'],
            $loaded
        );
    }

    public function testLoadVarsThrowsForMissingFile(): void
    {
        $this->expectException(RuntimeException::class);

        $this->worker()->loadVars('/no/such/vars/file');
    }

    public function testGetEnvReturnsSortedEasyrsaVars(): void
    {
        $worker = $this->worker();
        $worker->setEnv(['EASYRSA_REQ_CN' => 'z', 'EASYRSA_DN' => 'a']);

        $env = $worker->getEnv();

        $this->assertSame(['EASYRSA_DN', 'EASYRSA_PKI', 'EASYRSA_REQ_CN'], array_keys($env));
    }

    /**
     * The core of issue #4: values that a Dotenv-style loader placed only in
     * $_ENV must reach the subprocess environment before exec() launches the
     * easyrsa script. We assert the propagation by inspecting the environment
     * right after a dry-run exec() (which calls syncEnv() internally).
     */
    public function testExecSyncsEnvFromSuperGlobal(): void
    {
        $worker = $this->worker();
        $worker->dryRun = true;

        // Simulate Dotenv::createImmutable(...)->load() which only fills $_ENV
        $_ENV['EASYRSA_DN'] = 'org';
        $this->assertFalse(getenv('EASYRSA_DN'), 'Precondition: not yet in real env');

        $worker->exec('init-pki');

        $this->assertSame('org', getenv('EASYRSA_DN'));
    }

    public function testExecSyncsEnvFromServerSuperGlobal(): void
    {
        $worker = $this->worker();
        $worker->dryRun = true;

        $_SERVER['EASYRSA_REQ_CN'] = 'from-server';

        $worker->exec('init-pki');

        $this->assertSame('from-server', getenv('EASYRSA_REQ_CN'));
    }

    public function testExecAlwaysOwnsPkiFromConfig(): void
    {
        $worker = $this->worker();
        $worker->dryRun = true;

        // A stale PKI value in $_ENV must not override the worker config
        $_ENV['EASYRSA_PKI'] = '/somewhere/else';

        $worker->exec('init-pki');

        $this->assertSame('/tmp/easy-rsa-certs', getenv('EASYRSA_PKI'));
    }

    public function testDryRunReturnsCommandWithoutExecution(): void
    {
        $worker = $this->worker();
        $worker->dryRun = true;

        $result = $worker->exec('init-pki');

        $this->assertCount(1, $result);
        $this->assertStringContainsString('/easyrsa3/easyrsa --batch init-pki', $result[0]);
    }
}
