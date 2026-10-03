<nav
    x-data="{
        expanded: false,
        docOpen: {{ request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'true' : 'false' }}
    }"
    @mouseenter="expanded = true"
    @mouseleave="expanded = false; docOpen = false"
    :style="expanded ? 'width: 216px' : 'width: 68px'"
    class="hidden md:flex flex-shrink-0 bg-white dark:bg-zinc-900 border-r border-gray-100 dark:border-zinc-800 transition-all duration-300 ease-in-out overflow-hidden flex-col"
>
    <div class="h-full flex flex-col">
        <div class="py-4 flex items-center transition-all duration-200 border-b border-gray-100 dark:border-zinc-800 overflow-hidden"
             :class="expanded ? 'px-2 justify-start' : 'px-2 justify-center'">
            <a href="{{ route('dashboard') }}"
               class="flex items-center overflow-hidden"
               :class="expanded ? 'gap-3 px-2 justify-start' : 'justify-center px-0'">
                <x-application-logo class="block h-10 w-10 flex-shrink-0 object-contain" />
                <span x-show="expanded" x-cloak x-transition class="font-semibold text-lg text-gray-900 dark:text-zinc-100 whitespace-nowrap">{{ config('app.name', 'Reminder App') }}</span>
            </a>
        </div>

        <div class="flex-1 px-2 py-3 overflow-y-auto">
            <ul class="space-y-1">
                @php $active = request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ $active }} flex items-center gap-3 rounded-md transition-colors overflow-hidden" :class="expanded ? 'px-3 py-2 justify-start' : 'px-0 py-2 justify-center'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 1.707a1 1 0 00-1.414 0L2 9v8a1 1 0 001 1h5a1 1 0 001-1v-5h2v5a1 1 0 001 1h5a1 1 0 001-1V9l-7.293-7.293z" />
                        </svg>
                        <span x-show="expanded" x-cloak x-transition class="whitespace-nowrap">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                @if (Auth::user()?->canAccessLogs())
                    @php $activeLogs = request()->routeIs('logs.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                    <li>
                        <a href="{{ route('logs.index') }}" class="{{ $activeLogs }} flex items-center gap-3 rounded-md transition-colors overflow-hidden" :class="expanded ? 'px-3 py-2 justify-start' : 'px-0 py-2 justify-center'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 3A2.25 2.25 0 002 5.25v9.5A2.25 2.25 0 004.25 17h11.5A2.25 2.25 0 0018 14.75v-9.5A2.25 2.25 0 0015.75 3H4.25zM5.5 6.75A.75.75 0 016.25 6h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span x-show="expanded" x-cloak x-transition class="whitespace-nowrap">Logs</span>
                        </a>
                    </li>
                @endif

                @if (! Auth::user()?->isNewUserLevel())
                @php $activeDoc = request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <button type="button" @click="docOpen = !docOpen" class="{{ $activeDoc }} w-full flex items-center rounded-md transition-colors overflow-hidden" :class="expanded ? 'px-3 py-2 justify-between gap-3' : 'px-0 py-2 justify-center'">
                        <span class="flex items-center gap-3" :class="expanded ? '' : 'justify-center'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V8.414A2 2 0 0015.586 7L11 2.414A2 2 0 009.586 2H6z" />
                            </svg>
                            <span x-show="expanded" x-cloak x-transition class="whitespace-nowrap">Dokumen</span>
                        </span>
                        <svg x-show="expanded" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 transition-transform duration-200" :class="docOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="docOpen && expanded" x-cloak x-transition class="mt-1 space-y-1 pl-4">
                        <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('dokumen') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Manajemen Dokumen</a>
                        @if (Auth::user()?->canManageDocumentTypes())
                            <a href="{{ route('doc_type.index') }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Jenis Dokumen</a>
                        @endif
                    </div>
                </li>
                @endif
            </ul>
        </div>

        <div class="border-t border-gray-200 dark:border-zinc-800 mt-auto transition-all duration-200" :class="expanded ? 'px-4 py-4' : 'px-0 py-4'">
            <div class="flex flex-col gap-0.5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="flex items-center h-11 rounded-2xl text-gray-400 dark:text-zinc-500 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition duration-150 w-full overflow-hidden" :class="expanded ? 'gap-3 px-3' : 'justify-center px-0'">
                        <span class="shrink-0 w-[18px] flex justify-center">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1" /><path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z" /></svg>
                        </span>
                        <span x-show="expanded" x-cloak x-transition class="text-sm font-semibold whitespace-nowrap">Keluar</span>
                    </button>
                </form>

                <a href="{{ route('profile.edit') }}" title="Profil"
                   class="flex items-center h-11 rounded-2xl transition-all duration-150 overflow-hidden {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50' : 'hover:bg-gray-50 dark:hover:bg-zinc-900/40' }}"
                   :class="expanded ? 'gap-3 px-3' : 'justify-center px-0'">
                    <div class="w-8 h-8 shrink-0 rounded-full overflow-hidden border-2 border-gray-200 dark:border-zinc-700 bg-slate-100 dark:bg-zinc-800 flex items-center justify-center">
                        @if(Auth::user()->avatar_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <span class="text-[10px] font-black text-slate-600 dark:text-zinc-300 leading-none">{{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div x-show="expanded" x-cloak x-transition class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate leading-tight">{{ explode(' ', Auth::user()->nama ?? 'User')[0] }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 truncate leading-tight" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</nav>

<div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" @keydown.escape.window="sidebarOpen = false">
    <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>

    <aside class="absolute right-0 top-0 h-full w-72 max-w-[85vw] bg-white dark:bg-zinc-900 border-l border-gray-100 dark:border-zinc-800 shadow-2xl shadow-black/20 overflow-y-auto">
        <div class="flex items-center justify-between gap-3 border-b border-gray-200 dark:border-zinc-800 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                <x-application-logo class="block h-10 w-10 object-contain" />
                <span class="font-semibold text-lg text-gray-900 dark:text-zinc-100 truncate">{{ config('app.name', 'Reminder App') }}</span>
            </a>

            <button type="button" @click="sidebarOpen = false" aria-label="Close navigation menu" class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="p-4" x-data="{ docOpen: {{ request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'true' : 'false' }} }">
            <ul class="space-y-1">
                @php $active = request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ $active }} flex items-center gap-3 rounded-md px-3 py-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 1.707a1 1 0 00-1.414 0L2 9v8a1 1 0 001 1h5a1 1 0 001-1v-5h2v5a1 1 0 001 1h5a1 1 0 001-1V9l-7.293-7.293z" />
                        </svg>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>

                @if (Auth::user()?->canAccessLogs())
                    @php $activeLogs = request()->routeIs('logs.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                    <li>
                        <a href="{{ route('logs.index') }}" class="{{ $activeLogs }} flex items-center gap-3 rounded-md px-3 py-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 3A2.25 2.25 0 002 5.25v9.5A2.25 2.25 0 004.25 17h11.5A2.25 2.25 0 0018 14.75v-9.5A2.25 2.25 0 0015.75 3H4.25zM5.5 6.75A.75.75 0 016.25 6h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>Logs</span>
                        </a>
                    </li>
                @endif

                @if (! Auth::user()?->isNewUserLevel())
                @php $activeDoc = request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <button type="button" @click="docOpen = !docOpen" class="{{ $activeDoc }} w-full flex items-center justify-between gap-3 rounded-md px-3 py-2 transition-colors">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V8.414A2 2 0 0015.586 7L11 2.414A2 2 0 009.586 2H6z" />
                            </svg>
                            <span>Dokumen</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="docOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="docOpen" x-transition class="mt-1 space-y-1 pl-4">
                        <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('dokumen') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Manajemen Dokumen</a>
                        @if (Auth::user()?->canManageDocumentTypes())
                            <a href="{{ route('doc_type.index') }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Jenis Dokumen</a>
                        @endif
                    </div>
                </li>
                @endif
            </ul>
        </div>

        <div class="mt-auto border-t border-gray-200 dark:border-zinc-800 p-4">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 h-11 px-3 rounded-2xl transition-all duration-150 overflow-hidden {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50' : 'hover:bg-gray-50 dark:hover:bg-zinc-900/40' }}">
                <div class="w-8 h-8 shrink-0 rounded-full overflow-hidden border-2 border-gray-200 dark:border-zinc-700 bg-slate-100 dark:bg-zinc-800 flex items-center justify-center">
                    @if(Auth::user()->avatar_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-[10px] font-black text-slate-600 dark:text-zinc-300 leading-none">{{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate leading-tight">{{ explode(' ', Auth::user()->nama ?? 'User')[0] }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 truncate leading-tight" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1.5">
                @csrf
                <button type="submit" class="flex items-center gap-3 h-11 px-3 rounded-2xl text-gray-400 dark:text-zinc-500 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition duration-150 w-full">
                    <span class="shrink-0 w-[18px] flex justify-center">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1" /><path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117M11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5M4 1.934V15h6V1.077z" /></svg>
                    </span>
                    <span class="text-sm font-semibold">Keluar</span>
                </button>
            </form>
        </div>
    </aside>
</div>
