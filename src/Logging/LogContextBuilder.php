<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Logging;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Builds log context. The guard is provided explicitly; the auth helper is not used (`09_Authorization_Boundary_Standard`).
 */
final class LogContextBuilder
{
    public function __construct(
        private readonly Application $app,
    ) {}

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function build(array $extra = [], ?string $authGuard = null): array
    {
        $context = [];

        if ($authGuard !== null && $authGuard !== '') {
            $actor = Auth::guard($authGuard)->user();
            if ($actor instanceof Authenticatable) {
                $context['actor'] = [
                    'guard' => $authGuard,
                    'id' => $actor->getAuthIdentifier(),
                ];
            } else {
                $context['actor'] = [
                    'guard' => $authGuard,
                    'id' => null,
                ];
            }
        }

        $requestContext = $this->requestSlice();
        if ($requestContext !== []) {
            $context['request'] = $requestContext;
        }

        if ($extra !== []) {
            $context['extra'] = $extra;
        }

        return $context;
    }

    /**
     * @return array<string, mixed>
     */
    private function requestSlice(): array
    {
        if (! $this->app->bound('request')) {
            return [];
        }

        /** @var mixed $rawRequest */
        $rawRequest = $this->app->make('request');
        if (! $rawRequest instanceof Request) {
            return [];
        }

        $request = $rawRequest;

        $uri = $request->getUri();
        if ($uri === '') {
            return [];
        }

        $out = [
            'method' => $request->getMethod(),
            'url' => $uri,
            'ip' => $request->ip(),
        ];

        $traceId = $request->headers->get('X-Trace-Id')
            ?? $request->headers->get('X-Request-Id');
        if (is_string($traceId) && $traceId !== '') {
            $out['trace_id'] = $traceId;
        }

        return $out;
    }
}
