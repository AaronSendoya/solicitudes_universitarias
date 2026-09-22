<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard y Reportes Administrativos') }}
            </h2>
            <a href="{{ route('manager.requests.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                Ir a Bandeja de Solicitudes &rarr;
            </a>
        </div>
    </x-slot>

    <!-- Importar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Sección Superior: KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Solicitudes -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Histórico</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $kpis['total'] }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-500 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>

                <!-- Activas / Pendientes -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">En Curso (Activas)</p>
                        <h4 class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $kpis['active'] }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Resueltas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Resueltas / Cerradas</p>
                        <h4 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $kpis['resolved'] }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Emergencias Críticas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Emergencias Críticas</p>
                        <h4 class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $kpis['critical'] }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 flex items-center justify-center animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Sección de Gráficos (Grid Responsivo) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Gráfico de Estados (Doughnut) -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Distribución por Estado</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Rendimiento de Técnicos (Bar) -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Rendimiento por Técnico (Resueltos)</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="techChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Categorías (Bar Horizontal) -->
                <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Distribución Histórica por Categorías</h3>
                    <div class="relative h-72 w-full">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Inicialización de Gráficos con Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Paleta de colores TailwindCSS (Adaptación)
            const colors = {
                gray: '#9ca3af',
                blue: '#3b82f6',
                amber: '#f59e0b',
                emerald: '#10b981',
                slate: '#475569',
                red: '#ef4444',
                indigo: '#6366f1',
                purple: '#8b5cf6',
                pink: '#ec4899',
            };

            // Inyección de Datos desde Blade
            const statusData = @json($statusDistribution);
            const categoryData = @json($categoryDistribution);
            const techData = @json($technicianPerformance);

            // 1. Gráfico de Estados (Doughnut)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            const statusLabels = Object.keys(statusData);
            const statusValues = Object.values(statusData);
            
            // Asignación de colores basada en el estado
            const statusColors = statusLabels.map(label => {
                if(label === 'Pendiente') return colors.gray;
                if(label === 'Asignado') return colors.blue;
                if(label === 'En Proceso') return colors.amber;
                if(label === 'Resuelto') return colors.emerald;
                if(label === 'Cerrado') return colors.slate;
                return colors.indigo;
            });

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderWidth: 1,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });

            // 2. Gráfico de Rendimiento de Técnicos (Barra Vertical)
            const ctxTech = document.getElementById('techChart').getContext('2d');
            new Chart(ctxTech, {
                type: 'bar',
                data: {
                    labels: Object.keys(techData),
                    datasets: [{
                        label: 'Solicitudes Resueltas',
                        data: Object.values(techData),
                        backgroundColor: colors.indigo + '80', // Con transparencia
                        borderColor: colors.indigo,
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // 3. Gráfico de Categorías (Barra Horizontal)
            const ctxCat = document.getElementById('categoryChart').getContext('2d');
            
            // Generar colores dinámicos para las categorías
            const catColors = [colors.blue, colors.emerald, colors.amber, colors.purple, colors.pink, colors.red, colors.indigo];
            const bgCatColors = catColors.map(c => c + '80');

            new Chart(ctxCat, {
                type: 'bar',
                data: {
                    labels: Object.keys(categoryData),
                    datasets: [{
                        label: 'Total Solicitudes',
                        data: Object.values(categoryData),
                        backgroundColor: bgCatColors,
                        borderColor: catColors,
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y', // Hace el gráfico horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
