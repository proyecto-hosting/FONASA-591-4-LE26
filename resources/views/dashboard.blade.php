<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Control Gubernamental') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-cyan-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Campañas Activas</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_campaigns'] ?? 0 }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-600">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Destinatarios</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_emails'] ?? 0 }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-teal-600">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Correos Enviados</div>
                    <div class="text-3xl font-bold text-teal-700 mt-2">{{ $stats['sent_emails'] ?? 0 }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Rebotes / Errores</div>
                    <div class="text-3xl font-bold text-red-600 mt-2">{{ $stats['bounced_emails'] ?? 0 }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bienvenido al Sistema de Despacho Masivo</h3>
                    <p class="text-gray-600 mb-8">El motor asíncrono está en línea y conectado a PostgreSQL. Desde aquí puede forzar la ingesta de nuevas remesas para esta demostración.</p>
                    
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <strong>Error:&nbsp;</strong> {{ session('error') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-teal-50 border-l-4 border-teal-500 text-teal-800 rounded shadow-sm">
                            <div class="flex items-center mb-3">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <strong>{{ session('status') }}</strong>
                            </div>
                            
                            @if(session('preview'))
                                <div class="mt-3 bg-white border border-teal-100 rounded p-3 text-sm font-mono text-gray-600 space-y-1">
                                    @foreach(session('preview') as $registro)
                                        <div class="flex items-center gap-2">
                                            <span class="text-teal-500">✓</span> {{ $registro }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('sftp.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 text-white font-bold py-3 px-6 rounded shadow-md transition-all flex items-center transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Ejecutar Sincronización SFTP (Demo)
                        </button>
                    </form>

                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>