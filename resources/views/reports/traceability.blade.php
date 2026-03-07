<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoría y Trazabilidad') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('reports.traceability') }}" class="flex gap-4">
                        <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Ingrese RUT (ej: 11111111-1) o Email..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            Auditar Envío
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($query))
                @if($recipient)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Detalle del Destinatario</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500">RUT</p>
                                    <p class="font-medium text-gray-900">{{ $recipient->rut }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium text-gray-900">{{ $recipient->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Campaña Asociada</p>
                                    <p class="font-medium text-gray-900">{{ $recipient->campaign->name ?? 'Desconocida' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Estado Actual</p>
                                    @if($recipient->status == 'leído')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">Leído</span>
                                    @elseif($recipient->status == 'rebotado')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 uppercase">Rebotado</span>
                                        <p class="text-xs text-red-600 mt-1">Motivo: {{ $recipient->bounce_reason }}</p>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $recipient->status }}</span>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-6 border-gray-200">

                            <h3 class="text-lg font-bold text-gray-800 mb-4">Línea de Tiempo</h3>
                            <ul class="space-y-3">
                                <li class="text-sm text-gray-600">
                                    <span class="font-bold text-gray-800">Encolado el:</span> {{ $recipient->created_at->format('d/m/Y H:i:s') }}
                                </li>
                                @if($recipient->opened_at)
                                <li class="text-sm text-gray-600">
                                    <span class="font-bold text-green-600">Apertura confirmada el:</span> {{ \Carbon\Carbon::parse($recipient->opened_at)->format('d/m/Y H:i:s') }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    No se encontraron registros de envíos para el RUT o Email ingresado: <b>{{ $query }}</b>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>