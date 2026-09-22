<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis Asignaciones - Técnico') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-lg shadow-sm" role="alert">
                    <strong class="font-medium">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">ID / Fecha</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Estudiante</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Categoría / Título</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Prioridad</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($requests as $req)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $req->priority === 'Crítica' && $req->status !== 'Cerrado' && $req->status !== 'Resuelto' ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">#{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $req->created_at->format('d/m/Y h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $req->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $req->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 mb-1">
                                            {{ $req->category->name }}
                                        </span>
                                        <div class="font-medium text-gray-900 dark:text-white truncate max-w-xs" title="{{ $req->title }}">
                                            {{ $req->title }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                            @if($req->priority === 'Baja') border-gray-200 text-gray-600 dark:border-gray-600 dark:text-gray-400
                                            @elseif($req->priority === 'Media') border-blue-200 text-blue-600 dark:border-blue-800 dark:text-blue-400
                                            @elseif($req->priority === 'Alta') border-amber-200 text-amber-600 bg-amber-50 dark:border-amber-800 dark:text-amber-400 dark:bg-amber-900/20
                                            @elseif($req->priority === 'Crítica') border-red-300 text-red-700 bg-red-50 font-bold dark:border-red-800 dark:text-red-400 dark:bg-red-900/20
                                            @endif
                                        ">
                                            @if($req->priority === 'Crítica')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                            @endif
                                            {{ $req->priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($req->status === 'Pendiente') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @elseif($req->status === 'Asignado') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                            @elseif($req->status === 'En Proceso') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                            @elseif($req->status === 'Resuelto') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($req->status === 'Cerrado') bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300
                                            @endif
                                        ">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('technician.requests.show', $req->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Atender
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <p class="text-lg font-medium">Bandeja Vacía</p>
                                        <p class="mt-1 text-sm">No tienes solicitudes asignadas actualmente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
