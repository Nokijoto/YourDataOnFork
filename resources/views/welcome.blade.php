<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FORKED_DATA // SYSTEM CONTROL PANEL</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Share Tech Mono', monospace;
            background:
                linear-gradient(rgba(16, 185, 129, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 211, 238, 0.025) 1px, transparent 1px),
                #030706;
            background-size: 44px 44px, 44px 44px, auto;
            color: #4af626;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, 0.78), rgba(0, 0, 0, 0.24) 48%, rgba(0, 0, 0, 0.68)),
                linear-gradient(180deg, rgba(3, 7, 6, 0.1), rgba(3, 7, 6, 0.72));
        }

        body > header,
        body > main,
        body > footer {
            position: relative;
            z-index: 3;
        }
        
        .glow-green {
            text-shadow: 0 0 10px rgba(74, 246, 38, 0.5);
        }
        .glow-cyan {
            text-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
        }
        .glow-border-green {
            box-shadow: 0 0 15px rgba(74, 246, 38, 0.15);
            border-color: rgba(74, 246, 38, 0.3);
        }
        .glow-border-cyan {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.15);
            border-color: rgba(6, 182, 212, 0.3);
        }
        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            opacity: 0.16;
            pointer-events: none;
        }
        .scanline {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(
                to bottom,
                rgba(255,255,255,0),
                rgba(255,255,255,0) 50%,
                rgba(0, 0, 0, 0.22) 50%,
                rgba(0, 0, 0, 0.22)
            );
            background-size: 100% 4px;
            z-index: 2;
        }
        .terminal-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .terminal-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        .terminal-scroll::-webkit-scrollbar-thumb {
            background: rgba(74, 246, 38, 0.3);
            border-radius: 3px;
        }
        .terminal-record {
            border-left: 2px solid rgba(74, 246, 38, 0.34);
            background: rgba(2, 6, 5, 0.38);
            padding: 0.45rem 0.65rem;
            margin: 0.25rem 0;
            overflow-wrap: anywhere;
        }
        .terminal-record--capture {
            border-left-color: rgba(248, 113, 113, 0.62);
            background: rgba(69, 10, 10, 0.1);
        }
        .terminal-record--packet {
            border-left-color: rgba(34, 211, 238, 0.62);
            background: rgba(8, 47, 73, 0.12);
        }
        .terminal-record-title {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px dashed rgba(74, 246, 38, 0.16);
        }
        .terminal-kv-grid {
            display: grid;
            grid-template-columns: minmax(6.25rem, 0.22fr) minmax(0, 1fr);
            gap: 0.1rem 0.75rem;
            margin-top: 0.35rem;
        }
        .terminal-kv-label {
            color: rgba(156, 163, 175, 0.9);
            text-transform: uppercase;
            font-size: 0.68rem;
        }
        .terminal-kv-value {
            color: rgba(220, 252, 231, 0.96);
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
        .terminal-section {
            margin-top: 0.45rem;
            color: rgba(250, 204, 21, 0.9);
            font-size: 0.72rem;
            text-transform: uppercase;
        }
        .terminal-tree {
            margin-top: 0.2rem;
            color: rgba(220, 252, 231, 0.94);
        }
        .terminal-tree-row {
            display: grid;
            grid-template-columns: minmax(6.25rem, 0.22fr) minmax(0, 1fr);
            gap: 0.1rem 0.75rem;
        }
        .terminal-tree-key {
            color: rgba(125, 211, 252, 0.95);
        }
        .terminal-tree-value {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
        .terminal-muted {
            color: rgba(107, 114, 128, 0.95);
        }
        @media (max-width: 640px) {
            .terminal-kv-grid,
            .terminal-tree-row {
                grid-template-columns: 1fr;
                gap: 0.1rem;
            }
            .terminal-kv-value,
            .terminal-tree-value {
                margin-bottom: 0.35rem;
            }
        }
    </style>
</head>
<body class="relative min-h-screen flex flex-col justify-between p-4 md:p-8">
    
    <!-- Matrix rain canvas -->
    <canvas id="matrix" class="matrix-bg"></canvas>
    <div class="scanline"></div>
    
    <!-- Header -->
    <header style="display: none;" class="w-full max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4 p-4 mb-6 border border-emerald-900/40 bg-zinc-950/70 backdrop-blur-md rounded-lg glow-border-green">
        <div>
            <div class="flex items-center gap-3">
                <span class="relative flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                </span>
                <h1 class="text-2xl md:text-3xl font-bold tracking-widest text-green-400 glow-green">
                    FORKED_DATA // CORE
                </h1>
            </div>
            <p class="text-xs text-green-500/60 mt-1 uppercase tracking-wider">
                System status: Operational // Node: {{ php_uname('n') }}
            </p>
        </div>
        
        <div class="flex items-center gap-6 text-sm text-green-400/80">
            <div>
                <span class="text-green-600">IP:</span> 127.0.0.1
            </div>
            <div>
                <span class="text-green-600">TIME:</span> <span id="live-time">00:00:00 UTC</span>
            </div>
        </div>
    </header>

    <!-- Main Layout -->
    <main class="w-full max-w-7xl mx-auto flex flex-col gap-6 flex-grow mb-6">
        
        <!-- Quick Actions Panel (Hidden) -->
        <div style="display: none;" class="p-6 border border-cyan-900/40 bg-zinc-950/70 backdrop-blur-md rounded-lg glow-border-cyan flex flex-col justify-between h-[300px]">
            <div>
                <h2 class="text-lg font-semibold tracking-wider text-cyan-400 glow-cyan mb-2 uppercase">
                    // SECURE ACCESS PORTALS
                </h2>
                <p class="text-xs text-cyan-500/60 mb-6">
                    Authorized personnel only. Logs are being registered.
                </p>
                
                <div class="flex flex-col gap-4">
                    <a href="/admin" class="group flex items-center justify-between p-3 border border-cyan-500/20 hover:border-cyan-400 bg-cyan-950/10 hover:bg-cyan-950/30 rounded-md transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <span class="text-cyan-500">&gt;_</span>
                            <span class="text-cyan-300 group-hover:text-cyan-100 transition-colors uppercase tracking-wider text-sm font-semibold">
                                FILAMENT ADMIN PANEL
                            </span>
                        </div>
                        <span class="text-cyan-500 group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>

                    <a href="http://localhost:8025" target="_blank" class="group flex items-center justify-between p-3 border border-emerald-500/20 hover:border-emerald-400 bg-emerald-950/10 hover:bg-emerald-950/30 rounded-md transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <span class="text-emerald-500">✉_</span>
                            <span class="text-emerald-300 group-hover:text-emerald-100 transition-colors uppercase tracking-wider text-sm font-semibold">
                                MAILPIT INTERCEPTOR
                            </span>
                        </div>
                        <span class="text-emerald-500 group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
            
            <div class="text-xs text-cyan-500/40 mt-4 border-t border-cyan-950/40 pt-4 flex justify-between">
                <span>SECURITY LEV: 0</span>
                <span>CONTEXT: SAIL</span>
            </div>
        </div>
        
        <!-- Live Capture Counter (At the top) -->
        <div class="p-4 border border-red-900/40 bg-zinc-950/70 backdrop-blur-md rounded-lg flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold tracking-wider text-red-400 uppercase">// LIVE CAPTURE</h2>
                <span id="monitor-badge" class="text-xs px-2 py-0.5 rounded border border-gray-700 text-gray-500">OFF</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-black/40 rounded p-2 text-center">
                    <div class="text-xl font-bold text-red-400" id="stat-captures">0</div>
                    <div class="text-xs text-gray-500 uppercase">Requests</div>
                </div>
                <div class="bg-black/40 rounded p-2 text-center">
                    <div class="text-xl font-bold text-cyan-400" id="stat-packets">0</div>
                    <div class="text-xs text-gray-500 uppercase">Packets</div>
                </div>
            </div>
            <div class="text-xs text-gray-600">Wpisz <span class="text-red-400 font-bold">monitor</span> aby włączyć live feed</div>
        </div>

        <!-- Network / Services Status (Hidden) -->
        <div style="display: none;" class="p-6 border border-emerald-900/40 bg-zinc-950/70 backdrop-blur-md rounded-lg glow-border-green flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-semibold tracking-wider text-green-400 glow-green mb-4 uppercase">
                    // ACTIVE TELEMETRY
                </h2>
                
                <div class="flex flex-col gap-4">
                    <!-- Service Item -->
                    <div class="flex items-center justify-between p-2 border-b border-emerald-900/20">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-sm font-semibold uppercase tracking-wider text-green-300">Database (MySQL)</span>
                        </div>
                        <span class="text-xs text-green-500 bg-green-950/40 px-2 py-0.5 border border-green-500/20 rounded">ONLINE</span>
                    </div>
                    
                    <!-- Service Item -->
                    <div class="flex items-center justify-between p-2 border-b border-emerald-900/20">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-sm font-semibold uppercase tracking-wider text-green-300">Cache (Redis)</span>
                        </div>
                        <span class="text-xs text-green-500 bg-green-950/40 px-2 py-0.5 border border-green-500/20 rounded">ONLINE</span>
                    </div>
                    
                    <!-- Service Item -->
                    <div class="flex items-center justify-between p-2 border-b border-emerald-900/20">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-sm font-semibold uppercase tracking-wider text-green-300">Mail Catcher</span>
                        </div>
                        <span class="text-xs text-green-500 bg-green-950/40 px-2 py-0.5 border border-green-500/20 rounded">ONLINE</span>
                    </div>

                    <!-- Service Item -->
                    <div class="flex items-center justify-between p-2 border-b border-emerald-900/20">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                            <span class="text-sm font-semibold uppercase tracking-wider text-blue-300">HTTP Server (Nginx)</span>
                        </div>
                        <span class="text-xs text-blue-500 bg-blue-950/40 px-2 py-0.5 border border-blue-500/20 rounded">PORT 80</span>
                    </div>
                </div>

                <!-- Dynamic SVG chart -->
                <div class="mt-6">
                    <p class="text-xs text-green-500/60 uppercase mb-2">SYSTEM LOAD MONITOR</p>
                    <div class="relative w-full h-24 bg-black/40 border border-emerald-950/80 rounded p-1 overflow-hidden">
                        <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
                            <path id="chart-path" d="M 0 30 Q 10 20, 20 28 T 40 22 T 60 15 T 80 25 T 100 20" fill="none" stroke="#4af626" stroke-width="1.2" class="transition-all duration-500"></path>
                            <path id="chart-area" d="M 0 30 Q 10 20, 20 28 T 40 22 T 60 15 T 80 25 T 100 20 L 100 30 L 0 30 Z" fill="rgba(74, 246, 38, 0.05)" class="transition-all duration-500"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="text-xs text-green-500/40 mt-4 border-t border-emerald-950/40 pt-4 flex justify-between">
                <span>APP_ENV: {{ config('app.env') }}</span>
                <span>PHP: 8.5.6</span>
            </div>
        </div>

        <!-- Big Terminal Panel (Full Width) -->
        <div class="w-full flex flex-col flex-grow">
            <div class="flex-grow p-6 border border-emerald-900/40 bg-black/85 backdrop-blur-md rounded-lg glow-border-green flex flex-col h-[500px] lg:h-auto min-h-[480px]">
                
                <!-- Terminal Header -->
                <div class="flex justify-between items-center pb-4 border-b border-emerald-950 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        <span class="text-xs text-green-500/60 uppercase tracking-widest ml-2">INTERACTIVE SHELL v1.0.8</span>
                        <span id="monitor-status" class="hidden text-xs px-2 py-0.5 rounded bg-red-900/40 border border-red-500/40 text-red-400 animate-pulse ml-3">● MONITORING</span>
                    </div>
                    <span class="text-xs text-green-500/40">ttyS001</span>
                </div>
                
                <!-- Terminal Content -->
                <div id="terminal-content" class="flex-grow overflow-y-auto text-sm space-y-2 pr-2 mb-4 terminal-scroll text-green-400/90 font-mono">
                    <div>Welcome to FORKED_DATA terminal portal.</div>
                    <div>Type <span class="text-green-300 font-semibold uppercase">help</span> for list of available commands.</div>
                    <div class="border-b border-emerald-950/40 pb-2">Ready for inputs...</div>
                </div>
                
                <!-- Live Alert Banner -->
                <div id="live-alert" class="hidden mb-2 p-2 rounded border border-red-500/60 bg-red-950/30 text-red-400 text-xs font-mono animate-pulse">
                    <span id="live-alert-text"></span>
                </div>

                <!-- Terminal Input -->
                <div class="flex items-center gap-2 pt-2 border-t border-emerald-950">
                    <span class="text-green-500 font-bold shrink-0">guest@forkeddata:~$</span>
                    <input type="text" id="terminal-input" autofocus autocomplete="off" class="flex-grow bg-transparent border-none outline-none text-green-300 focus:ring-0 p-0 font-mono text-sm" placeholder="type a command...">
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 p-4 border border-emerald-900/20 bg-zinc-950/40 backdrop-blur rounded-lg text-xs text-green-500/40 mt-6">
        <div>
            &copy; 2026 FORKED_DATA // SYSTEM MONITOR. ALL DEVIATIONS REGISTERED.
        </div>
        <div class="flex items-center gap-4">
            <a href="https://laravel.com" class="hover:text-green-400 transition-colors uppercase">LARAVEL DOCS</a>
            <span>//</span>
            <a href="https://filamentphp.com" class="hover:text-green-400 transition-colors uppercase">FILAMENT DOCS</a>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Matrix Rain Effect
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const columns = Math.floor(width / 20) + 1;
        const yPositions = Array.from({ length: columns }).fill(0);

        function matrix() {
            ctx.fillStyle = 'rgba(5, 8, 6, 0.05)';
            ctx.fillRect(0, 0, width, height);

            ctx.fillStyle = '#10b981';
            ctx.font = '15pt monospace';

            yPositions.forEach((y, index) => {
                const text = String.fromCharCode(33 + Math.random() * 93);
                const x = index * 20;
                ctx.fillText(text, x, y);

                if (y > 100 + Math.random() * 10000) {
                    yPositions[index] = 0;
                } else {
                    yPositions[index] = y + 20;
                }
            });
        }

        let matrixInterval = setInterval(matrix, 50);

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        // Live Clock
        function updateTime() {
            const timeSpan = document.getElementById('live-time');
            const now = new Date();
            timeSpan.innerText = now.toUTCString().replace('GMT', 'UTC');
        }
        setInterval(updateTime, 1000);
        updateTime();

        // System Load Mock Chart Animator
        const chartPath = document.getElementById('chart-path');
        const chartArea = document.getElementById('chart-area');
        
        function updateChart() {
            const points = [];
            for (let i = 0; i <= 10; i++) {
                const x = i * 10;
                const y = Math.floor(5 + Math.random() * 20); // range 5 to 25
                points.push({ x, y });
            }
            
            // Build SVG path
            let d = `M 0 ${points[0].y}`;
            for (let i = 1; i < points.length; i++) {
                d += ` L ${points[i].x} ${points[i].y}`;
            }
            
            chartPath.setAttribute('d', d);
            chartArea.setAttribute('d', d + ' L 100 30 L 0 30 Z');
        }
        setInterval(updateChart, 2000);
        updateChart();

        // Interactive Terminal Logic
        const users = {!! json_encode($users) !!};
        const services = {!! json_encode($services) !!};
        const rules = {!! json_encode($rules) !!};
        const breaches = {!! json_encode($breaches) !!};
        const pwnedRules = {!! json_encode($pwnedRules) !!};
        const terminalContent = document.getElementById('terminal-content');
        const terminalInput = document.getElementById('terminal-input');

        const commands = {
            help: {
                desc: 'Show list of available commands',
                run: () => [
                    'Available commands:',
                    '  <span class="text-cyan-300 font-semibold">help</span>             - Show this help list',
                    '  <span class="text-cyan-300 font-semibold">status</span>           - Query system & database connectivity telemetry',
                    '  <span class="text-cyan-300 font-semibold">users</span>            - Read encrypted database accounts table',
                    '  <span class="text-cyan-300 font-semibold">sherlock [nick]</span>   - Run OSINT search simulator for a username',
                    '  <span class="text-cyan-300 font-semibold">pwned [email]</span>     - Check email against leaked HIBP databases',
                    '  <span class="text-red-400 font-semibold">monitor</span>          - Toggle live capture feed (auto-alerts)',
                    '  <span class="text-red-400 font-semibold">captures</span>         - Show last 5 captured form submissions',
                    '  <span class="text-cyan-400 font-semibold">packets</span>          - Show last 10 network packets',
                    '  <span class="text-cyan-300 font-semibold">ping</span>             - Test latency to docker containers',
                    '  <span class="text-cyan-300 font-semibold">hack</span>             - Run brute force simulator',
                    '  <span class="text-cyan-300 font-semibold">clear</span>            - Clean terminal screen'
                ]
            },
            status: {
                desc: 'Query system telemetry',
                run: () => [
                    'Querying nodes...',
                    'Database: MySQL v8.4 ......... <span class="text-green-400 font-bold">ONLINE (port 3306)</span>',
                    'Cache: Redis Alpine ........... <span class="text-green-400 font-bold">ONLINE (port 6379)</span>',
                    'Mailpit Server ................ <span class="text-green-400 font-bold">ONLINE (port 8025 / 1025)</span>',
                    'Vite Dev Server ............... <span class="text-cyan-400 font-bold">READY (port 5173)</span>',
                    'Environment: local ............ <span class="text-green-400">SECURE</span>',
                    'Host Kernel: {{ php_uname("s") }} {{ php_uname("r") }}'
                ]
            },
            users: {
                desc: 'Read database users',
                run: () => {
                    const lines = ['Querying users database...', '--------------------------------------------'];
                    if (users.length === 0) {
                        lines.push('No registered users found.');
                    } else {
                        users.forEach((u, i) => {
                            lines.push(`[${i}] NAME: ${u.name.padEnd(16)} | EMAIL: ${u.email}`);
                        });
                    }
                    lines.push('--------------------------------------------');
                    lines.push(`Total registers: ${users.length}`);
                    return lines;
                }
            },
            ping: {
                desc: 'Ping containers',
                run: () => [
                    'PING mysql (172.20.0.3): 56 data bytes',
                    '64 bytes from mysql: icmp_seq=0 ttl=64 time=0.082 ms',
                    '64 bytes from mysql: icmp_seq=1 ttl=64 time=0.071 ms',
                    '--- mysql ping statistics ---',
                    '2 packets transmitted, 2 packets received, 0% packet loss, time 1002ms',
                    'rtt min/avg/max = 0.071/0.076/0.082 ms'
                ]
            },
            clear: {
                desc: 'Clear terminal screen',
                run: null // special case
            },
            hack: {
                desc: 'Run hack simulation',
                run: (arg) => {
                    // special case handled in listener
                    return [];
                }
            },
            sherlock: {
                desc: 'Run username search',
                run: (arg) => {
                    // special case handled in listener
                    return [];
                }
            },
            pwned: {
                desc: 'Check email against leaks',
                run: (arg) => {
                    // special case handled in listener
                    return [];
                }
            },
            monitor: {
                desc: 'Toggle live capture monitoring',
                run: () => { return []; }  // special case
            },
            captures: {
                desc: 'Show last captured form submissions',
                run: () => { return []; }  // special case
            },
            packets: {
                desc: 'Show last captured network packets',
                run: () => { return []; }  // special case
            }
        };

        function printToTerminal(lines) {
            lines.forEach(line => {
                const div = document.createElement('div');
                div.innerHTML = line;
                terminalContent.appendChild(div);
            });
            // Auto scroll
            terminalContent.scrollTop = terminalContent.scrollHeight;
        }

        function escapeHtml(value) {
            return String(value ?? '—')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatDisplayValue(value) {
            if (value === null || value === undefined || value === '') {
                return '—';
            }

            if (typeof value === 'object') {
                return Array.isArray(value)
                    ? `[${value.length} items]`
                    : `{${Object.keys(value).length} fields}`;
            }

            return String(value);
        }

        function renderKvRows(rows) {
            return rows
                .map(([label, value]) => `
                    <div class="terminal-kv-label">${escapeHtml(label)}</div>
                    <div class="terminal-kv-value">${escapeHtml(formatDisplayValue(value))}</div>
                `)
                .join('');
        }

        function getPayloadEntries(payload) {
            if (!payload) {
                return [];
            }

            if (typeof payload === 'object' && !Array.isArray(payload)) {
                return Object.entries(payload);
            }

            return [['payload', payload]];
        }

        function renderPayloadFields(payload) {
            const entries = getPayloadEntries(payload);

            if (entries.length === 0) {
                return renderKvRows([['payload', '—']]);
            }

            return `
                <div class="terminal-section">┌ payload</div>
                <div class="terminal-tree">
                    ${renderTreeRows(entries)}
                </div>
            `;
        }

        function renderScalarValue(value) {
            if (value === null || value === undefined || value === '') {
                return '<span class="terminal-muted">—</span>';
            }

            if (typeof value === 'boolean') {
                return value ? 'true' : 'false';
            }

            if (typeof value === 'number') {
                return String(value);
            }

            return escapeHtml(String(value));
        }

        function renderTreeRows(entries, depth = 0) {
            return entries
                .map(([key, value]) => {
                    const prefix = depth === 0 ? '├─' : '│ '.repeat(depth) + '├─';
                    const label = `${prefix} ${key}`;

                    if (value && typeof value === 'object' && !Array.isArray(value)) {
                        return `
                            <div class="terminal-tree-row">
                                <div class="terminal-tree-key">${escapeHtml(label)}</div>
                                <div class="terminal-tree-value terminal-muted">{${Object.keys(value).length} fields}</div>
                            </div>
                            ${renderTreeRows(Object.entries(value), depth + 1)}
                        `;
                    }

                    if (Array.isArray(value)) {
                        const preview = value
                            .slice(0, 4)
                            .map(item => typeof item === 'object' ? formatDisplayValue(item) : formatDisplayValue(item))
                            .join(', ');
                        const suffix = value.length > 4 ? `, +${value.length - 4} more` : '';

                        return `
                            <div class="terminal-tree-row">
                                <div class="terminal-tree-key">${escapeHtml(label)}</div>
                                <div class="terminal-tree-value">[${escapeHtml(preview + suffix)}]</div>
                            </div>
                        `;
                    }

                    return `
                        <div class="terminal-tree-row">
                            <div class="terminal-tree-key">${escapeHtml(label)}</div>
                            <div class="terminal-tree-value">${renderScalarValue(value)}</div>
                        </div>
                    `;
                })
                .join('');
        }

        function renderCaptureCard(c, index = null) {
            const title = index === null ? 'CAPTURE' : `CAPTURE #${index}`;

            return `
                <div class="terminal-record terminal-record--capture">
                    <div class="terminal-record-title">
                        <span class="text-red-400 font-bold">[${escapeHtml(title)}]</span>
                        ${formatSourceBadge(c.source || 'unknown')}
                        <span class="text-green-300">${escapeHtml(c.ip_address || '?')}</span>
                        <span class="text-gray-500">@ ${escapeHtml(c.created_at || '—')}</span>
                    </div>
                    <div class="terminal-kv-grid">
                        ${renderKvRows([
                            ['source', c.source],
                            ['ip_address', c.ip_address],
                            ['user_agent', c.user_agent],
                            ['created_at', c.created_at],
                        ])}
                        ${renderPayloadFields(c.payload)}
                    </div>
                </div>
            `;
        }

        function renderPacketCard(p, index = null) {
            const protoColor = {
                HTTP: 'text-orange-400', HTTPS: 'text-green-400',
                DNS: 'text-lime-400', TCP: 'text-cyan-400',
                UDP: 'text-yellow-400', ICMP: 'text-pink-400',
                ARP: 'text-purple-400'
            };
            const c = protoColor[p.protocol] || 'text-gray-400';
            const title = index === null ? 'PKT' : `PKT #${index}`;

            return `
                <div class="terminal-record terminal-record--packet">
                    <div class="terminal-record-title">
                        <span class="text-cyan-400 font-bold">[${escapeHtml(title)}]</span>
                        <span class="${c} font-bold">[${escapeHtml(p.protocol || '?')}]</span>
                        <span class="text-gray-500">@ ${escapeHtml(p.created_at || '—')}</span>
                    </div>
                    <div class="terminal-kv-grid">
                        ${renderKvRows([
                            ['protocol', p.protocol],
                            ['source', `${p.src_ip || '?'}:${p.src_port || '?'}`],
                            ['destination', `${p.dst_ip || '?'}:${p.dst_port || '?'}`],
                            ['size', p.packet_size ? `${p.packet_size} B` : '—'],
                            ['summary', p.summary],
                            ['created_at', p.created_at],
                        ])}
                    </div>
                </div>
            `;
        }

        // ============================================================
        // LIVE CAPTURE MONITOR — polling /api/latest every 4 seconds
        // ============================================================
        let monitorActive = false;
        let monitorInterval = null;
        let lastCaptureId = 0;
        let lastPacketId = 0;
        const statCaptures = document.getElementById('stat-captures');
        const statPackets  = document.getElementById('stat-packets');
        const monitorBadge  = document.getElementById('monitor-badge');
        const monitorStatus = document.getElementById('monitor-status');
        const liveAlert     = document.getElementById('live-alert');
        const liveAlertText = document.getElementById('live-alert-text');

        function showLiveAlert(msg) {
            liveAlertText.innerHTML = msg;
            liveAlert.classList.remove('hidden');
            setTimeout(() => liveAlert.classList.add('hidden'), 6000);
        }

        function formatSourceBadge(source) {
            const normalizedSource = String(source || 'unknown').toLowerCase();
            const colors = {
                discord: 'text-indigo-400', facebook: 'text-blue-400',
                steam: 'text-cyan-400', uczelnia: 'text-amber-400'
            };
            const c = colors[normalizedSource] || 'text-gray-400';
            return `<span class="${c} font-bold uppercase">[${escapeHtml(source || 'unknown')}]</span>`;
        }

        function pollLiveFeed() {
            fetch('/api/latest')
                .then(r => r.json())
                .then(data => {
                    // Update counters
                    statCaptures.textContent = data.captures.length;
                    statPackets.textContent  = data.packets.length;

                    // Alert on new captures
                    data.captures.forEach(c => {
                        if (c.id > lastCaptureId) {
                            lastCaptureId = c.id;
                            const payloadKeys = getPayloadEntries(c.payload).map(([key]) => key).join(', ') || '—';
                            showLiveAlert(`🔴 NOWE PRZECHWYCENIE! ${formatSourceBadge(c.source)} IP: <span class="text-green-300">${escapeHtml(c.ip_address)}</span> | Pola: ${escapeHtml(payloadKeys)}`);
                            if (monitorActive) {
                                printToTerminal([renderCaptureCard(c)]);
                            }
                        }
                    });

                    // Alert on new interesting packets
                    data.packets.forEach(p => {
                        if (p.id > lastPacketId) {
                            lastPacketId = p.id;
                            if (monitorActive && ['HTTP','HTTPS','DNS','ARP'].includes(p.protocol)) {
                                printToTerminal([renderPacketCard(p)]);
                            }
                        }
                    });
                })
                .catch(() => {});
        }

        function startMonitor() {
            monitorActive = true;
            monitorBadge.textContent = 'ON';
            monitorBadge.className = 'text-xs px-2 py-0.5 rounded border border-red-500/60 text-red-400 animate-pulse';
            monitorStatus.classList.remove('hidden');
            monitorInterval = setInterval(pollLiveFeed, 4000);
            pollLiveFeed(); // immediate first poll
            printToTerminal([
                '<span class="text-red-400 font-bold">[ MONITOR ACTIVE ]</span> Live feed uruchomiony.',
                'Przechwycone dane i pakiety będą wyświetlane automatycznie.',
                'Wpisz <span class="text-red-300">monitor</span> ponownie aby wyłączyć.'
            ]);
        }

        function stopMonitor() {
            monitorActive = false;
            monitorBadge.textContent = 'OFF';
            monitorBadge.className = 'text-xs px-2 py-0.5 rounded border border-gray-700 text-gray-500';
            monitorStatus.classList.add('hidden');
            clearInterval(monitorInterval);
            printToTerminal(['<span class="text-gray-500">[ MONITOR OFF ]</span> Live feed zatrzymany.']);
        }

        terminalInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const value = terminalInput.value.trim();
                terminalInput.value = '';

                if (value === '') return;

                // Print prompt
                printToTerminal([`<span class="text-green-500 font-bold">guest@forkeddata:~$</span> ${value}`]);

                const parts = value.split(' ');
                const cmdName = parts[0].toLowerCase();
                const arg = parts.slice(1).join(' ');

                if (cmdName === 'clear') {
                    terminalContent.innerHTML = '';
                    return;
                }

                if (cmdName === 'hack') {
                    printToTerminal(['Initializing override sequence...']);
                    let progress = 0;
                    const id = setInterval(() => {
                        progress += 10;
                        const bar = '█'.repeat(progress / 5) + '░'.repeat(20 - (progress / 5));
                        printToTerminal([`BYPASSING GATEWAY: [${bar}] ${progress}%`]);
                        if (progress >= 100) {
                            clearInterval(id);
                            printToTerminal([
                                '<span class="text-red-500 font-bold glow-green">!!! BYPASS SUCCESSFUL !!!</span>',
                                'ACCESS GRANTED TO DATABASE RECORDS.',
                                'Redirecting to Administration Console in 2 seconds...',
                            ]);
                            setTimeout(() => {
                                window.location.href = '/admin';
                            }, 2000);
                        }
                    }, 200);
                    return;
                }

                if (cmdName === 'sherlock') {
                    if (!arg) {
                        printToTerminal([
                            '<span class="text-red-400">Error: Username parameter is missing.</span>',
                            'Usage: <span class="text-cyan-300">sherlock [username]</span>',
                            'Example: <span class="text-cyan-300">sherlock admin</span>'
                        ]);
                        return;
                    }

                    if (services.length === 0) {
                        printToTerminal([
                            '[*] No active social networks configured in database.',
                            '[*] Please log in to Filament Admin Panel to activate services.'
                        ]);
                        return;
                    }

                    terminalInput.disabled = true;
                    printToTerminal([
                        `[*] Indexing target username: <span class="text-cyan-300 font-bold">${arg}</span>`,
                        `[*] Scanning ${services.length} active databases...`,
                        '---------------------------------------------'
                    ]);

                    let index = 0;
                    let foundCount = 0;
                    const intervalId = setInterval(() => {
                        if (index >= services.length) {
                            clearInterval(intervalId);
                            printToTerminal([
                                '---------------------------------------------',
                                `[*] Scan complete for: <span class="text-cyan-300 font-bold">${arg}</span>`,
                                `[*] Accounts located: <span class="text-green-400 font-bold">${foundCount} / ${services.length}</span>`,
                                '---------------------------------------------'
                            ]);
                            terminalInput.disabled = false;
                            terminalInput.focus();
                            return;
                        }

                        const service = services[index];
                        const rule = rules.find(r => r.username.toLowerCase() === arg.toLowerCase() && r.service_id === service.id);
                        
                        let isFound = false;
                        if (rule) {
                            isFound = rule.is_found;
                        }

                        if (isFound) {
                            foundCount++;
                            const url = service.url_pattern.replace('{}', arg);
                            printToTerminal([
                                `<span class="text-green-400 font-bold">[+] FOUND:</span> ${service.name.padEnd(15)} => <a href="${url}" target="_blank" class="text-cyan-400 underline hover:text-cyan-200">${url}</a>`
                            ]);
                        }
                        index++;
                    }, 250);
                    return;
                }

                if (cmdName === 'pwned') {
                    if (!arg) {
                        printToTerminal([
                            '<span class="text-red-400">Error: Email address parameter is missing.</span>',
                            'Usage: <span class="text-cyan-300">pwned [email]</span>',
                            'Example: <span class="text-cyan-300">pwned admin@example.com</span>'
                        ]);
                        return;
                    }

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(arg)) {
                        printToTerminal([
                            '<span class="text-yellow-400">Warning: Entered string does not look like a valid email. Scanning anyway...</span>'
                        ]);
                    }

                    if (breaches.length === 0) {
                        printToTerminal([
                            '[*] No active breach databases configured in database.',
                            '[*] Please log in to Filament Admin Panel to activate breach databases.'
                        ]);
                        return;
                    }

                    terminalInput.disabled = true;
                    printToTerminal([
                        `[*] Querying leak repositories for target: <span class="text-cyan-300 font-bold">${arg}</span>`,
                        `[*] Querying ${breaches.length} compromised datasets...`,
                        '---------------------------------------------'
                    ]);

                    const getCompromisedFieldsHtml = (compromisedDataStr, matchedCapture, searchedEmail, rule) => {
                        if (!compromisedDataStr) return [];
                        
                        const items = compromisedDataStr.split(',').map(s => s.trim());
                        const fields = [];
                        const emailPrefix = searchedEmail.split('@')[0] || 'user';
                        
                        let passwordVal = '';
                        let emailVal = searchedEmail;
                        let usernameVal = '';
                        let phoneVal = '';
                        let nameVal = '';

                        // 1. Try to use rule custom fields first
                        if (rule) {
                            if (rule.custom_password) passwordVal = rule.custom_password;
                            if (rule.custom_username) usernameVal = rule.custom_username;
                            if (rule.custom_phone) phoneVal = rule.custom_phone;
                            if (rule.custom_name) nameVal = rule.custom_name;
                        }

                        // 2. Try to use captured payload next
                        if (matchedCapture && matchedCapture.payload) {
                            const payload = matchedCapture.payload;
                            Object.keys(payload).forEach(key => {
                                const lKey = key.toLowerCase();
                                const val = payload[key];
                                if (typeof val === 'string') {
                                    if ((lKey.includes('pass') || lKey.includes('haslo')) && !passwordVal) {
                                        passwordVal = val;
                                    } else if ((lKey.includes('email') || lKey.includes('mail') || lKey.includes('login')) && !emailVal) {
                                        emailVal = val;
                                    } else if ((lKey.includes('user') || lKey.includes('nick')) && !usernameVal) {
                                        usernameVal = val;
                                    } else if ((lKey.includes('phone') || lKey.includes('tel')) && !phoneVal) {
                                        phoneVal = val;
                                    } else if ((lKey.includes('name') || lKey.includes('imie') || lKey.includes('nazwisko')) && !nameVal) {
                                        nameVal = val;
                                    }
                                }
                            });
                        }

                        // 3. Fallbacks to default values if still empty
                        if (!passwordVal) {
                            if (searchedEmail.includes('admin')) {
                                passwordVal = 'admin123';
                            } else if (searchedEmail.includes('test')) {
                                passwordVal = 'password';
                            } else if (searchedEmail.includes('kowalski') || searchedEmail.includes('jan')) {
                                passwordVal = 'haslo123';
                            } else {
                                passwordVal = emailPrefix + '123';
                            }
                        }
                        if (!usernameVal) usernameVal = emailPrefix;
                        if (!phoneVal) phoneVal = '501234567';
                        if (!nameVal) nameVal = 'Jan Kowalski';

                        const mask = (val) => {
                            if (!val) return '';
                            if (val.length <= 3) return '*'.repeat(val.length);
                            const visibleLen = Math.floor(val.length / 2);
                            return val.substring(0, visibleLen) + '*'.repeat(val.length - visibleLen);
                        };

                        items.forEach(item => {
                            const lItem = item.toLowerCase();
                            if (lItem.includes('password') || lItem.includes('credential')) {
                                fields.push(`password: <span class="text-red-300">${mask(passwordVal)}</span>`);
                            } else if (lItem.includes('email') || lItem.includes('mail')) {
                                fields.push(`email: <span class="text-red-300">${mask(emailVal)}</span>`);
                            } else if (lItem.includes('username') || lItem.includes('login')) {
                                fields.push(`username: <span class="text-red-300">${mask(usernameVal)}</span>`);
                            } else if (lItem.includes('phone') || lItem.includes('tel')) {
                                fields.push(`phone: <span class="text-red-300">${mask(phoneVal)}</span>`);
                            } else if (lItem.includes('name')) {
                                fields.push(`name: <span class="text-red-300">${mask(nameVal)}</span>`);
                            }
                        });

                        return fields;
                    };

                    const runPwnedScan = (captures) => {
                        const matchedCapture = captures.find(c => {
                            if (!c.payload) return false;
                            return Object.values(c.payload).some(val => 
                                typeof val === 'string' && val.toLowerCase() === arg.toLowerCase()
                            );
                        });

                        let index = 0;
                        let pwnedCount = 0;
                        const intervalId = setInterval(() => {
                            if (index >= breaches.length) {
                                clearInterval(intervalId);
                                printToTerminal(['---------------------------------------------']);
                                if (pwnedCount > 0) {
                                    printToTerminal([
                                        `<span class="text-yellow-400 font-bold">[+] SCAN COMPLETE: Found ${pwnedCount} compromises in scanned databases.</span>`,
                                        '---------------------------------------------'
                                    ]);
                                } else {
                                    printToTerminal([
                                        `<span class="text-green-400 font-bold">[+] SCAN COMPLETE: No compromises found in scanned databases.</span>`,
                                        '---------------------------------------------'
                                    ]);
                                }
                                terminalInput.disabled = false;
                                terminalInput.focus();
                                return;
                            }

                            const breach = breaches[index];
                            const rule = pwnedRules.find(r => r.email.toLowerCase() === arg.toLowerCase() && r.breach_id === breach.id);
                            
                            let isPwned = false;
                            if (rule) {
                                isPwned = rule.is_pwned;
                            }

                            if (isPwned) {
                                pwnedCount++;
                                const fieldsHtml = getCompromisedFieldsHtml(breach.compromised_data, matchedCapture, arg, rule);
                                const linesToPrint = [
                                    `<span class="text-red-500 font-bold animate-pulse">[!] PWNED:</span> <span class="text-red-300 font-bold">${breach.name}</span> (${breach.breach_date || 'Unknown Date'})`,
                                    `    <span class="text-zinc-500">Leaked data:</span> <span class="text-yellow-500/80">${breach.compromised_data || 'Credentials'}</span>`
                                ];
                                fieldsHtml.forEach(f => {
                                    linesToPrint.push(`      <span class="text-zinc-500">└─</span> ${f}`);
                                });
                                printToTerminal(linesToPrint);
                            } else {
                                printToTerminal([
                                    `<span class="text-green-400">[+] CLEAN:</span> ${breach.name}`
                                ]);
                            }
                            index++;
                        }, 250);
                    };

                    fetch('/api/latest')
                        .then(r => r.json())
                        .then(data => {
                            runPwnedScan(data.captures || []);
                        })
                        .catch(() => {
                            runPwnedScan([]);
                        });

                    return;
                }

                if (cmdName === 'monitor') {
                    if (monitorActive) {
                        stopMonitor();
                    } else {
                        startMonitor();
                    }
                    return;
                }

                if (cmdName === 'captures') {
                    terminalInput.disabled = true;
                    printToTerminal(['[*] Pobieranie ostatnich przechwyconych requestów...']);
                    fetch('/api/latest')
                        .then(r => r.json())
                        .then(data => {
                            const lines = ['---------------------------------------------'];
                            if (data.captures.length === 0) {
                                lines.push('<span class="text-gray-500">Brak przechwyconych requestów.</span>');
                            } else {
                                data.captures.slice(0, 5).forEach((c, i) => {
                                    lines.push(renderCaptureCard(c, i + 1));
                                });
                            }
                            lines.push('---------------------------------------------');
                            lines.push(`Total: <span class="text-red-400 font-bold">${data.captures.length}</span> przechwyconych requestów`);
                            printToTerminal(lines);
                            terminalInput.disabled = false;
                            terminalInput.focus();
                        })
                        .catch(() => {
                            printToTerminal(['<span class="text-red-400">Błąd połączenia z API.</span>']);
                            terminalInput.disabled = false;
                        });
                    return;
                }

                if (cmdName === 'packets') {
                    terminalInput.disabled = true;
                    printToTerminal(['[*] Pobieranie ostatnich pakietów sieciowych...']);
                    fetch('/api/latest')
                        .then(r => r.json())
                        .then(data => {
                            const lines = ['---------------------------------------------'];
                            if (data.packets.length === 0) {
                                lines.push('<span class="text-gray-500">Brak przechwyconych pakietów.</span>');
                            } else {
                                data.packets.slice(0, 10).forEach((p, i) => {
                                    lines.push(renderPacketCard(p, i + 1));
                                });
                            }
                            lines.push('---------------------------------------------');
                            lines.push(`Total: <span class="text-cyan-400 font-bold">${data.packets.length}</span> pakietów (ostatnie 10)`);
                            printToTerminal(lines);
                            terminalInput.disabled = false;
                            terminalInput.focus();
                        })
                        .catch(() => {
                            printToTerminal(['<span class="text-red-400">Błąd połączenia z API.</span>']);
                            terminalInput.disabled = false;
                        });
                    return;
                }

                if (commands[cmdName]) {
                    const result = commands[cmdName].run(arg);
                    printToTerminal(result);
                } else {
                    printToTerminal([
                        `bash: ${cmdName}: command not found.`,
                        `Type <span class="text-green-300 uppercase">help</span> to view available operations.`
                    ]);
                }
            }
        });

        // Focus terminal on click anywhere in body
        document.body.addEventListener('click', (e) => {
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT') {
                terminalInput.focus();
            }
        });

        // Background passive polling (stats only, no terminal output until monitor is ON)
        setInterval(pollLiveFeed, 10000);
        pollLiveFeed();
    </script>
</body>
</html>
