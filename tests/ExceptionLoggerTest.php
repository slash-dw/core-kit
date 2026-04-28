<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Auth\GenericUser;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use SlashDw\CoreKit\Logging\ExceptionLogger;

final class ExceptionLoggerTest extends TestCase
{
    public function test_error_dispatches_message_logged_with_exception_and_throw_site(): void
    {
        Event::fake([MessageLogged::class]);
        $this->actingAs(new GenericUser(['id' => 5]), 'web');

        $logger = $this->laravel()->make(ExceptionLogger::class);
        $logger->error(new \RuntimeException('boom'), ['note' => 'n'], 'web');

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event): bool {
            if ($event->level !== 'error') {
                return false;
            }

            if (! str_contains($event->message, 'boom')) {
                return false;
            }

            $ctx = $event->context;
            if (($ctx['actor']['id'] ?? null) !== 5) {
                return false;
            }

            if (($ctx['exception']['class'] ?? null) !== \RuntimeException::class) {
                return false;
            }

            if (! isset($ctx['throw_site']['file'], $ctx['throw_site']['line'])) {
                return false;
            }

            return ($ctx['extra']['note'] ?? null) === 'n';
        });
    }

    public function test_warning_dispatches_message_logged_with_context(): void
    {
        Event::fake([MessageLogged::class]);

        $logger = $this->laravel()->make(ExceptionLogger::class);
        $logger->warning('heads up', ['k' => 'v']);

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event): bool {
            return $event->level === 'warning'
                && $event->message === 'heads up'
                && ($event->context['extra']['k'] ?? null) === 'v';
        });
    }
}
