<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class DomSnapshotService
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/snapshots');
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Get list of available registered views for snapshot testing
     */
    public function getAvailableViews(): array
    {
        return [
            'welcome' => 'Welcome Landing Page',
            'product-form' => 'Product Creation Form Component',
            'api-status' => 'API Health & Status View',
        ];
    }

    /**
     * Render view to clean HTML string
     */
    public function renderViewContent(string $viewName): string
    {
        if ($viewName === 'product-form') {
            $html = '<div class="product-form-container"><form id="create-product-form" action="/api/products" method="POST"><input type="text" name="name" placeholder="Product Name" required /><input type="number" name="price" placeholder="Price" step="0.01" required /><button type="submit" class="btn btn-primary">Save Product</button></form></div>';
        } elseif ($viewName === 'api-status') {
            $html = '<div class="api-status-widget"><span class="badge badge-success">API Online</span><p>Version 1.0.0</p><p>Endpoints: /api/products, /api/test-runner</p></div>';
        } else {
            if (View::exists($viewName)) {
                $html = View::make($viewName)->render();
            } else {
                $html = '<div class="container"><h1>Default App View</h1><p>Automated snapshot testing initialized.</p></div>';
            }
        }

        // Normalize whitespace and newlines for clean diffs
        return $this->normalizeHtml($html);
    }

    /**
     * Get all saved baseline snapshots with status
     */
    public function getSnapshotsStatus(): array
    {
        $available = $this->getAvailableViews();
        $snapshots = [];

        foreach ($available as $viewKey => $label) {
            $filePath = $this->storagePath . '/' . $viewKey . '.html';
            $hasBaseline = File::exists($filePath);
            
            $snapshots[] = [
                'key' => $viewKey,
                'label' => $label,
                'has_baseline' => $hasBaseline,
                'last_updated' => $hasBaseline ? date('Y-m-d H:i:s', File::lastModified($filePath)) : null,
                'size_bytes' => $hasBaseline ? File::size($filePath) : 0,
            ];
        }

        return $snapshots;
    }

    /**
     * Update/save current rendered view as baseline snapshot
     */
    public function updateBaseline(string $viewName): array
    {
        $html = $this->renderViewContent($viewName);
        $filePath = $this->storagePath . '/' . $viewName . '.html';

        File::put($filePath, $html);

        return [
            'status' => 'success',
            'message' => "Baseline snapshot updated for '{$viewName}'.",
            'view' => $viewName,
            'size_bytes' => strlen($html),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Compare current rendered view with stored baseline
     */
    public function compareWithBaseline(string $viewName): array
    {
        $filePath = $this->storagePath . '/' . $viewName . '.html';
        $currentHtml = $this->renderViewContent($viewName);

        if (!File::exists($filePath)) {
            // Auto-create baseline on first check
            File::put($filePath, $currentHtml);
            return [
                'status' => 'created',
                'view' => $viewName,
                'match' => true,
                'match_percentage' => 100,
                'message' => "No existing baseline found. New baseline created automatically for '{$viewName}'.",
                'diff_lines' => [],
                'current_html' => $currentHtml,
                'baseline_html' => $currentHtml,
            ];
        }

        $baselineHtml = File::get($filePath);

        if ($currentHtml === $baselineHtml) {
            return [
                'status' => 'passed',
                'view' => $viewName,
                'match' => true,
                'match_percentage' => 100,
                'message' => 'DOM Snapshot perfectly matches baseline!',
                'diff_lines' => [],
                'current_html' => $currentHtml,
                'baseline_html' => $baselineHtml,
            ];
        }

        // Calculate line by line diff
        $baselineLines = explode("\n", $baselineHtml);
        $currentLines = explode("\n", $currentHtml);
        $diffLines = [];

        $max = max(count($baselineLines), count($currentLines));
        $matches = 0;

        for ($i = 0; $i < $max; $i++) {
            $baseLine = $baselineLines[$i] ?? '';
            $currLine = $currentLines[$i] ?? '';

            if ($baseLine === $currLine) {
                $matches++;
            } else {
                $diffLines[] = [
                    'line' => $i + 1,
                    'type' => 'mismatch',
                    'expected' => $baseLine,
                    'actual' => $currLine,
                ];
            }
        }

        $matchPercent = round(($matches / max(1, $max)) * 100, 2);

        return [
            'status' => 'failed',
            'view' => $viewName,
            'match' => false,
            'match_percentage' => $matchPercent,
            'message' => "DOM Snapshot regression detected! ({$matchPercent}% similarity)",
            'diff_lines' => $diffLines,
            'current_html' => $currentHtml,
            'baseline_html' => $baselineHtml,
        ];
    }

    /**
     * Normalize HTML string to compare consistent structures
     */
    protected function normalizeHtml(string $html): string
    {
        // Remove spaces between tags
        $html = preg_replace('/>\s+</', ">\n<", trim($html));
        return $html;
    }
}
