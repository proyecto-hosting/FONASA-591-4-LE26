<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Control FONASA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Campañas Activas</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_campaigns'] }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Destinatarios</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_emails'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Correos Enviados</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['sent_emails'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Rebotes / Errores</div>
                    <div class="text-3xl font-bold text-red-600 mt-2">{{ $stats['bounced_emails'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Bienvenido al Sistema de Despacho Masivo</h3>
                    <p class="text-gray-600">El motor asíncrono está en línea y conectado a Google Cloud SQL. Navegue a la sección de campañas para gestionar los envíos pendientes.</p>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>