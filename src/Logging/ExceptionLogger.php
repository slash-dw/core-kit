<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Logging;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Central error logging entry point for unexpected/infrastructure failures.
 * Expected domain/business exceptions should not use this logger (`08_Exception_Policy_Standard`).
 */
final class ExceptionLogger
{
    public function __construct(
        private readonly LogContextBuilder $contextBuilder,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function error(Throwable $e, array $context = [], ?string $authGuardForActorContext = null): void
    {
        $throwSite = ThrowSiteCapture::capture(2);
        $prefix = $throwSite['class'] !== null && $throwSite['function'] !== null
            ? sprintf('[%s::%s]', $throwSite['class'], $throwSite['function'])
            : '[throw]';

        Log::error(
            sprintf('%s %s', $prefix, $e->getMessage()),
            array_merge(
                $this->contextBuilder->build($context, $authGuardForActorContext),
                [
                    'exception' => [
                        'class' => $e::class,
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                    'throw_site' => $throwSite,
                ],
            ),
        );
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function warning(string $message, array $context = [], ?string $authGuardForActorContext = null): void
    {
        Log::warning(
            $message,
            $this->contextBuilder->build($context, $authGuardForActorContext),
        );
    }
}
