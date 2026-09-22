<?php

namespace App\Http\Controllers;

use App\Services\TestRunnerService;
use App\Services\LoadTestingService;
use App\Services\DomSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TestRunnerController extends Controller
{
    protected TestRunnerService $testRunnerService;
    protected LoadTestingService $loadTestingService;
    protected DomSnapshotService $domSnapshotService;

    public function __construct(
        TestRunnerService $testRunnerService,
        LoadTestingService $loadTestingService,
        DomSnapshotService $domSnapshotService
    ) {
        $this->testRunnerService = $testRunnerService;
        $this->loadTestingService = $loadTestingService;
        $this->domSnapshotService = $domSnapshotService;
    }

    /**
     * Show the main Interactive Live Test Studio Dashboard
     */
    public function index()
    {
        $discoveredTests = $this->testRunnerService->discoverTests();
        $snapshots = $this->domSnapshotService->getSnapshotsStatus();
        return view('test-studio', compact('discoveredTests', 'snapshots'));
    }

    /**
     * Endpoint to list discovered test files
     */
    public function discoveredTests(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'tests' => $this->testRunnerService->discoverTests(),
        ]);
    }

    /**
     * Endpoint to execute PHPUnit test suite or specific test file
     */
    public function runTests(Request $request): JsonResponse
    {
        $filter = $request->input('filter', 'all');
        $testFile = $request->input('test_file');

        $results = $this->testRunnerService->runTests($filter, $testFile);

        return response()->json([
            'status' => 'success',
            'results' => $results,
        ]);
    }

    /**
     * Endpoint to run API stress and load testing benchmark
     */
    public function runLoadTest(Request $request): JsonResponse
    {
        $requestsCount = (int) $request->input('requests_count', 50);
        $concurrency = (int) $request->input('concurrency', 10);
        $endpoint = $request->input('endpoint', '/api/products');

        $metrics = $this->loadTestingService->runStressTest($requestsCount, $concurrency, $endpoint);

        return response()->json([
            'status' => 'success',
            'metrics' => $metrics,
        ]);
    }

    /**
     * Endpoint to get DOM snapshot statuses
     */
    public function snapshotStatus(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'snapshots' => $this->domSnapshotService->getSnapshotsStatus(),
        ]);
    }

    /**
     * Endpoint to compare view DOM snapshot against baseline
     */
    public function compareSnapshot(Request $request): JsonResponse
    {
        $viewName = $request->input('view_name', 'welcome');
        $comparison = $this->domSnapshotService->compareWithBaseline($viewName);

        return response()->json($comparison);
    }

    /**
     * Endpoint to update view baseline DOM snapshot
     */
    public function updateSnapshot(Request $request): JsonResponse
    {
        $viewName = $request->input('view_name', 'welcome');
        $res = $this->domSnapshotService->updateBaseline($viewName);

        return response()->json($res);
    }

    /**
     * Export execution report in HTML or JSON
     */
    public function exportReport(Request $request)
    {
        $format = $request->input('format', 'html');
        $filter = $request->input('filter', 'all');

        $testResults = $this->testRunnerService->runTests($filter);

        if ($format === 'json') {
            return response()->json($testResults)
                ->header('Content-Disposition', 'attachment; filename="test-report-' . date('Ymd-His') . '.json"');
        }

        $htmlContent = $this->testRunnerService->generateHtmlReport($testResults);

        return response($htmlContent, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="test-report-' . date('Ymd-His') . '.html"');
    }
}
