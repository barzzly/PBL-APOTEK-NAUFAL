<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Apotek Naufal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shrink-0 sticky top-0 h-screen shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-20">
        <div class="h-16 flex items-center px-10 border-b border-gray-100 shrink-0">
            <a href="/" class="text-primary text-xl font-bold flex items-center gap-3">
                <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-md shadow-primary/30">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                Apotek Naufal
            </a>
        </div>
        
        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-2 px-3">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-chart-pie w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white/90' : 'text-gray-400' }}"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-list w-5 {{ request()->routeIs('admin.categories*') ? 'text-white/90' : 'text-gray-400' }}"></i> Kategori Obat
            </a>
            <a href="{{ route('admin.medicines') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.medicines*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-pills w-5 {{ request()->routeIs('admin.medicines*') ? 'text-white/90' : 'text-gray-400' }}"></i> Data Obat
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all text-left font-medium text-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-red-400"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col min-w-0 bg-gray-50/50">
        <!-- Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between pl-16 pr-10 sticky top-0 z-10 shrink-0">
            <h1 class="text-xl font-bold text-gray-800">@yield('header_title', 'Dashboard')</h1>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <button class="relative text-gray-400 hover:text-primary transition cursor-pointer">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-0.5 w-2 h-2 bg-secondary rounded-full border border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-gray-200"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-gray-700 group-hover:text-primary transition">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold border-2 border-white shadow-sm overflow-hidden">
                            @if(auth()->user() && auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-8 bg-bg-body">
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>
</html>
