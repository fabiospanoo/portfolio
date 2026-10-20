const CODE_LINES = [
  [['{{-- ─────────────────────────────────────────────────────────────────────────────────────── --}}', 'cm']],
  [['@extends', 'dc'], ["('layouts.app')                        ", ''], ['{{-- eredita layout con navbar, sidebar, footer globale --}}', 'cm']],
  [['@section', 'dc'], ["('title', __('Dashboard — ') . ", ''], ['$user', 'nm'], ['->name . ', ''], ["' — MyApp')", 'st']],
  [['@section', 'dc'], ["('meta_description', 'Pannello di controllo con statistiche in tempo reale, notifiche e gestione progetti')", 'st']],
  [['']],
  [['@push', 'dc'], ["('styles')", '']],
  [['    <link', 'kw'], [' rel', 'nm'], ['=', ''], ['"stylesheet"', 'st'], [' href', 'nm'], ['=', ''], ['"{{ asset(\'css/dashboard.css\') }}"', 'fn'], [' />       '], ['{{-- stili specifici del dashboard --}}', 'cm']],
  [['@endpush', 'dc']],
  [['']],
  [['@section', 'dc'], ["('content')", '']],
  [['']],
  [['    {{-- ── Intestazione ─────────────────────────────────────────────────────────────────── --}}', 'cm']],
  [['    <div', 'kw'], [' class', 'nm'], ['=', ''], ['"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"', 'st'], ['>', '']],
  [['        <div', 'kw'], [' class', 'nm'], ['=', ''], ['"flex items-center justify-between mb-8"', 'st'], ['>', '']],
  [['            <div', 'kw'], ['>', '']],
  [['                <h1', 'kw'], [' class', 'nm'], ['=', ''], ['"text-3xl font-bold tracking-tight text-gray-900 dark:text-white"', 'st'], ['>', '']],
  [['                    ', ''], ['{{ __("Ciao,") }}', 'fn'], [' <span', 'kw'], [' class', 'nm'], ['=', ''], ['"text-indigo-600 dark:text-indigo-400"', 'st'], ['>{{ $user->name }}</span>', '']],
  [['                </h1>', 'kw']],
  [['                <p', 'kw'], [' class', 'nm'], ['=', ''], ['"mt-1 text-sm text-gray-500 dark:text-gray-400"', 'st'], ['>', ''], ['{{ now()->translatedFormat("l d F Y, H:i") }}', 'fn'], ['</p>', 'kw']],
  [['            </div>', '']],
  [['            <a', 'kw'], [' href', 'nm'], ['=', ''], ['"{{ route(\'projects.create\') }}"', 'fn'], [' class', 'nm'], ['=', ''], ['"inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow hover:bg-indigo-700 transition"', 'st'], ['>', '']],
  [['                <x-heroicon-o-plus', 'cl'], [' class', 'nm'], ['=', ''], ['"w-4 h-4"', 'st'], [' />  {{ __("Nuovo progetto") }}', '']],
  [['            </a>', 'kw']],
  [['        </div>', '']],
  [['']],
  [['        {{-- ── Cards statistiche ──────────────────────────────────────────────────────────── --}}', 'cm']],
  [['        <div', 'kw'], [' class', 'nm'], ['=', ''], ['"grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10"', 'st'], ['>', '']],
  [['            @foreach', 'dc'], [' ($stats as $stat)                       ', ''], ['{{-- itera le statistiche dal controller --}}', 'cm']],
  [['                <div', 'kw'], [' class', 'nm'], ['=', ''], ['"relative overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm"', 'st'], ['>', '']],
  [['                    <dt', 'kw'], [' class', 'nm'], ['=', ''], ['"truncate text-sm font-medium text-gray-500 dark:text-gray-400"', 'st'], ['>{{ $stat->label }}</dt>', '']],
  [['                    <dd', 'kw'], [' class', 'nm'], ['=', ''], ['"mt-2 flex items-baseline gap-2"', 'st'], ['>', '']],
  [['                        <span', 'kw'], [' class', 'nm'], ['=', ''], ['"text-4xl font-extrabold text-gray-900 dark:text-white"', 'st'], ['>{{ number_format($stat->value) }}</span>', '']],
  [['                        @if', 'dc'], [' ($stat->trend > ', ''], ['0', 'nb'], [')', '']],
  [['                            <span', 'kw'], [' class', 'nm'], ['=', ''], ['"text-sm font-semibold text-emerald-600"', 'st'], ['>↑ {{ $stat->trend }}%</span>', '']],
  [['                        @elseif', 'dc'], [' ($stat->trend < ', ''], ['0', 'nb'], [')', '']],
  [['                            <span', 'kw'], [' class', 'nm'], ['=', ''], ['"text-sm font-semibold text-red-500"', 'st'], ['>↓ {{ abs($stat->trend) }}%</span>', '']],
  [['                        @else', 'dc']],
  [['                            <span', 'kw'], [' class', 'nm'], ['=', ''], ['"text-sm font-semibold text-gray-400"', 'st'], ['>— stabile</span>', '']],
  [['                        @endif', 'dc']],
  [['                    </dd>', '']],
  [['                </div>', '']],
  [['            @endforeach', 'dc']],
  [['        </div>', '']],
  [['']],
  [['        {{-- ── Tabella progetti ───────────────────────────────────────────────────────────── --}}', 'cm']],
  [['        <div', 'kw'], [' class', 'nm'], ['=', ''], ['"rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden mb-8"', 'st'], ['>', '']],
  [['            <div', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between"', 'st'], ['>', '']],
  [['                <h2', 'kw'], [' class', 'nm'], ['=', ''], ['"font-semibold text-gray-900 dark:text-white"', 'st'], ['>Progetti recenti</h2>', '']],
  [['                <a', 'kw'], [' href', 'nm'], ['=', ''], ['"{{ route(\'projects.index\') }}"', 'fn'], [' class', 'nm'], ['=', ''], ['"text-sm text-indigo-600 hover:text-indigo-500 font-medium"', 'st'], ['>Vedi tutti →</a>', '']],
  [['            </div>', '']],
  [['            <table', 'kw'], [' class', 'nm'], ['=', ''], ['"min-w-full divide-y divide-gray-200 dark:divide-gray-700"', 'st'], ['>', '']],
  [['                <thead', 'kw'], [' class', 'nm'], ['=', ''], ['"bg-gray-50 dark:bg-gray-900/50"', 'st'], ['>', '']],
  [['                    <tr', 'kw'], ['>', '']],
  [['                        @foreach', 'dc'], [" (['Progetto', 'Stato', 'Team', 'Scadenza', 'Priorità'] as $col)", '']],
  [['                            <th', 'kw'], [' scope', 'nm'], ['=', ''], ['"col"', 'st'], [' class', 'nm'], ['=', ''], ['"px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"', 'st'], ['>{{ $col }}</th>', '']],
  [['                        @endforeach', 'dc']],
  [['                    </tr>', '']],
  [['                </thead>', '']],
  [['                <tbody', 'kw'], [' class', 'nm'], ['=', ''], ['"divide-y divide-gray-100 dark:divide-gray-700"', 'st'], ['>', '']],
  [['                    @forelse', 'dc'], [' ($projects as $project)', '']],
  [['                        <tr', 'kw'], [' wire:key', 'nm'], ['=', ''], ['"project-{{ $project->id }}"', 'fn'], [' class', 'nm'], ['=', ''], ['"hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"', 'st'], ['>', '']],
  [['                            <td', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4"', 'st'], ['>', '']],
  [['                                <a', 'kw'], [' href', 'nm'], ['=', ''], ['"{{ route(\'projects.show\', $project) }}"', 'fn'], [' class', 'nm'], ['=', ''], ['"font-medium text-gray-900 dark:text-white hover:text-indigo-600 transition"', 'st'], ['>{{ $project->name }}</a>', '']],
  [['                                <p', 'kw'], [' class', 'nm'], ['=', ''], ['"text-xs text-gray-400 mt-0.5 truncate max-w-xs"', 'st'], ['>{{ $project->description }}</p>', '']],
  [['                            </td>', '']],
  [['                            <td', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4"', 'st'], ['>', '']],
  [['                                <x-badge', 'cl'], [' :color', 'nm'], ['=', ''], ['"$project->statusColor()"', 'fn'], [' :label', 'nm'], ['=', ''], ['"$project->status_label"', 'fn'], [' />', '']],
  [['                            </td>', '']],
  [['                            <td', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4"', 'st'], ['>', '']],
  [['                                <x-avatar-group', 'cl'], [' :users', 'nm'], ['=', ''], ['"$project->members->take(4)"', 'fn'], [' :extra', 'nm'], ['=', ''], ['"max(0, $project->members->count() - 4)"', 'fn'], [' />', '']],
  [['                            </td>', '']],
  [['                            <td', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4 text-sm text-gray-500"', 'st'], ['>', ''], ['{{ $project->due_date?->format("d M Y") ?? "—" }}', 'fn'], ['</td>', 'kw']],
  [['                        </tr>', '']],
  [['                    @empty', 'dc']],
  [['                        <tr><td', 'kw'], [' colspan', 'nm'], ['=', ''], ['"5"', 'st'], [' class', 'nm'], ['=', ''], ['"px-6 py-16 text-center text-sm text-gray-400"', 'st'], ['>Nessun progetto. <a href="{{ route(\'projects.create\') }}" class="text-indigo-600 underline">Creane uno →</a></td></tr>', '']],
  [['                    @endforelse', 'dc']],
  [['                </tbody>', '']],
  [['            </table>', '']],
  [['            @if', 'dc'], [" ($projects->hasPages())                    ", ''], ['{{-- paginazione automatica Laravel --}}', 'cm']],
  [['                <div', 'kw'], [' class', 'nm'], ['=', ''], ['"px-6 py-4 border-t border-gray-200 dark:border-gray-700"', 'st'], ['>{{ $projects->links() }}</div>', '']],
  [['            @endif', 'dc']],
  [['        </div>', '']],
  [['']],
  [['        {{-- ── Livewire components ────────────────────────────────────────────────────────── --}}', 'cm']],
  [['        @livewire', 'dc'], ["('dashboard.activity-feed',       ['userId' => ", ''], ['$user', 'nm'], ['->id, ', ''], ["'limit' => ", 'st'], ['20', 'nb'], [", 'filter' => 'all'], key($user->id)", '']],
  [['        @livewire', 'dc'], ["('dashboard.notifications-panel', ['userId' => ", ''], ['$user', 'nm'], ["->id, 'unreadOnly' => true],  key('notif-' . ", ''], ['$user', 'nm'], ['->id)', '']],
  [['']],
  [['    </div>', '']],
  [['']],
  [['@endsection', 'dc']],
  [['']],
  [['@push', 'dc'], ["('scripts')", '']],
  [['    <script src="{{ asset(\'js/dashboard.js\') }}" defer></script>', 'kw']],
  [['    <script>', 'kw']],
  [['        window.__DASHBOARD_CONFIG__ = @json', 'dc'], ['([', '']],
  [['            ', ''], ["'userId'    ", 'st'], [' => ', ''], ['$user', 'nm'], ['->id,', '']],
  [['            ', ''], ["'locale'    ", 'st'], [' => ', ''], ['app', 'nm'], ['()->getLocale(),', '']],
  [['            ', ''], ["'timezone'  ", 'st'], [' => ', ''], ['$user', 'nm'], ["->timezone ?? config('app.timezone'),", '']],
  [['            ', ''], ["'csrfToken' ", 'st'], [' => ', ''], ['csrf_token', 'nm'], ['(),', '']],
  [['            ', ''], ["'pusherKey' ", 'st'], [' => ', ''], ["env('PUSHER_APP_KEY'),", 'nm']],
  [['        ]);', '']],
  [['    </script>', 'kw']],
  [['@endpush', 'dc']],
  [['']],
];

const output = document.getElementById('code-output');
const lineNos = document.getElementById('line-numbers');

const LINE_H = 28;
const getMax = () => Math.floor(window.innerHeight / LINE_H) + 2;

let lineIndex = 0, tokenIndex = 0, tokenCharIdx = 0, currentLine = null;
const BASE = 8, VAR = 6, LINE_PAUSE = 60;
const rand = (a, b) => Math.floor(Math.random() * (b - a + 1)) + a;

function addLineNum(n) {
  const el = document.createElement('div');
  el.className = 'line-num';
  el.textContent = n;
  lineNos.appendChild(el);
}

function prune() {
  const max = getMax();
  while (output.children.length > max) {
    output.removeChild(output.firstChild);
    if (lineNos.firstChild) lineNos.removeChild(lineNos.firstChild);
  }
}

function startNewLine() {
  const old = output.querySelector('.cursor');
  if (old) old.remove();
  currentLine = document.createElement('div');
  currentLine.className = 'code-line';
  output.appendChild(currentLine);
  addLineNum(lineIndex + 1);
  const cur = document.createElement('span');
  cur.className = 'cursor';
  currentLine.appendChild(cur);
  tokenIndex = 0;
  tokenCharIdx = 0;
  prune();
}

function typeNext() {
  const tokens = CODE_LINES[lineIndex];

  if (tokens.length === 1 && tokens[0][0] === '') {
    if (!currentLine) startNewLine();
    const old = output.querySelector('.cursor');
    if (old) old.remove();
    lineIndex = (lineIndex + 1) % CODE_LINES.length;
    currentLine = null;
    setTimeout(tick, LINE_PAUSE);
    return;
  }

  if (tokenIndex >= tokens.length) {
    lineIndex = (lineIndex + 1) % CODE_LINES.length;
    currentLine = null;
    setTimeout(tick, LINE_PAUSE);
    return;
  }

  const [text, cls] = tokens[tokenIndex];
  if (tokenCharIdx === 0) {
    const sp = document.createElement('span');
    if (cls) sp.className = cls;
    const cursor = currentLine.querySelector('.cursor');
    currentLine.insertBefore(sp, cursor);
    currentLine._span = sp;
  }
  currentLine._span.textContent += text[tokenCharIdx];
  tokenCharIdx++;
  if (tokenCharIdx >= text.length) {
    tokenIndex++;
    tokenCharIdx = 0;
  }
  setTimeout(tick, rand(BASE - VAR, BASE + VAR));
}

function tick() {
  if (!currentLine) startNewLine();
  typeNext();
}

tick();
