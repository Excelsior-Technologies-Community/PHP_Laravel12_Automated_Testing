<?php

namespace Tests\Feature;

use App\Services\DomSnapshotService;
use App\Services\LoadTestingService;
use App\Services\TestRunnerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestRunnerSuiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Test Studio Dashboard page loads successfully
     */
    public function test_can_render_test_studio_dashboard_page(): void
    {
        $response = $this->get('/test-studio');

        $response->assertStatus(200);
        $response->assertSee('Automated Test Studio & Benchmark Suite', false);
        $response->assertSee('Interactive Test Runner');
        $response->assertSee('API Load Benchmark Suite');
        $response->assertSee('DOM Snapshot Regression');
    }

    /**
     * Test API returns discovered test files
     */
    public function test_can_get_discovered_tests_list(): void
    {
        $response = $this->getJson('/api/test-runner/discovered');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'tests' => [
                'feature',
                'unit',
            ],
        ]);
    }

    /**
     * Test running test suite via API
     */
    public function test_can_run_test_suite_via_api(): void
    {
        $response = $this->postJson('/api/test-runner/run', [
            'test_file' => 'tests/Feature/ProductApiTest.php',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'results' => [
                'summary' => [
                    'total',
                    'passed_count',
                    'failed_count',
                    'duration_ms',
                    'passed_all',
                    'test_cases',
                ],
                'raw_output',
            ],
        ]);
    }

    /**
     * Test API stress and load testing service
     */
    public function test_can_run_api_load_benchmark(): void
    {
        $response = $this->postJson('/api/test-runner/load-test', [
            'endpoint' => '/api/products',
            'requests_count' => 10,
            'concurrency' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'metrics' => [
                'endpoint',
                'total_requests',
                'concurrency',
                'successful_requests',
                'failed_requests',
                'rps',
                'min_latency_ms',
                'avg_latency_ms',
                'p95_latency_ms',
                'p99_latency_ms',
                'memory_peak_mb',
                'db_query_count',
            ],
        ]);
    }

    /**
     * Test DOM snapshot regression service comparison & baseline updates
     */
    public function test_dom_snapshot_regression_service(): void
    {
        $service = new DomSnapshotService();

        // 1. First comparison auto-creates baseline
        $result = $service->compareWithBaseline('api-status');
        $this->assertTrue($result['match']);
        $this->assertEquals(100, $result['match_percentage']);

        // 2. Subsequent comparison matches baseline
        $result2 = $service->compareWithBaseline('api-status');
        $this->assertTrue($result2['match']);

        // 3. Update baseline manually
        $updateRes = $service->updateBaseline('api-status');
        $this->assertEquals('success', $updateRes['status']);
    }

    /**
     * Test exporting HTML report
     */
    public function test_can_export_html_report(): void
    {
        $response = $this->get('/api/test-runner/export-report?format=html&filter=unit');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $response->assertSee('PHPUnit Automated Test Execution Report');
    }

    /**
     * Test exporting JSON report
     */
    public function test_can_export_json_report(): void
    {
        $response = $this->get('/api/test-runner/export-report?format=json&filter=unit');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'summary',
            'raw_output',
        ]);
    }
}
