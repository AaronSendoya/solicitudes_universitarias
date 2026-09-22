<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.requests.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de la Solicitud') }} #{{ str_pad($requestModel->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-3">
            
            <!-- Columna Principal: Detalles -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tarjeta de Información -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $requestModel->title }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    {{ $requestModel->category->name }}
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $requestModel->created_at->format('d/m/Y h:i A') }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold shadow-sm
                            @if($requestModel->status === 'Pendiente') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @elseif($requestModel->status === 'Asignado') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                            @elseif($requestModel->status === 'En Proceso') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                            @elseif($requestModel->status === 'Resuelto') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                            @elseif($requestModel->status === 'Cerrado') bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300
                            @endif
                        ">
                            {{ $requestModel->status }}
                        </span>
                    </div>
                    
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Descripción del problema</h4>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {{ rtrim($requestModel->description) }}
                        </div>
                    </div>

                    <!-- Archivos Adjuntos -->
                    @if($requestModel->evidences->isNotEmpty())
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Archivos Adjuntos ({{ $requestModel->evidences->count() }})</h4>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($requestModel->evidences as $evidence)
                                <li class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <svg class="flex-shrink-0 w-6 h-6 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">Adjunto #{{ $loop->iteration }}</span>
                                    </div>
                                    <a href="{{ Storage::url($evidence->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm flex-shrink-0">Ver / Bajar</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Sección de Comentarios (Placeholder para futura implementación) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Comentarios</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Aún no hay comentarios en esta solicitud.</p>
                        <!-- Aquí irá el input de comentarios en el futuro -->
                        <div class="mt-4 flex gap-3 opacity-50 pointer-events-none">
                            <input type="text" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm sm:text-sm" placeholder="Escribe un comentario...">
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Secundaria: Sidebar de Trazabilidad -->
            <div class="space-y-6">
                <!-- Tarjeta de Asignación -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Técnico Asignado</h4>
                        @if($requestModel->assignedTo)
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold text-lg shadow-sm">
                                    {{ substr($requestModel->assignedTo->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $requestModel->assignedTo->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $requestModel->assignedTo->email }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-dashed border-gray-200 dark:border-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-sm">Aún no se ha asignado un técnico.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tarjeta de Trazabilidad (Timeline) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6">Historial (Trazabilidad)</h4>
                        
                        <div class="relative border-l border-gray-200 dark:border-gray-700 ml-3 space-y-6">
                            <!-- Ejemplo de paso estático (Se dinámizará más adelante con ActionHistory) -->
                            <div class="relative pl-6">
                                <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-1.5 top-1.5 ring-4 ring-white dark:ring-gray-800"></div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Solicitud Creada</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $requestModel->created_at->diffForHumans() }}</p>
                            </div>
                            <!-- ... -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
