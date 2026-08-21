<?php

namespace Javaabu\DhivehiGpt\Tests\TestSupport;

/**
 * Starts and stops PHP's built-in development server, backed by
 * tests/TestSupport/server/router.php, so integration tests can exercise
 * real network I/O without depending on an external service.
 */
trait InteractsWithLocalServer
{
    /** @var resource|false */
    protected $server_process;

    protected int $port;

    protected function startLocalServer(): void
    {
        $this->port = random_int(20000, 60000);

        $docroot = __DIR__.'/server';

        $descriptor_spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $this->server_process = proc_open(
            [PHP_BINARY, '-S', "127.0.0.1:{$this->port}", '-t', $docroot, $docroot.'/router.php'],
            $descriptor_spec,
            $pipes
        );

        if ($this->server_process === false) {
            $this->markTestSkipped('Could not start the PHP built-in server.');
        }

        $this->waitForLocalServer();
    }

    protected function stopLocalServer(): void
    {
        if (is_resource($this->server_process)) {
            proc_terminate($this->server_process);
            proc_close($this->server_process);
        }
    }

    protected function waitForLocalServer(): void
    {
        $deadline = microtime(true) + 5;

        while (microtime(true) < $deadline) {
            $connection = @fsockopen('127.0.0.1', $this->port, $error_code, $error_message, 0.2);

            if ($connection !== false) {
                fclose($connection);

                return;
            }

            usleep(50000);
        }

        $this->markTestSkipped('The PHP built-in server did not start in time.');
    }
}
