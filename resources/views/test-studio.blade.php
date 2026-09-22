<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Test Studio & Live Security Benchmark Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] { display: none !important; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans" x-data="testStudio()">
    
    <!-- Header Navbar -->
    <header class="bg-slate-800/80 backdrop-blur border-b border-slate-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-xl shadow-lg shadow-indigo-500/30">
                    🧪
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-300 bg-clip-text text-transparent">
                        Automated Test Studio & Benchmark Suite
                    </h1>
                    <p class="text-xs text-slate-400">Laravel 12 High-Performance Testing Engine</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/products" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs font-semibold text-slate-200 transition">
                    📦 App Products
                </a>
                <a href="/product/create" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-xs font-semibold text-slate-200 transition">
                    ➕ Add Product
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-800 space-x-4 mb-8">
            <button 
                @click="activeTab = 'runner'" 
                :class="activeTab === 'runner' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center space-x-2 px-4 py-3 border-b-2 font-medium text-sm rounded-t-lg transition">
                <span>🎛️ Interactive Test Runner</span>
            </button>
            <button 
                @click="activeTab = 'load'" 
                :class="activeTab === 'load' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center space-x-2 px-4 py-3 border-b-2 font-medium text-sm rounded-t-lg transition">
                <span>⚡ API Load Benchmark Suite</span>
            </button>
            <button 
                @click="activeTab = 'dom'" 
                :class="activeTab === 'dom' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center space-x-2 px-4 py-3 border-b-2 font-medium text-sm rounded-t-lg transition">
                <span>👁️ DOM Snapshot Regression</span>
            </button>
        </div>

        <!-- ================= TAB 1: TEST RUNNER ================= -->
        <div x-show="activeTab === 'runner'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Control Panel Card -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-1 shadow-xl">
                    <h2 class="text-lg font-bold mb-4 text-slate-100 flex items-center gap-2">
                        <span>⚙️ Test Execution Controls</span>
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Filter Suite</label>
                            <select x-model="runnerFilter" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="all">All Test Suites (Feature & Unit)</option>
                                <option value="feature">Feature Tests Only</option>
                                <option value="unit">Unit Tests Only</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Specific Test File</label>
                            <select x-model="selectedTestFile" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="">Run All Files in Suite</option>
                                @foreach($discoveredTests['feature'] ?? [] as $fTest)
                                    <option value="{{ $fTest['relative_path'] }}">Feature: {{ $fTest['name'] }}</option>
                                @endforeach
                                @foreach($discoveredTests['unit'] ?? [] as $uTest)
                                    <option value="{{ $uTest['relative_path'] }}">Unit: {{ $uTest['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button 
                            @click="executeTests()" 
                            :disabled="isExecutingTests"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 font-semibold text-white shadow-lg shadow-indigo-500/25 flex items-center justify-center space-x-2 disabled:opacity-50 transition">
                            <span x-show="!isExecutingTests">▶ Run Tests Now</span>
                            <span x-show="isExecutingTests" class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Running Test Suite...</span>
                            </span>
                        </button>
                    </div>

                    <!-- Export Report Section -->
                    <div class="mt-8 pt-6 border-t border-slate-700/60">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Export Test Reports</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a :href="'/api/test-runner/export-report?format=html&filter=' + runnerFilter" target="_blank"
                               class="text-center py-2 px-3 rounded-lg bg-slate-700/70 hover:bg-slate-700 border border-slate-600 text-xs font-medium text-indigo-300 transition">
                                📄 Export HTML
                            </a>
                            <a :href="'/api/test-runner/export-report?format=json&filter=' + runnerFilter" download
                               class="text-center py-2 px-3 rounded-lg bg-slate-700/70 hover:bg-slate-700 border border-slate-600 text-xs font-medium text-emerald-300 transition">
                                💾 Export JSON
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Live Test Results Window -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-2 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                                <span>📊 Suite Execution Summary</span>
                            </h2>
                            <span class="text-xs px-2.5 py-1 rounded-full font-mono bg-slate-900 border border-slate-700 text-slate-400">
                                Total: <strong class="text-slate-200" x-text="testSummary.total">0</strong> tests
                            </span>
                        </div>

                        <!-- Summary Stat Badges -->
                        <div class="grid grid-cols-4 gap-4 mb-6">
                            <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-3 text-center">
                                <span class="text-xs text-slate-400 uppercase">Status</span>
                                <div class="text-base font-bold mt-1" :class="testSummary.passed_all ? 'text-emerald-400' : (testSummary.total > 0 ? 'text-rose-400' : 'text-slate-400')">
                                    <span x-text="testSummary.total === 0 ? 'IDLE' : (testSummary.passed_all ? '🟢 PASSED' : '🔴 FAILED')"></span>
                                </div>
                            </div>
                            <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-3 text-center">
                                <span class="text-xs text-slate-400 uppercase">Passed</span>
                                <div class="text-xl font-bold text-emerald-400 mt-1" x-text="testSummary.passed_count">0</div>
                            </div>
                            <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-3 text-center">
                                <span class="text-xs text-slate-400 uppercase">Failed</span>
                                <div class="text-xl font-bold text-rose-400 mt-1" x-text="testSummary.failed_count">0</div>
                            </div>
                            <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-3 text-center">
                                <span class="text-xs text-slate-400 uppercase">Time</span>
                                <div class="text-xl font-bold text-indigo-400 mt-1"><span x-text="testSummary.duration_ms">0</span> <span class="text-xs text-slate-400 font-normal">ms</span></div>
                            </div>
                        </div>

                        <!-- Test Items List -->
                        <div class="mb-4 max-h-64 overflow-y-auto space-y-2 pr-1">
                            <template x-for="(test, idx) in testSummary.test_cases" :key="idx">
                                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span x-text="test.status === 'passed' ? '🟢' : '🔴'" class="text-sm"></span>
                                        <div>
                                            <div class="text-xs font-bold text-slate-200" x-text="test.name"></div>
                                            <div class="text-[10px] text-slate-400 font-mono" x-text="test.class"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono text-slate-400" x-text="test.duration + ' ms'"></span>
                                </div>
                            </template>
                            <div x-show="testSummary.test_cases.length === 0" class="text-center py-8 text-slate-500 text-sm">
                                Click "▶ Run Tests Now" to execute PHPUnit test suites.
                            </div>
                        </div>
                    </div>

                    <!-- Execution Output Terminal Window -->
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Console Output</span>
                            <span class="text-[10px] text-slate-500 font-mono">PHPUnit CLI Raw Log</span>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 font-mono text-xs text-emerald-400 overflow-x-auto max-h-48 whitespace-pre-wrap leading-relaxed select-all">
                            <span x-text="testOutput || '// Console output will appear here...'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: API LOAD TESTING ================= -->
        <div x-show="activeTab === 'load'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Load Testing Controls -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-1 shadow-xl">
                    <h2 class="text-lg font-bold mb-4 text-slate-100 flex items-center gap-2">
                        <span>⚡ Load Generator Controls</span>
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Endpoint</label>
                            <select x-model="loadEndpoint" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                <option value="/api/products">GET /api/products (Product Listing)</option>
                                <option value="/api/products/statistics">GET /api/products/statistics (DB Aggregations)</option>
                                <option value="/products">GET /products (Blade Server Rendered)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Requests: <strong class="text-indigo-400" x-text="loadRequests"></strong></label>
                            <input type="range" min="10" max="250" step="10" x-model="loadRequests" class="w-full accent-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Concurrency Batch Level: <strong class="text-indigo-400" x-text="loadConcurrency"></strong></label>
                            <input type="range" min="2" max="25" step="1" x-model="loadConcurrency" class="w-full accent-indigo-500">
                        </div>

                        <button 
                            @click="executeLoadTest()" 
                            :disabled="isExecutingLoad"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 font-semibold text-white shadow-lg shadow-emerald-500/25 flex items-center justify-center space-x-2 disabled:opacity-50 transition">
                            <span x-show="!isExecutingLoad">🚀 Launch Stress Test</span>
                            <span x-show="isExecutingLoad" class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Simulating Traffic Pool...</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Load Metrics Dashboard -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-2 shadow-xl">
                    <h2 class="text-lg font-bold mb-4 text-slate-100 flex items-center justify-between">
                        <span>📈 Performance & Latency Telemetry</span>
                        <span class="text-xs text-slate-400 font-mono" x-text="loadMetrics.endpoint || 'No test executed yet'"></span>
                    </h2>

                    <!-- Primary Metric Cards Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-4">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Throughput</span>
                            <div class="text-2xl font-extrabold text-emerald-400 mt-1" x-text="loadMetrics.rps || 0">0</div>
                            <span class="text-[10px] text-slate-500">Req / Second (RPS)</span>
                        </div>

                        <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-4">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Average Latency</span>
                            <div class="text-2xl font-extrabold text-indigo-400 mt-1"><span x-text="loadMetrics.avg_latency_ms || 0">0</span> <span class="text-xs font-normal text-slate-400">ms</span></div>
                            <span class="text-[10px] text-slate-500">Min: <span x-text="loadMetrics.min_latency_ms || 0"></span>ms</span>
                        </div>

                        <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-4">
                            <span class="text-xs text-slate-400 uppercase font-semibold">P95 Latency</span>
                            <div class="text-2xl font-extrabold text-purple-400 mt-1"><span x-text="loadMetrics.p95_latency_ms || 0">0</span> <span class="text-xs font-normal text-slate-400">ms</span></div>
                            <span class="text-[10px] text-slate-500">P99: <span x-text="loadMetrics.p99_latency_ms || 0"></span>ms</span>
                        </div>

                        <div class="bg-slate-900/80 border border-slate-700/60 rounded-xl p-4">
                            <span class="text-xs text-slate-400 uppercase font-semibold">Peak Memory</span>
                            <div class="text-2xl font-extrabold text-amber-400 mt-1"><span x-text="loadMetrics.memory_peak_mb || 0">0</span> <span class="text-xs font-normal text-slate-400">MB</span></div>
                            <span class="text-[10px] text-slate-500">PHP Memory Usage</span>
                        </div>
                    </div>

                    <!-- HTTP Response Codes & Query Profile -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-900/60 border border-slate-700/60 rounded-xl p-4">
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">HTTP Status Breakdown</h3>
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-800 text-xs">
                                <span class="text-emerald-400 font-bold">200 OK (Success)</span>
                                <span class="font-mono text-slate-200" x-text="loadMetrics.status_codes ? (loadMetrics.status_codes[200] || 0) : 0">0</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 text-xs">
                                <span class="text-rose-400 font-bold">Errors / Other</span>
                                <span class="font-mono text-slate-200" x-text="loadMetrics.failed_requests || 0">0</span>
                            </div>
                        </div>

                        <div class="bg-slate-900/60 border border-slate-700/60 rounded-xl p-4">
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Database Profiler</h3>
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-800 text-xs">
                                <span class="text-slate-300">Total Queries Run</span>
                                <span class="font-mono text-indigo-300 font-bold" x-text="loadMetrics.db_query_count || 0">0</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 text-xs">
                                <span class="text-slate-300">Success Rate</span>
                                <span class="font-mono text-emerald-400 font-bold"><span x-text="loadMetrics.success_rate || 100"></span>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 3: DOM SNAPSHOT TESTING ================= -->
        <div x-show="activeTab === 'dom'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- View Selector Controls -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-1 shadow-xl">
                    <h2 class="text-lg font-bold mb-4 text-slate-100 flex items-center gap-2">
                        <span>👁️ UI Snapshot Controls</span>
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Target Component View</label>
                            <select x-model="selectedDomView" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-indigo-500">
                                @foreach($snapshots as $snap)
                                    <option value="{{ $snap['key'] }}">{{ $snap['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button 
                                @click="compareDomSnapshot()" 
                                :disabled="isCheckingDom"
                                class="flex-1 py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 font-semibold text-xs text-white shadow-lg flex items-center justify-center space-x-1 transition">
                                <span>🔍 Compare Baseline</span>
                            </button>
                            <button 
                                @click="updateDomBaseline()" 
                                :disabled="isCheckingDom"
                                class="py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 font-semibold text-xs text-white shadow-lg flex items-center justify-center space-x-1 transition">
                                <span>📸 Save Baseline</span>
                            </button>
                        </div>
                    </div>

                    <!-- Baseline List -->
                    <div class="mt-6 pt-6 border-t border-slate-700/60">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Saved Baseline Snapshots</h3>
                        <div class="space-y-2">
                            @foreach($snapshots as $snap)
                                <div class="bg-slate-900/60 border border-slate-700/40 rounded-lg p-2.5 flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-300">{{ $snap['label'] }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $snap['has_baseline'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                        {{ $snap['has_baseline'] ? 'Baseline Saved' : 'No Baseline' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- DOM Diff Results Display -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-6 lg:col-span-2 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                            <span>🔍 DOM Structure Match Analysis</span>
                        </h2>
                        <span x-show="domResult.status" class="px-3 py-1 rounded-full text-xs font-bold" 
                              :class="domResult.match ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30'"
                              x-text="domResult.match ? '🟢 100% MATCH' : '🔴 REGRESSION DETECTED'">
                        </span>
                    </div>

                    <!-- Message Banner -->
                    <div x-show="domResult.message" class="mb-4 p-3 rounded-xl border text-xs font-medium"
                         :class="domResult.match ? 'bg-emerald-950/40 border-emerald-800 text-emerald-300' : 'bg-rose-950/40 border-rose-800 text-rose-300'">
                        <span x-text="domResult.message"></span>
                    </div>

                    <!-- HTML Diff Terminal -->
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>DOM Code Diff View</span>
                            <span class="text-[10px] text-slate-500 font-mono" x-text="domResult.view ? 'Component: ' + domResult.view : ''"></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 font-mono text-xs max-h-80 overflow-y-auto space-y-1">
                            <template x-for="diff in domResult.diff_lines" :key="diff.line">
                                <div class="bg-rose-950/50 text-rose-300 p-1.5 rounded border border-rose-900/50">
                                    <div class="text-[10px] text-rose-400 font-bold mb-0.5">Line <span x-text="diff.line"></span> Mismatch:</div>
                                    <div class="text-slate-400">- Baseline: <span x-text="diff.expected" class="text-slate-300"></span></div>
                                    <div class="text-rose-400">+ Rendered: <span x-text="diff.actual" class="text-rose-200"></span></div>
                                </div>
                            </template>
                            <div x-show="domResult.match === true" class="text-emerald-400 py-4 text-center">
                                ✨ DOM snapshot structure perfectly matches baseline HTML! No visual regression found.
                            </div>
                            <div x-show="!domResult.status" class="text-slate-500 py-8 text-center">
                                Click "🔍 Compare Baseline" to verify rendered Blade HTML view against stored snapshot.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Alpine.js Frontend Controller logic -->
    <script>
        function testStudio() {
            return {
                activeTab: 'runner',
                
                // Runner state
                runnerFilter: 'all',
                selectedTestFile: '',
                isExecutingTests: false,
                testOutput: '',
                testSummary: {
                    total: 0,
                    passed_count: 0,
                    failed_count: 0,
                    duration_ms: 0,
                    passed_all: false,
                    test_cases: []
                },

                // Load test state
                loadEndpoint: '/api/products',
                loadRequests: 50,
                loadConcurrency: 10,
                isExecutingLoad: false,
                loadMetrics: {},

                // DOM state
                selectedDomView: 'welcome',
                isCheckingDom: false,
                domResult: {},

                async executeTests() {
                    this.isExecutingTests = true;
                    this.testOutput = "Executing PHPUnit suite, please wait...";
                    
                    try {
                        const response = await fetch('/api/test-runner/run', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                filter: this.runnerFilter,
                                test_file: this.selectedTestFile
                            })
                        });
                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            this.testSummary = data.results.summary;
                            this.testOutput = data.results.raw_output;
                        }
                    } catch (error) {
                        this.testOutput = "Error executing tests: " + error.message;
                    } finally {
                        this.isExecutingTests = false;
                    }
                },

                async executeLoadTest() {
                    this.isExecutingLoad = true;
                    
                    try {
                        const response = await fetch('/api/test-runner/load-test', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                endpoint: this.loadEndpoint,
                                requests_count: this.loadRequests,
                                concurrency: this.loadConcurrency
                            })
                        });
                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            this.loadMetrics = data.metrics;
                        }
                    } catch (error) {
                        alert("Load test failed: " + error.message);
                    } finally {
                        this.isExecutingLoad = false;
                    }
                },

                async compareDomSnapshot() {
                    this.isCheckingDom = true;
                    try {
                        const response = await fetch('/api/test-runner/snapshots/compare', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                view_name: this.selectedDomView
                            })
                        });
                        this.domResult = await response.json();
                    } catch (error) {
                        alert("DOM check failed: " + error.message);
                    } finally {
                        this.isCheckingDom = false;
                    }
                },

                async updateDomBaseline() {
                    this.isCheckingDom = true;
                    try {
                        const response = await fetch('/api/test-runner/snapshots/update', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                view_name: this.selectedDomView
                            })
                        });
                        const res = await response.json();
                        alert(res.message);
                        this.compareDomSnapshot();
                    } catch (error) {
                        alert("Failed to update baseline: " + error.message);
                    } finally {
                        this.isCheckingDom = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
