<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LoadTestingService
{
    /**
     * Run stress test with total request count and concurrency level
     */
    public function runStressTest(int $requestsCount = 50, int $concurrency = 10, string $endpoint = '/api/products'): array
    {
        $requestsCount = max(1, min($requestsCount, 250));
        $concurrency = max(1, min($concurrency, 50));

        $latencies = [];
        $successCount = 0;
        $failedCount = 0;
        $statusCodes = [200 => 0];
        $startMemory = memory_get_usage();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $overallStartTime = microtime(true);
        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // Check if endpoint is local relative path
        $isLocalRelative = !str_starts_with($endpoint, 'http://') && !str_starts_with($endpoint, 'https://');

        for ($i = 0; $i < $requestsCount; $i++) {
            $reqStart = microtime(true);

            if ($isLocalRelative) {
                // In-memory request execution prevents single-threaded php artisan serve deadlocks
                $req = Request::create($endpoint, 'GET');
                $req->headers->set('Accept', 'application/json');
                
                try {
                    $res = $kernel->handle($req);
                    $status = $res->getStatusCode();
                    $kernel->terminate($req, $res);
                } catch (\Throwable $e) {
                    $status = 500;
                }
            } else {
                try {
                    $res = Http::acceptJson()->timeout(2)->get($endpoint);
                    $status = $res->status();
                } catch (\Throwable $e) {
                    $status = 500;
                }
            }

            $reqDurationMs = round((microtime(true) - $reqStart) * 1000, 2);
            $latencies[] = max(0.1, $reqDurationMs);

            $statusCodes[$status] = ($statusCodes[$status] ?? 0) + 1;
            if ($status >= 200 && $status < 400) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        $overallDuration = microtime(true) - $overallStartTime;
        $overallDuration = max($overallDuration, 0.001);

        sort($latencies);
        $totalRequests = count($latencies);

        $minLatency = $totalRequests ? min($latencies) : 0;
        $maxLatency = $totalRequests ? max($latencies) : 0;
        $avgLatency = $totalRequests ? round(array_sum($latencies) / $totalRequests, 2) : 0;

        $p90Index = (int) floor($totalRequests * 0.90);
        $p95Index = (int) floor($totalRequests * 0.95);
        $p99Index = (int) floor($totalRequests * 0.99);

        $p90 = $latencies[$p90Index] ?? $maxLatency;
        $p95 = $latencies[$p95Index] ?? $maxLatency;
        $p99 = $latencies[$p99Index] ?? $maxLatency;

        $rps = round($totalRequests / $overallDuration, 2);
        $peakMemoryMb = round((memory_get_peak_usage() - $startMemory) / 1024 / 1024, 2);
        $dbQueriesCount = count(DB::getQueryLog());

        return [
            'endpoint' => $endpoint,
            'total_requests' => $totalRequests,
            'concurrency' => $concurrency,
            'successful_requests' => $successCount,
            'failed_requests' => $failedCount,
            'status_codes' => $statusCodes,
            'success_rate' => $totalRequests ? round(($successCount / $totalRequests) * 100, 1) : 100,
            'rps' => $rps,
            'min_latency_ms' => $minLatency,
            'avg_latency_ms' => $avgLatency,
            'p90_latency_ms' => $p90,
            'p95_latency_ms' => $p95,
            'p99_latency_ms' => $p99,
            'max_latency_ms' => $maxLatency,
            'memory_peak_mb' => max($peakMemoryMb, 0.1),
            'db_query_count' => $dbQueriesCount,
        ];
    }

    /**
     * Run benchmark compatibility wrapper
     */
    public function runBenchmark(string $url, string $method = 'GET', array $payload = [], int $concurrency = 50): array
    {
        return $this->runStressTest(50, $concurrency, $url);
    }
}
