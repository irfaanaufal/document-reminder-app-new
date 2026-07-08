<nav x-data="{ userOpen: false, docOpen: {{ request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'true' : 'false' }} }" @mouseenter="$root.hovered = true" @mouseleave="$root.hovered = false" class="hidden md:block fixed inset-y-0 left-0 z-30 bg-white dark:bg-zinc-900 border-r border-gray-100 dark:border-zinc-800 transition-all duration-200 ease-in-out overflow-hidden" :class="$root.collapsed && !$root.hovered ? 'w-20' : 'w-64'">
    <div class="h-screen flex flex-col">
        <div class="px-2 py-4 flex items-center justify-between transition-all duration-200 border-b border-gray-100 dark:border-zinc-800 overflow-hidden">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden px-2">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-zinc-100" />
                <span class="font-semibold text-lg text-gray-900 dark:text-zinc-100 whitespace-nowrap transition-all duration-200" :class="($root.collapsed && !$root.hovered) ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[12rem]'">{{ config('app.name', 'Laravel') }}</span>
            </a>
        </div>

        <div class="flex-1 px-2 py-3 overflow-y-auto">
            <ul class="space-y-1">
                @php $active = request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ $active }} flex items-center gap-3 rounded-md px-3 py-2 transition-colors overflow-hidden" :class="$root.collapsed && !$root.hovered ? 'justify-center' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 1.707a1 1 0 00-1.414 0L2 9v8a1 1 0 001 1h5a1 1 0 001-1v-5h2v5a1 1 0 001 1h5a1 1 0 001-1V9l-7.293-7.293z" />
                        </svg>
                        <span class="whitespace-nowrap transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[8rem]'">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                @if (Auth::user()?->canUseChatbot())
                    <li>
                        <a href="javascript:void(0)" class="chatbot-trigger flex items-center gap-3 rounded-md px-3 py-2 transition-colors overflow-hidden text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800" :class="$root.collapsed && !$root.hovered ? 'justify-center' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                                <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                            </svg>
                            <span class="whitespace-nowrap transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[8rem]'">AI Assistant</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()?->isAdmin())
                    @php $activeLogs = request()->routeIs('logs.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                    <li>
                        <a href="{{ route('logs.index') }}" class="{{ $activeLogs }} flex items-center gap-3 rounded-md px-3 py-2 transition-colors overflow-hidden" :class="$root.collapsed && !$root.hovered ? 'justify-center' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 3A2.25 2.25 0 002 5.25v9.5A2.25 2.25 0 004.25 17h11.5A2.25 2.25 0 0018 14.75v-9.5A2.25 2.25 0 0015.75 3H4.25zM5.5 6.75A.75.75 0 016.25 6h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zm0 3.5a.75.75 0 01.75-.75h4.5a.75.75 0 010 1.5h-4.5a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span class="whitespace-nowrap transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[8rem]'">Logs</span>
                        </a>
                    </li>
                @endif



                @php $activeDoc = request()->routeIs('dokumen') || request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                <li>
                    <button type="button" @click="docOpen = !docOpen" class="{{ $activeDoc }} w-full flex items-center justify-between gap-3 rounded-md px-3 py-2 transition-colors overflow-hidden" :class="$root.collapsed && !$root.hovered ? 'justify-center' : ''">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V8.414A2 2 0 0015.586 7L11 2.414A2 2 0 009.586 2H6z" />
                            </svg>
                            <span class="whitespace-nowrap transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[12rem]'">Dokumen</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="docOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" :style="$root.collapsed && !$root.hovered ? 'display: none;' : ''">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="docOpen && !($root.collapsed && !$root.hovered)" x-transition class="mt-1 space-y-1 pl-4">
                        <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('dokumen') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Manajemen Dokumen</a>
                        <a href="{{ route('doc_type.index') }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Jenis Dokumen</a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="px-4 py-4 border-t border-gray-200 dark:border-zinc-800 mt-auto overflow-visible">
            <div class="relative" x-data="{ userOpen: false }">
                <button @click.stop="userOpen = !userOpen" class="w-full text-left">
                    <div class="text-sm font-medium text-gray-800 dark:text-zinc-100 truncate transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100 max-w-[12rem]'" title="{{ Auth::user()->nama }}">{{ Auth::user()->nama }}</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-300 truncate max-w-[12rem] transition-all duration-200" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100'" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</div>
                    @if (Auth::user()?->isSuperAdmin())
                        <span class="mt-2 inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300" :class="$root.collapsed && !$root.hovered ? 'opacity-0 max-w-0 pointer-events-none' : 'opacity-100'">Super Admin</span>
                    @endif
                </button>

                <div x-show="userOpen" x-transition @click.away="userOpen = false" class="absolute left-0 bottom-full z-50 mb-2 w-48 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded shadow-lg">
                    <div class="py-2">
                        <a @click.stop href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button @click.stop type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-zinc-800">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<div x-cloak x-show="sidebarOpen && window.innerWidth < 768" class="fixed inset-0 z-40 md:hidden" @keydown.escape.window="sidebarOpen = false">
    <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>

    <aside class="absolute right-0 top-0 h-full w-72 max-w-[85vw] bg-white dark:bg-zinc-900 border-l border-gray-100 dark:border-zinc-800 shadow-2xl shadow-black/20 overflow-y-auto">
        <div class="flex items-center justify-between gap-3 border-b border-gray-200 dark:border-zinc-800 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-zinc-100" />
                <span class="font-semibold text-lg text-gray-900 dark:text-zinc-100 truncate">{{ config('app.name', 'Laravel') }}</span>
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

                @if (Auth::user()?->canUseChatbot())
                    @php $activeChatbot = request()->routeIs('chatbot.index') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800'; @endphp
                    <li>
                        <a href="javascript:void(0)" class="chatbot-trigger flex items-center gap-3 rounded-md px-3 py-2 transition-colors text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                                <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                            </svg>
                            <span>AI Assistant</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()?->isAdmin())
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
                        <a href="{{ route('doc_type.index') }}" class="block rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('doc_type.*') ? 'bg-gray-100 text-gray-900 dark:bg-zinc-800 dark:text-zinc-100' : 'text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800' }}">Jenis Dokumen</a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="mt-auto border-t border-gray-200 dark:border-zinc-800 p-4 overflow-visible">
            <div class="text-sm font-medium text-gray-800 dark:text-zinc-100 truncate" title="{{ Auth::user()->nama }}">{{ Auth::user()->nama }}</div>
            <div class="text-xs text-gray-500 dark:text-zinc-300 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</div>
            @if (Auth::user()?->isSuperAdmin())
                <span class="mt-2 inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">Super Admin</span>
            @endif
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-800">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-zinc-800">Log Out</button>
                </form>
            </div>
        </div>
    </aside>
</div>
