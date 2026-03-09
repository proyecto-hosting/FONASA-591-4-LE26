<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoría y Trazabilidad de Campañas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    
                    <form method="GET" action="{{ route('reports.search') }}" class="flex gap-4 w-full md:w-auto flex-1">
                        <input type="text" name="query" placeholder="Ingrese RUT (ej: 11111111-1) o Email..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition-colors">
                            Auditar Envío
                        </button>
                    </form>

                    <a href="{{ route('reports.export') }}" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded inline-flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar a Excel
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                                <th class="px-5 py-3 border-b-2 text-left">Campaña / Fecha</th>
                                <th class="px-5 py-3 border-b-2 text-center">Enviados</th>
                                <th class="px-5 py-3 border-b-2 text-center">Recibidos</th>
                                <th class="px-5 py-3 border-b-2 text-center text-green-600">Leídos</th>
                                <th class="px-5 py-3 border-b-2 text-center text-red-600">Rebotados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports ?? [] as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 border-b text-sm">
                                    <p class="font-bold text-gray-900">{{ $report->campaign_name }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->extraction_date)->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-4 border-b text-center text-sm font-semibold">{{ $report->total_sent }}</td>
                                <td class="px-5 py-4 border-b text-center text-sm font-semibold text-blue-600">{{ $report->total_received }}</td>
                                <td class="px-5 py-4 border-b text-center text-sm font-bold text-green-600">{{ $report->total_opened }}</td>
                                <td class="px-5 py-4 border-b text-center text-sm font-bold text-red-600">{{ $report->total_errors }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">No hay campañas registradas aún.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>