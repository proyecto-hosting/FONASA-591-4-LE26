<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resultado de Búsqueda') }}
            </h2>
            <a href="{{ route('reports.traceability') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">&larr; Volver al Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('reports.search') }}" class="flex gap-4">
                        <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Ingrese RUT (ej: 11111111-1) o Email..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            Auditar Envío
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($query))
                @if($results->count() > 0)
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <p class="text-gray-700 text-sm mb-3 sm:mb-0">
                        Se encontraron <span class="font-bold text-indigo-600">{{ $results->count() }}</span> envíos históricos para: <b class="text-gray-900">{{ $query }}</b>
                    </p>
                    
                    <a href="{{ route('reports.search.export', ['query' => $query]) }}" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded inline-flex items-center justify-center transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exportar Historial del RUT
                    </a>
                </div>
                    @foreach($results as $recipient)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Detalle del Destinatario</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div>
                                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">RUT</p>
                                        <p class="font-medium text-gray-900 mt-1">{{ $recipient->rut }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Email</p>
                                        <p class="font-medium text-gray-900 mt-1">{{ $recipient->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Campaña Asociada</p>
                                        <p class="font-medium text-gray-900 mt-1">{{ $recipient->campaign->name ?? 'Desconocida' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Estado Actual</p>
                                        <div class="mt-1">
                                            @if($recipient->status == 'leído')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 uppercase">Leído</span>
                                            @elseif($recipient->status == 'rebotado')
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800 uppercase">Rebotado</span>
                                                @if($recipient->bounce_reason)
                                                    <p class="text-xs text-red-600 mt-2 font-medium" title="Log técnico: {{ $recipient->bounce_reason }}">
                                                        Motivo: {{ $recipient->friendly_bounce_reason }}
                                                    </p>                                                
                                                @endif
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $recipient->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-6 border-gray-200">

                                <h3 class="text-lg font-bold text-gray-800 mb-4">Línea de Tiempo</h3>
                                <ul class="space-y-3 relative border-l-2 border-gray-200 ml-3 pl-4">
                                    <li class="text-sm text-gray-600 relative">
                                        <div class="absolute -left-[21px] top-1 w-3 h-3 bg-gray-300 rounded-full border-2 border-white"></div>
                                        <span class="font-bold text-gray-800">Encolado el:</span> {{ $recipient->created_at->format('d/m/Y H:i:s') }}
                                    </li>
                                    @if($recipient->opened_at)
                                    <li class="text-sm text-gray-600 relative">
                                        <div class="absolute -left-[21px] top-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                        <span class="font-bold text-green-600">Apertura confirmada el:</span> {{ \Carbon\Carbon::parse($recipient->opened_at)->format('d/m/Y H:i:s') }}
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    No se encontraron registros de envíos para el RUT o Email ingresado: <span class="font-bold">{{ $query }}</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>