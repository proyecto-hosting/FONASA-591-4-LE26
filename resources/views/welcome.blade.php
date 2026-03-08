<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Técnico - NexoHost SpA</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-gray-900 font-sans bg-gray-50 selection:bg-cyan-500 selection:text-white">
    
    <nav class="absolute top-0 w-full z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="text-2xl font-bold text-white tracking-wider">NexoHost<span class="text-cyan-400">.</span></span>
        </div>
        
        @if (Route::has('login'))
            <div class="flex gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-white hover:text-cyan-300 transition-colors">Ir al Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-white hover:text-cyan-300 transition-colors py-2">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="font-semibold bg-cyan-500 hover:bg-cyan-400 text-gray-900 px-4 py-2 rounded-lg transition-all shadow-lg hover:shadow-cyan-500/30">Registrar Evaluador</a>
                    @endif
                @endauth
            </div>
        @endif
    </nav>

    <div class="relative bg-gradient-to-br from-purple-900 via-[#1e3a5f] to-teal-900 pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-cyan-500/20 text-cyan-300 text-sm font-semibold tracking-wider mb-4 border border-cyan-500/30">
                ENTORNO DE DEMOSTRACIÓN
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-6">
                Plataforma de Notificación <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-teal-300">Gubernamental Segura</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-300 mb-10">
                Arquitectura dedicada en Google Cloud para el procesamiento masivo, despacho asíncrono y auditoría de trazabilidad exigida por las bases técnicas.
            </p>
            
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="bg-cyan-500 hover:bg-cyan-400 text-gray-900 font-bold px-8 py-3 rounded-lg text-lg transition-all shadow-[0_0_20px_rgba(6,182,212,0.4)] hover:shadow-[0_0_30px_rgba(6,182,212,0.6)] transform hover:-translate-y-1">
                    Ingresar al Demo
                </a>
            </div>
        </div>
    </div>

    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">¿Qué evalúa este entorno?</h2>
                <p class="mt-4 text-lg text-gray-600">El ciclo de vida completo de una campaña de notificaciones.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">1. Sincronización Segura</h3>
                    <p class="text-gray-600">Simulación de ingesta de datos vía SFTP. El sistema lee archivos CSV/Excel, normaliza los registros (RUT/Email) y asocia los documentos PDF adjuntos en nuestra base de datos PostgreSQL.</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-cyan-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">2. Despacho Asíncrono</h3>
                    <p class="text-gray-600">Aprobación mediante Dashboard corporativo. El motor de colas (Queues) procesa miles de correos en segundo plano sin saturar la memoria RAM del servidor, garantizando alta disponibilidad.</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-teal-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">3. Auditoría y Trazabilidad</h3>
                    <p class="text-gray-600">Seguimiento en tiempo real. Intercepción de Webhooks para registrar la fecha y hora exacta de apertura por cada RUT. Exportación nativa a CSV para rendición comercial limpia.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 py-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} NexoHost SpA. Plataforma desarrollada para licitaciones públicas.</p>
            <p>Arquitectura nativa en Google Cloud.</p>
        </div>
    </footer>

</body>
</html>