<?php

namespace Tests\EasyRSA;

use EasyRSA\Commands;
use PHPUnit\Framework\TestCase;

class CommandsTest extends TestCase
{
    /**
     * Build a Commands instance in dry-run mode so exec() only returns
     * the assembled command string without launching the easyrsa script.
     */
    private function commands(): Commands
    {
        $cmd = new Commands([
            'scripts' => '/opt/easy-rsa',
            'certs'   => '/tmp/easy-rsa-certs',
        ]);

        $cmd->dryRun = true;

        return $cmd;
    }

    public function testBuildClientFullWithoutPassin(): void
    {
        $result = $this->commands()->buildClientFull('client1', true);

        $this->assertCount(1, $result);
        $this->assertStringContainsString('build-client-full \'client1\' nopass', $result[0]);
        $this->assertStringNotContainsString('--passin', $result[0]);
    }

    public function testBuildClientFullWithPassin(): void
    {
        $result = $this->commands()->buildClientFull('client1', false, 'env:CA_PASS');

        $command = $result[0];

        // Global option must be present, escaped, and placed before the command
        $this->assertStringContainsString("--passin='env:CA_PASS'", $command);
        $this->assertStringContainsString("build-client-full 'client1'", $command);
        $this->assertLessThan(
            strpos($command, 'build-client-full'),
            strpos($command, '--passin'),
            'Global --passin option must precede the command name'
        );
    }

    public function testBuildServerFullWithPassin(): void
    {
        $result = $this->commands()->buildServerFull('server', true, 'file:/run/secrets/ca.pass');

        $command = $result[0];

        $this->assertStringContainsString("--passin='file:/run/secrets/ca.pass'", $command);
        $this->assertStringContainsString("build-server-full 'server' nopass", $command);
        $this->assertLessThan(
            strpos($command, 'build-server-full'),
            strpos($command, '--passin')
        );
    }

    public function testSignReqClientWithPassin(): void
    {
        $result = $this->commands()->signReqClient('bob', 'env:CA_PASS');

        $this->assertStringContainsString("--passin='env:CA_PASS'", $result[0]);
        $this->assertStringContainsString("sign-req client 'bob'", $result[0]);
    }

    public function testSignReqServerWithoutPassin(): void
    {
        $result = $this->commands()->signReqServer('web');

        $this->assertStringContainsString("sign-req server 'web'", $result[0]);
        $this->assertStringNotContainsString('--passin', $result[0]);
    }

    public function testEmptyPassinIsIgnored(): void
    {
        $result = $this->commands()->buildClientFull('client1', false, '');

        $this->assertStringNotContainsString('--passin', $result[0]);
    }

    public function testNameIsShellEscaped(): void
    {
        $name = 'bad;name $(id)';
        $result = $this->commands()->buildClientFull($name, false);

        $command = $result[0];

        // The whole name must be wrapped by escapeshellarg in single quotes,
        // so shell metacharacters (; and $( )) are never interpreted
        $this->assertStringContainsString('build-client-full ' . escapeshellarg($name), $command);
        $this->assertStringContainsString("'bad;name \$(id)'", $command);

        // No unquoted command separator may leak outside the quotes
        $this->assertRegExp("/build-client-full '[^']*'/", $command);
    }

    public function testPassinValueIsShellEscaped(): void
    {
        $result = $this->commands()->buildClientFull('client1', false, 'env:X; rm -rf /');

        $command = $result[0];

        $this->assertStringContainsString(escapeshellarg('env:X; rm -rf /'), $command);
        // The injected command must not appear as a bare token
        $this->assertStringNotContainsString('; rm -rf / ', $command . ' ');
    }
}
