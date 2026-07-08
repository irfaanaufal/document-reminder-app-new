<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-zinc-100">Akses Saya</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Daftar aplikasi yang tersedia dan status akses Anda.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($applications as $app)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-700 p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-800 dark:text-zinc-100 truncate">{{ $app['name'] }}</h3>
                                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 line-clamp-2">{{ $app['description'] ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                            <span class="ml-3 flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $app['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : ($app['has_requested'] ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-slate-100 text-slate-600 dark:bg-zinc-700 dark:text-zinc-400') }}">
                                {{ $app['is_active'] ? 'Aktif' : ($app['has_requested'] ? 'Menunggu' : 'Belum') }}
                            </span>
                        </div>
                        <div class="mt-4">
                            @if ($app['is_active'])
                                <a href="{{ url($app['slug']) }}" target="_blank" class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Buka Aplikasi &rarr;
                                </a>
                            @elseif (!$app['has_requested'])
                                <form method="POST" action="{{ route('applications.request') }}">
                                    @csrf
                                    <input type="hidden" name="application_id" value="{{ $app['id'] }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg hover:bg-slate-700 transition">
                                        Request Access
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400 dark:text-zinc-500">Menunggu persetujuan admin...</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-slate-400 dark:text-zinc-500">
                        Belum ada aplikasi tersedia.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
