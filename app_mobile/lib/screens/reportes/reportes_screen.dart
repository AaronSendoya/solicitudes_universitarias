import 'package:flutter/material.dart';

import '../../models/reporte_gestion.dart';
import '../../services/reporte_service.dart';
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
                  icon: const Icon(Icons.date_range),
                  label: Text(
                    _desde == null
                        ? 'Filtrar por fecha'
                        : '${_fmt(_desde!)} – ${_fmt(_hasta!)}',
                  ),
                ),
              ),
              if (_desde != null)
                IconButton(onPressed: _limpiarFiltro, icon: const Icon(Icons.clear)),
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
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _tarjetaMetrica(
                          'Tiempo promedio de resolución',
                          reporte.tiempoPromedioResolucionHoras != null
                              ? '${reporte.tiempoPromedioResolucionHoras!.toStringAsFixed(1)} h'
                              : '—',
                          Icons.timer_outlined,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  _seccionConteo('Por estado', reporte.porEstado, Colors.blue),
                  const SizedBox(height: 20),
                  _seccionConteo('Por tipo', reporte.porTipo, Colors.teal),
                  const SizedBox(height: 20),
                  _seccionConteo('Por prioridad', reporte.porPrioridad, Colors.deepOrange),
                  const SizedBox(height: 20),
                  _seccionConteo('Por técnico', reporte.porTecnico, Colors.indigo),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  String _fmt(DateTime d) => '${d.day}/${d.month}/${d.year}';

  Widget _tarjetaMetrica(String titulo, String valor, IconData icono) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icono, color: Theme.of(context).colorScheme.primary),
            const SizedBox(height: 8),
            Text(valor, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            Text(titulo, style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _seccionConteo(String titulo, List<ConteoItem> items, Color color) {
    if (items.isEmpty) {
      return Text(titulo, style: const TextStyle(fontWeight: FontWeight.bold));
    }
    final maximo = items.map((e) => e.total).reduce((a, b) => a > b ? a : b);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(titulo, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        const SizedBox(height: 10),
        for (final item in items)
          Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(item.nombre, style: const TextStyle(fontSize: 13)),
                    Text('${item.total}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ),
                const SizedBox(height: 4),
                ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: maximo == 0 ? 0 : item.total / maximo,
                    minHeight: 8,
                    backgroundColor: color.withValues(alpha: 0.12),
                    valueColor: AlwaysStoppedAnimation(color),
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }
}
