import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../core/api_exception.dart';
import '../../models/solicitud.dart';
import '../../providers/auth_provider.dart';
import '../../services/solicitud_service.dart';
import '../../widgets/error_retry.dart';
import '../../widgets/estado_chip.dart';
import '../../widgets/prioridad_chip.dart';
import 'editar_solicitud_screen.dart';
import 'widgets/adjuntos_tab.dart';
import 'widgets/asignar_dialog.dart';
import 'widgets/cambiar_estado_dialog.dart';
import 'widgets/clasificar_dialog.dart';
import 'widgets/comentarios_tab.dart';
import 'widgets/historial_tab.dart';

class SolicitudDetailScreen extends StatefulWidget {
  final int solicitudId;

  const SolicitudDetailScreen({super.key, required this.solicitudId});

  @override
  State<SolicitudDetailScreen> createState() => _SolicitudDetailScreenState();
}

class _SolicitudDetailScreenState extends State<SolicitudDetailScreen> {
  final _service = SolicitudService();
  late Future<Solicitud> _futuro;
  bool _seModificoAlgo = false;

  @override
  void initState() {
    super.initState();
    _futuro = _service.obtener(widget.solicitudId);
  }

  Future<void> _recargar() async {
    setState(() {
      _futuro = _service.obtener(widget.solicitudId);
      _seModificoAlgo = true;
    });
    await _futuro;
  }

  void _mostrarError(Object e) {
    final mensaje = e is ApiException ? e.friendlyMessage : 'Ocurrió un error inesperado.';
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(mensaje)));
  }

  Future<void> _eliminar(Solicitud solicitud) async {
    final confirmar = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Eliminar solicitud'),
        content: Text('¿Seguro que deseas eliminar "${solicitud.titulo}"?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancelar')),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Eliminar'),
          ),
        ],
      ),
    );
    if (confirmar != true) return;

    try {
      await _service.eliminar(solicitud.id);
      if (mounted) Navigator.of(context).pop(true);
    } catch (e) {
      if (mounted) _mostrarError(e);
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        if (!didPop) Navigator.of(context).pop(_seModificoAlgo);
      },
      child: DefaultTabController(
        length: 4,
        child: Scaffold(
          appBar: AppBar(
            title: const Text('Detalle de solicitud'),
            bottom: const TabBar(
              isScrollable: true,
              tabs: [
                Tab(text: 'Detalle'),
                Tab(text: 'Comentarios'),
                Tab(text: 'Evidencia'),
                Tab(text: 'Historial'),
              ],
            ),
          ),
          body: FutureBuilder<Solicitud>(
            future: _futuro,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              }
              if (snapshot.hasError) {
                return ErrorRetry(error: snapshot.error!, onRetry: _recargar);
              }
              final solicitud = snapshot.data!;
              return TabBarView(
                children: [
                  _DetalleTab(
                    solicitud: solicitud,
                    onEliminar: () => _eliminar(solicitud),
                    onRecargar: _recargar,
                    onError: _mostrarError,
                  ),
                  ComentariosTab(solicitud: solicitud, onCambio: _recargar, onError: _mostrarError),
                  AdjuntosTab(solicitud: solicitud, onCambio: _recargar, onError: _mostrarError),
                  HistorialTab(solicitud: solicitud),
                ],
              );
            },
          ),
        ),
      ),
    );
  }
}

class _DetalleTab extends StatelessWidget {
  final Solicitud solicitud;
  final VoidCallback onEliminar;
  final Future<void> Function() onRecargar;
  final void Function(Object) onError;

  const _DetalleTab({
    required this.solicitud,
    required this.onEliminar,
    required this.onRecargar,
    required this.onError,
  });

  @override
  Widget build(BuildContext context) {
    final usuario = context.watch<AuthProvider>().usuario!;
    final esDueno = usuario.esEstudiante && solicitud.solicitante?.id == usuario.id;
    final esResponsable = usuario.esTecnico && solicitud.responsableActual?.id == usuario.id;
    final puedeEditar = esDueno && solicitud.estaAbierta;
    final formatoFecha = DateFormat('dd/MM/yyyy HH:mm');

    return RefreshIndicator(
      onRefresh: onRecargar,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  solicitud.titulo,
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
              ),
              if (solicitud.estado != null) EstadoChip(estado: solicitud.estado!),
            ],
          ),
          const SizedBox(height: 12),
          Text(solicitud.descripcion),
          const SizedBox(height: 20),
          _fila('Tipo', solicitud.tipo ?? '-'),
          _filaWidget(
            'Prioridad',
            solicitud.prioridad != null
                ? PrioridadChip(prioridad: solicitud.prioridad!)
                : const Text('-'),
          ),
          _fila('Ubicación', solicitud.ubicacion ?? '-'),
          if (solicitud.recurso != null) _fila('Recurso', solicitud.recurso!),
          _fila('Solicitante', solicitud.solicitante?.nombreCompleto ?? '-'),
          _fila('Responsable actual', solicitud.responsableActual?.nombreCompleto ?? 'Sin asignar'),
          if (solicitud.fechaCreacion != null)
            _fila('Creada', formatoFecha.format(solicitud.fechaCreacion!)),
          if (solicitud.fechaCierre != null)
            _fila('Cerrada', formatoFecha.format(solicitud.fechaCierre!)),
          const SizedBox(height: 24),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              if (puedeEditar)
                OutlinedButton.icon(
                  icon: const Icon(Icons.edit_outlined),
                  label: const Text('Editar'),
                  onPressed: () async {
                    final ok = await Navigator.of(context).push<bool>(
                      MaterialPageRoute(builder: (_) => EditarSolicitudScreen(solicitud: solicitud)),
                    );
                    if (ok == true) onRecargar();
                  },
                ),
              if (puedeEditar || usuario.esAdministrativo)
                OutlinedButton.icon(
                  icon: const Icon(Icons.delete_outline, color: Colors.red),
                  label: const Text('Eliminar', style: TextStyle(color: Colors.red)),
                  onPressed: onEliminar,
                ),
              if (usuario.esAdministrativo) ...[
                FilledButton.tonalIcon(
                  icon: const Icon(Icons.low_priority),
                  label: const Text('Clasificar / priorizar'),
                  onPressed: () async {
                    final ok = await mostrarDialogoClasificar(context, solicitud);
                    if (ok == true) onRecargar();
                  },
                ),
                FilledButton.tonalIcon(
                  icon: const Icon(Icons.person_add_alt),
                  label: const Text('Asignar responsable'),
                  onPressed: () async {
                    final ok = await mostrarDialogoAsignar(context, solicitud);
                    if (ok == true) onRecargar();
                  },
                ),
              ],
              if (esResponsable || usuario.esAdministrativo)
                FilledButton.icon(
                  icon: const Icon(Icons.sync_alt),
                  label: const Text('Cambiar estado'),
                  onPressed: () async {
                    final ok = await mostrarDialogoCambiarEstado(context, solicitud);
                    if (ok == true) onRecargar();
                  },
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _fila(String etiqueta, String valor) => _filaWidget(etiqueta, Text(valor));

  Widget _filaWidget(String etiqueta, Widget valor) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(etiqueta, style: TextStyle(color: Colors.grey.shade600)),
          ),
          Expanded(child: valor),
        ],
      ),
    );
  }
}
