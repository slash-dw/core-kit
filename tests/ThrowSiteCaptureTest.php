<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use SlashDw\CoreKit\Logging\ThrowSiteCapture;

final class ThrowSiteCaptureTest extends TestCase
{
    public function test_capture_returns_structured_frame_for_skip_depth(): void
    {
        $site = $this->captureFromHelper();

        $this->assertIsString($site['file']);
        $this->assertNotEmpty($site['file']);
        $this->assertGreaterThan(0, $site['line']);
        $this->assertIsString($site['function']);
        $this->assertSame('captureFromHelper', $site['function']);
        $this->assertSame(self::class, $site['class']);
    }

    /**
     * @return array{file: string, line: int, function: string|null, class: string|null}
     */
    private function captureFromHelper(): array
    {
        return ThrowSiteCapture::capture(1);
    }
}
