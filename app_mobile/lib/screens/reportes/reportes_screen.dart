import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';

import '../../models/reporte_gestion.dart';
import '../../services/reporte_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/error_retry.dart';

class ReportesScreen extends StatefulWidget {
  const ReportesScreen({super.key});

  @override
  State<ReportesScreen> createState() => _ReportesScreenState();
}

class _ReportesScreenState extends State<ReportesScreen> {
  final _service = ReporteService();
  late Future<ReporteGestion> _futuro;
  DateTime? _desde;
  DateTime? _hasta;

  @override
  void initState() {
    super.initState();
    _futuro = _cargar();
  }

  Future<ReporteGestion> _cargar() {
    return _service.gestion(fechaDesde: _desde, fechaHasta: _hasta);
  }

  Future<void> _elegirRango() async {
    final rango = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 1)),
      initialDateRange: _desde != null && _hasta != null
          ? DateTimeRange(start: _desde!, end: _hasta!)
          : null,
    );
    if (rango != null) {
      setState(() {
        _desde = rango.start;
        _hasta = rango.end;
        _futuro = _cargar();
      });
    }
  }

  void _limpiarFiltro() {
    setState(() {
      _desde = null;
      _hasta = null;
      _futuro = _cargar();
    });
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        setState(() {
          _futuro = _cargar();
        });
        await _futuro;
      },
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _elegirRango,
                  icon: const Icon(Icons.date_range, size: 18),
                  label: Text(
                    _desde == null ? 'Filtrar por fecha' : '${_fmt(_desde!)} – ${_fmt(_hasta!)}',
                  ),
                ),
              ),
              if (_desde != null) IconButton(onPressed: _limpiarFiltro, icon: const Icon(Icons.clear)),
            ],
          ),
          const SizedBox(height: 16),
          FutureBuilder<ReporteGestion>(
            future: _futuro,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Padding(
                  padding: EdgeInsets.only(top: 60),
                  child: Center(child: CircularProgressIndicator()),
                );
              }
              if (snapshot.hasError) {
                return ErrorRetry(
                  error: snapshot.error!,
                  onRetry: () => setState(() {
                    _futuro = _cargar();
                  }),
                );
              }
              final reporte = snapshot.data!;
              return Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: _tarjetaMetrica(
                          'Total de solicitudes',
                          '${reporte.totalSolicitudes}',
                          Icons.assignment_outlined,
                          AppColors.primary,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _tarjetaMetrica(
                          'Tiempo promedio\nde resolución',
                          reporte.tiempoPromedioResolucionHoras != null
                              ? '${reporte.tiempoPromedioResolucionHoras!.toStringAsFixed(1)} h'
                              : '—',
                          Icons.timer_outlined,
                          AppColors.accent,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  _tarjeta(
                    titulo: 'Distribución por estado',
                    child: _DonutEstado(items: reporte.porEstado, total: reporte.totalSolicitudes),
                  ),
                  const SizedBox(height: 16),
                  _tarjeta(
                    titulo: 'Solicitudes por tipo',
                    child: _BarrasSimples(items: reporte.porTipo, color: AppColors.accent),
                  ),
                  const SizedBox(height: 16),
                  _tarjeta(
                    titulo: 'Solicitudes por prioridad',
                    child: _BarrasSimples(
                      items: reporte.porPrioridad,
                      colorPorNombre: (n) => switch (n) {
                        'Alta' => AppColors.prioridadAlta,
                        'Media' => AppColors.prioridadMedia,
                        'Baja' => AppColors.prioridadBaja,
                        _ => Colors.grey,
                      },
                    ),
                  ),
                  const SizedBox(height: 16),
                  _tarjeta(titulo: 'Carga por técnico', child: _RankingTecnicos(items: reporte.porTecnico)),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  String _fmt(DateTime d) => '${d.day}/${d.month}/${d.year}';

  Widget _tarjetaMetrica(String titulo, String valor, IconData icono, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: color.withValues(alpha: 0.1), shape: BoxShape.circle),
            child: Icon(icono, color: color, size: 20),
          ),
          const SizedBox(height: 10),
          Text(valor, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          Text(titulo, style: TextStyle(color: Colors.grey.shade600, fontSize: 11.5)),
        ],
      ),
    );
  }

  Widget _tarjeta({required String titulo, required Widget child}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(titulo, style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }
}

class _DonutEstado extends StatelessWidget {
  final List<ConteoItem> items;
  final int total;

  const _DonutEstado({required this.items, required this.total});

  Color _color(String nombre) => switch (nombre) {
    'Abierta' => AppColors.abierta,
    'En Proceso' => AppColors.enProceso,
    'Pendiente de Revisión' => AppColors.pendiente,
    'Cerrada' => AppColors.cerrada,
    _ => Colors.grey,
  };

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty || total == 0) {
      return const Padding(
        padding: EdgeInsets.symmetric(vertical: 12),
        child: Text('Sin datos todavía.'),
      );
    }

    return Row(
      children: [
        SizedBox(
          height: 130,
          width: 130,
          child: Stack(
            alignment: Alignment.center,
            children: [
              PieChart(
                PieChartData(
                  sectionsSpace: 3,
                  centerSpaceRadius: 38,
                  sections: [
                    for (final item in items)
                      PieChartSectionData(
                        value: item.total.toDouble(),
                        color: _color(item.nombre),
                        radius: 24,
                        showTitle: false,
                      ),
                  ],
                ),
              ),
              Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text('$total', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
                  Text('total', style: TextStyle(fontSize: 11, color: Colors.grey.shade500)),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(width: 20),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              for (final item in items)
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 4),
                  child: Row(
                    children: [
                      Container(
                        width: 10,
                        height: 10,
                        decoration: BoxDecoration(color: _color(item.nombre), shape: BoxShape.circle),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(item.nombre, style: const TextStyle(fontSize: 12.5)),
                      ),
                      Text(
                        '${item.total}',
                        style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
            ],
          ),
        ),
      ],
    );
  }
}

class _BarrasSimples extends StatelessWidget {
  final List<ConteoItem> items;
  final Color? color;
  final Color Function(String nombre)? colorPorNombre;

  const _BarrasSimples({required this.items, this.color, this.colorPorNombre});

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) {
      return const Text('Sin datos todavía.');
    }
    final maximo = items.map((e) => e.total).reduce((a, b) => a > b ? a : b);

    return SizedBox(
      height: 160,
      child: BarChart(
        BarChartData(
          maxY: (maximo + 1).toDouble(),
          gridData: const FlGridData(show: false),
          borderData: FlBorderData(show: false),
          titlesData: FlTitlesData(
            leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                getTitlesWidget: (value, meta) {
                  final i = value.toInt();
                  if (i < 0 || i >= items.length) return const SizedBox.shrink();
                  return Padding(
                    padding: const EdgeInsets.only(top: 6),
                    child: SizedBox(
                      width: 64,
                      child: Text(
                        items[i].nombre,
                        style: const TextStyle(fontSize: 9.5, height: 1.15),
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  );
                },
                reservedSize: 40,
              ),
            ),
          ),
          barGroups: [
            for (var i = 0; i < items.length; i++)
              BarChartGroupData(
                x: i,
                barRods: [
                  BarChartRodData(
                    toY: items[i].total.toDouble(),
                    color: colorPorNombre?.call(items[i].nombre) ?? color ?? AppColors.primary,
                    width: 26,
                    borderRadius: BorderRadius.circular(6),
                    backDrawRodData: BackgroundBarChartRodData(
                      show: true,
                      toY: (maximo + 1).toDouble(),
                      color: Colors.grey.shade100,
                    ),
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}

class _RankingTecnicos extends StatelessWidget {
  final List<ConteoItem> items;

  const _RankingTecnicos({required this.items});

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) {
      return const Text('No hay solicitudes asignadas todavía.');
    }
    final ordenados = [...items]..sort((a, b) => b.total.compareTo(a.total));

    return Column(
      children: [
        for (final item in ordenados)
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 6),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 15,
                  backgroundColor: AppColors.primary.withValues(alpha: 0.12),
                  child: Text(
                    item.nombre.isNotEmpty ? item.nombre[0].toUpperCase() : '?',
                    style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700, fontSize: 12),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(child: Text(item.nombre, style: const TextStyle(fontSize: 13))),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    '${item.total}',
                    style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700, fontSize: 12),
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }
}
