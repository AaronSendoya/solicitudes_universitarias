<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('manager.requests.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Solicitud') }} #{{ str_pad($requestModel->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-3">
            
            <!-- Columna Principal: Detalle, Evidencias y Formulario de Gestión -->
            <div class="lg:col-span-2 space-y-6">
                
                @if (session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <strong class="font-medium">¡Éxito!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg shadow-sm" role="alert">
                        <strong class="font-medium">Error al guardar:</strong>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tarjeta de Información General -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $requestModel->title }}</h3>
                                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="font-medium">{{ $requestModel->user->name }}</span>
                                    <span>({{ $requestModel->user->email }})</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    {{ $requestModel->category->name }} &bull; {{ $requestModel->created_at->format('d/m/Y h:i A') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                            {{ rtrim($requestModel->description) }}
                        </div>
                    </div>

                    <!-- Evidencias -->
                    @if($requestModel->evidences->isNotEmpty())
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Evidencias Adjuntas</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($requestModel->evidences as $evidence)
                                @php
                                    $ext = pathinfo($evidence->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                <a href="{{ Storage::url($evidence->file_path) }}" target="_blank" class="group block border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    @if($isImage)
                                        <div class="h-32 w-full bg-gray-100 dark:bg-gray-700">
                                            <img src="{{ Storage::url($evidence->file_path) }}" alt="Evidencia" class="w-full h-full object-cover group-hover:opacity-75 transition-opacity">
                                        </div>
                                    @else
                                        <div class="h-32 w-full bg-gray-50 dark:bg-gray-700 flex flex-col items-center justify-center text-gray-400 dark:text-gray-300 group-hover:text-blue-500 transition-colors">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span class="text-xs font-semibold uppercase">{{ $ext }}</span>
                                        </div>
                                    @endif
                                    <div class="p-2 bg-white dark:bg-gray-800 text-xs text-center font-medium text-gray-700 dark:text-gray-300 truncate">
                                        Adjunto #{{ $loop->iteration }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Formulario de Gestión -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Panel de Gestión
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('manager.requests.update', $requestModel->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid gap-6 md:grid-cols-3">
                                <!-- Técnico Asignado -->
                                <div>
                                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Técnico Asignado</label>
                                    <select id="assigned_to" name="assigned_to" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Sin asignar --</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}" {{ $requestModel->assigned_to == $tech->id ? 'selected' : '' }}>
                                                {{ $tech->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Prioridad -->
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nivel de Prioridad</label>
                                    <select id="priority" name="priority" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-medium">
                                        <option value="Baja" {{ $requestModel->priority === 'Baja' ? 'selected' : '' }}>Baja</option>
                                        <option value="Media" {{ $requestModel->priority === 'Media' ? 'selected' : '' }}>Media</option>
                                        <option value="Alta" {{ $requestModel->priority === 'Alta' ? 'selected' : '' }}>Alta</option>
                                        <option value="Crítica" {{ $requestModel->priority === 'Crítica' ? 'selected' : '' }} class="text-red-600">Crítica</option>
                                    </select>
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado de la Solicitud</label>
                                    <select id="status" name="status" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-medium">
                                        <option value="Pendiente" {{ $requestModel->status === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="Asignado" {{ $requestModel->status === 'Asignado' ? 'selected' : '' }}>Asignado</option>
                                        <option value="En Proceso" {{ $requestModel->status === 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="Resuelto" {{ $requestModel->status === 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                                        <option value="Cerrado" {{ $requestModel->status === 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg shadow transition-colors duration-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna Secundaria: Trazabilidad -->
            <div class="space-y-6 lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden h-full">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Trazabilidad
                        </h3>
                    </div>
                    
                    <div class="p-6 overflow-y-auto max-h-[600px]">
                        @php
                            // Ordenamos los historiales para mostrarlos del más reciente al más antiguo
                            $histories = $requestModel->actionHistories->sortByDesc('created_at');
                        @endphp

                        @if($histories->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sin modificaciones registradas aún.</p>
                            </div>
                        @else
                            <div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-4 space-y-8">
                                @foreach($histories as $history)
                                    <div class="relative pl-6">
                                        <!-- Dot Indicator -->
                                        @php
                                            $iconColor = 'bg-gray-300 dark:bg-gray-600';
                                            if($history->action_type === 'status_change') $iconColor = 'bg-blue-500';
                                            if($history->action_type === 'priority_change') $iconColor = 'bg-amber-500';
                                            if($history->action_type === 'assignment') $iconColor = 'bg-indigo-500';
                                        @endphp
                                        <div class="absolute w-4 h-4 {{ $iconColor }} rounded-full -left-[9px] top-1 ring-4 ring-white dark:ring-gray-800"></div>
                                        
                                        <!-- Contenido del historial -->
                                        <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $history->user ? $history->user->name : 'Sistema' }}
                                                <span class="ml-auto text-[10px]">{{ $history->created_at->format('d M h:i A') }}</span>
                                            </p>
                                            
                                            <div class="text-sm text-gray-800 dark:text-gray-200 mt-1.5">
                                                @if($history->action_type === 'status_change')
                                                    Cambió el estado a <span class="font-semibold">{{ $history->new_value }}</span>
                                                @elseif($history->action_type === 'priority_change')
                                                    Cambió prioridad a <span class="font-semibold">{{ $history->new_value }}</span>
                                                @elseif($history->action_type === 'assignment')
                                                    Asignó técnico: <span class="font-semibold">{{ $history->new_value }}</span>
                                                @else
                                                    {{ $history->comments ?? 'Actualización de registro' }}
                                                @endif
                                            </div>
                                            @if($history->old_value)
                                            <p class="text-[11px] text-gray-500 mt-1 italic">
                                                Valor anterior: {{ $history->old_value }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Registro original (Creación) -->
                                <div class="relative pl-6">
                                    <div class="absolute w-4 h-4 bg-emerald-500 rounded-full -left-[9px] top-1 ring-4 ring-white dark:ring-gray-800"></div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $requestModel->user->name }}
                                            <span class="ml-auto text-[10px]">{{ $requestModel->created_at->format('d M h:i A') }}</span>
                                        </p>
                                        <div class="text-sm text-gray-800 dark:text-gray-200 mt-1.5 font-medium">
                                            Solicitud Creada
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
