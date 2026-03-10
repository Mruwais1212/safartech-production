<?php

namespace Tests\Feature;

use App\Support\HtmlSanitizer;
use Tests\TestCase;

class SecurityHeadersAndSanitizerTest extends TestCase
{
    public function test_web_responses_include_correlation_and_csp_report_only_headers(): void
    {
        $response = $this->withHeaders([
            'X-Correlation-Id' => 'corr-test-1234',
        ])->get('/health/status');

        $response->assertOk();
        $response->assertHeader('X-Correlation-Id', 'corr-test-1234');
        $response->assertHeader('Content-Security-Policy-Report-Only');
    }

    public function test_html_sanitizer_strips_scripts_attributes_and_disallowed_tags(): void
    {
        $input = '<p onclick="evil()">ok</p><script>alert(1)</script><a href="javascript:alert(1)">x</a><b style="color:red">safe</b>';
        $output = HtmlSanitizer::sanitizeLimitedHtml($input);

        $this->assertStringNotContainsString('script', $output);
        $this->assertStringNotContainsString('onclick', $output);
        $this->assertStringNotContainsString('<a', $output);
        $this->assertSame('<p>ok</p>x<b>safe</b>', $output);
    }
}
