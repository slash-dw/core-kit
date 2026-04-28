<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Tracing;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

final class TraceIdResolver
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function resolve(): string
    {
        if ($this->app->bound('requestId')) {
            $requestId = (string) $this->app->make('requestId');
            if ($requestId !== '') {
                return $requestId;
            }
        }

        if (! $this->app->bound('request')) {
            return '';
        }

        /** @var mixed $rawRequest */
        $rawRequest = $this->app->make('request');
        if (! $rawRequest instanceof Request) {
            return '';
        }

        $request = $rawRequest;

        $headers = $this->config->get('core_kit.tracing.headers', ['X-Trace-Id']);
        if (is_array($headers)) {
            foreach ($headers as $headerName) {
                if (! is_string($headerName) || $headerName === '') {
                    continue;
                }

                $value = (string) $request->header($headerName, '');
                if ($value !== '') {
                    return $value;
                }
            }
        }

        $fallbackHeader = (string) $this->config->get('core_kit.tracing.fallback_header', 'X-Trace-Id');

        return (string) $request->header($fallbackHeader, '');
    }
}
