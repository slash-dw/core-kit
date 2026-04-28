<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Logging;

/**
 * Captures the throw-site frame; uses skip frames to bypass internal factory/helper frames.
 */
final class ThrowSiteCapture
{
    /**
     * @return array{file: string, line: int, function: string|null, class: string|null}
     */
    public static function capture(int $skipFrames = 2): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $skipFrames + 1);
        if ($trace === []) {
            return [
                'file' => 'unknown',
                'line' => 0,
                'function' => null,
                'class' => null,
            ];
        }

        $lastIndex = array_key_last($trace);
        /** @var mixed $frame */
        $frame = $trace[$skipFrames] ?? ($lastIndex !== null ? $trace[$lastIndex] : null);
        if (! is_array($frame)) {
            return [
                'file' => 'unknown',
                'line' => 0,
                'function' => null,
                'class' => null,
            ];
        }

        $file = $frame['file'] ?? null;
        $line = $frame['line'] ?? null;
        $function = array_key_exists('function', $frame) ? $frame['function'] : null;
        $class = array_key_exists('class', $frame) ? $frame['class'] : null;

        $lineOut = 0;
        if (is_int($line)) {
            $lineOut = $line;
        } elseif (is_numeric($line)) {
            $lineOut = (int) $line;
        }

        return [
            'file' => is_string($file) ? $file : 'unknown',
            'line' => $lineOut,
            'function' => is_string($function) ? $function : null,
            'class' => is_string($class) ? $class : null,
        ];
    }
}
