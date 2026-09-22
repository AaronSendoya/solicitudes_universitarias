import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../models/catalogo_item.dart';
import '../../models/solicitud.dart';
import '../../services/catalogo_service.dart';
import '../../services/solicitud_service.dart';
import '../../widgets/error_retry.dart';
import '../../widgets/estado_chip.dart';
import '../../widgets/estado_vacio.dart';
import '../../widgets/prioridad_chip.dart';
import 'solicitud_detail_screen.dart';

class SolicitudesListScreen extends StatefulWidget {
  final String titulo;

  const SolicitudesListScreen({super.key, required this.titulo});

  @override
  State<SolicitudesListScreen> createState() => _SolicitudesListScreenState();
}

class _SolicitudesListScreenState extends State<SolicitudesListScreen> {
  final _solicitudService = SolicitudService();
  final _catalogoService = CatalogoService();

  late Future<List<Solicitud>> _futuro;
  List<CatalogoItem> _estados = [];
  int? _filtroEstadoId;

  @override
  void initState() {
    super.initState();
    _futuro = _cargar();
    _catalogoService.estados().then((e) {
      if (mounted) setState(() => _estados = e);
    });
  }

  Future<List<Solicitud>> _cargar() {
    return _solicitudService.listar(estadoId: _filtroEstadoId);
  }

  Future<void> _recargar() async {
    setState(() {
      _futuro = _cargar();
    });
    await _futuro;
  }

  void _abrirDetalle(Solicitud s) async {
    final cambiado = await Navigator.of(context).push<bool>(
      MaterialPageRoute(builder: (_) => SolicitudDetailScreen(solicitudId: s.id)),
    );
    if (cambiado == true) _recargar();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        if (_estados.isNotEmpty) _buildFiltros(),
        Expanded(
          child: RefreshIndicator(
            onRefresh: _recargar,
            child: FutureBuilder<List<Solicitud>>(
              future: _futuro,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return ErrorRetry(error: snapshot.error!, onRetry: _recargar);
                }
                final solicitudes = snapshot.data ?? [];
                if (solicitudes.isEmpty) {
                  return ListView(
                    children: const [
                      SizedBox(height: 80),
                      EstadoVacio(
                        mensaje: 'No hay solicitudes para mostrar.',
                        icono: Icons.assignment_outlined,
                      ),
                    ],
                  );
                }
                return ListView.separated(
                  padding: const EdgeInsets.all(12),
                  itemCount: solicitudes.length,
                  separatorBuilder: (_, _) => const SizedBox(height: 8),
                  itemBuilder: (context, i) => _SolicitudCard(
                    solicitud: solicitudes[i],
                    onTap: () => _abrirDetalle(solicitudes[i]),
                  ),
                );
              },
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildFiltros() {
    return SizedBox(
      height: 48,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        children: [
          _filtroChip(null, 'Todas'),
          for (final estado in _estados) _filtroChip(estado.id, estado.nombre),
        ],
      ),
    );
  }

  Widget _filtroChip(int? estadoId, String label) {
    final seleccionado = _filtroEstadoId == estadoId;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: ChoiceChip(
        label: Text(label),
        selected: seleccionado,
        onSelected: (_) {
          setState(() {
            _filtroEstadoId = estadoId;
            _futuro = _cargar();
          });
        },
      ),
    );
  }
}

class _SolicitudCard extends StatelessWidget {
  final Solicitud solicitud;
  final VoidCallback onTap;

  const _SolicitudCard({required this.solicitud, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final fecha = solicitud.fechaCreacion;
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      solicitud.titulo,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  if (solicitud.estado != null) EstadoChip(estado: solicitud.estado!),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                solicitud.descripcion,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(color: Colors.grey.shade700),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Icon(Icons.category_outlined, size: 14, color: Colors.grey.shade600),
                  const SizedBox(width: 4),
                  Text(
                    solicitud.tipo ?? '',
                    style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                  ),
                  const SizedBox(width: 12),
                  if (solicitud.prioridad != null) PrioridadChip(prioridad: solicitud.prioridad!),
                  const Spacer(),
                  if (fecha != null)
                    Text(
                      DateFormat('dd/MM/yyyy').format(fecha),
                      style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
                    ),
                ],
              ),
              if (solicitud.responsableActual != null) ...[
                const SizedBox(height: 6),
                Row(
                  children: [
                    Icon(Icons.engineering_outlined, size: 14, color: Colors.grey.shade600),
                    const SizedBox(width: 4),
                    Text(
                      solicitud.responsableActual!.nombreCompleto,
                      style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
