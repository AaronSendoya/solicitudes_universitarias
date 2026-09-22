import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../core/api_exception.dart';
import '../../models/solicitud.dart';
import '../../providers/auth_provider.dart';
import '../../services/solicitud_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/error_retry.dart';
import '../../widgets/estado_chip.dart';
import '../../widgets/prioridad_chip.dart';
import '../../widgets/tipo_icon.dart';
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
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.04),
                  blurRadius: 14,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(iconoParaTipo(solicitud.tipo), color: AppColors.primary),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(solicitud.titulo, style: Theme.of(context).textTheme.headlineSmall),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Text(
                  solicitud.descripcion,
                  style: TextStyle(color: Colors.grey.shade700, height: 1.4),
                ),
                const SizedBox(height: 14),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    if (solicitud.estado != null) EstadoChip(estado: solicitud.estado!, grande: true),
                    if (solicitud.prioridad != null)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: PrioridadChip(prioridad: solicitud.prioridad!),
                      ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 2.6,
            children: [
              _InfoTile(icono: Icons.category_outlined, etiqueta: 'Tipo', valor: solicitud.tipo ?? '-'),
              _InfoTile(
                icono: Icons.place_outlined,
                etiqueta: 'Ubicación',
                valor: solicitud.ubicacion ?? '-',
              ),
              _InfoTile(
                icono: Icons.person_outline,
                etiqueta: 'Solicitante',
                valor: solicitud.solicitante?.nombreCompleto ?? '-',
              ),
              _InfoTile(
                icono: Icons.engineering_outlined,
                etiqueta: 'Responsable',
                valor: solicitud.responsableActual?.nombreCompleto ?? 'Sin asignar',
              ),
              if (solicitud.recurso != null)
                _InfoTile(icono: Icons.inventory_2_outlined, etiqueta: 'Recurso', valor: solicitud.recurso!),
              if (solicitud.fechaCreacion != null)
                _InfoTile(
                  icono: Icons.event_outlined,
                  etiqueta: 'Creada',
                  valor: formatoFecha.format(solicitud.fechaCreacion!),
                ),
              if (solicitud.fechaCierre != null)
                _InfoTile(
                  icono: Icons.event_available_outlined,
                  etiqueta: 'Cerrada',
                  valor: formatoFecha.format(solicitud.fechaCierre!),
                ),
            ],
          ),
          const SizedBox(height: 20),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              if (puedeEditar)
                OutlinedButton.icon(
                  icon: const Icon(Icons.edit_outlined, size: 18),
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
                  icon: const Icon(Icons.delete_outline, color: Colors.red, size: 18),
                  label: const Text('Eliminar', style: TextStyle(color: Colors.red)),
                  onPressed: onEliminar,
                ),
              if (usuario.esAdministrativo) ...[
                FilledButton.tonalIcon(
                  icon: const Icon(Icons.low_priority, size: 18),
                  label: const Text('Clasificar / priorizar'),
                  onPressed: () async {
                    final ok = await mostrarDialogoClasificar(context, solicitud);
                    if (ok == true) onRecargar();
                  },
                ),
                FilledButton.tonalIcon(
                  icon: const Icon(Icons.person_add_alt, size: 18),
                  label: const Text('Asignar responsable'),
                  onPressed: () async {
                    final ok = await mostrarDialogoAsignar(context, solicitud);
                    if (ok == true) onRecargar();
                  },
                ),
              ],
              if (esResponsable || usuario.esAdministrativo)
                FilledButton.icon(
                  icon: const Icon(Icons.sync_alt, size: 18),
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
}

class _InfoTile extends StatelessWidget {
  final IconData icono;
  final String etiqueta;
  final String valor;

  const _InfoTile({required this.icono, required this.etiqueta, required this.valor});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Icon(icono, size: 18, color: AppColors.primary),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  etiqueta,
                  style: TextStyle(fontSize: 10.5, color: Colors.grey.shade500),
                ),
                Text(
                  valor,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
