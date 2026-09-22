import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../../models/catalogo_item.dart';
import '../../models/solicitud.dart';
import '../../models/usuario.dart';
import '../../providers/auth_provider.dart';
import '../../services/catalogo_service.dart';
import '../../services/solicitud_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/error_retry.dart';
import '../../widgets/estado_chip.dart';
import '../../widgets/estado_vacio.dart';
import '../../widgets/prioridad_chip.dart';
import '../../widgets/tipo_icon.dart';
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
    final usuario = context.watch<AuthProvider>().usuario!;

    return RefreshIndicator(
      onRefresh: _recargar,
      child: FutureBuilder<List<Solicitud>>(
        future: _futuro,
        builder: (context, snapshot) {
          final solicitudes = snapshot.data ?? [];

          return CustomScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            slivers: [
              SliverToBoxAdapter(child: _CabeceraSaludo(usuario: usuario, solicitudes: solicitudes)),
              if (_estados.isNotEmpty) SliverToBoxAdapter(child: _buildFiltros()),
              if (snapshot.connectionState == ConnectionState.waiting)
                const SliverFillRemaining(child: Center(child: CircularProgressIndicator()))
              else if (snapshot.hasError)
                SliverFillRemaining(child: ErrorRetry(error: snapshot.error!, onRetry: _recargar))
              else if (solicitudes.isEmpty)
                const SliverFillRemaining(
                  hasScrollBody: false,
                  child: EstadoVacio(
                    mensaje: 'No hay solicitudes para mostrar.',
                    icono: Icons.assignment_outlined,
                  ),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                  sliver: SliverList.separated(
                    itemCount: solicitudes.length,
                    separatorBuilder: (_, _) => const SizedBox(height: 10),
                    itemBuilder: (context, i) => _SolicitudCard(
                      solicitud: solicitudes[i],
                      onTap: () => _abrirDetalle(solicitudes[i]),
                    ),
                  ),
                ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildFiltros() {
    return SizedBox(
      height: 44,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
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
        label: Text(
          label,
          style: TextStyle(
            fontWeight: FontWeight.w600,
            color: seleccionado ? Colors.white : const Color(0xFF15213B),
          ),
        ),
        selected: seleccionado,
        showCheckmark: false,
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

class _CabeceraSaludo extends StatelessWidget {
  final Usuario usuario;
  final List<Solicitud> solicitudes;

  const _CabeceraSaludo({required this.usuario, required this.solicitudes});

  @override
  Widget build(BuildContext context) {
    final abiertas = solicitudes.where((s) => s.estado == 'Abierta').length;
    final enProceso = solicitudes.where((s) => s.estado == 'En Proceso').length;
    final cerradas = solicitudes.where((s) => s.estado == 'Cerrada').length;

    return Container(
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 16),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AppColors.primary, AppColors.primaryDark],
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Hola, ${usuario.nombres}',
            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w700),
          ),
          const SizedBox(height: 2),
          Text(
            usuario.rol ?? '',
            style: TextStyle(color: Colors.white.withValues(alpha: 0.8), fontSize: 13),
          ),
          const SizedBox(height: 18),
          Row(
            children: [
              _stat('Total', solicitudes.length, Icons.inbox_outlined),
              _stat('Abiertas', abiertas, Icons.radio_button_unchecked),
              _stat('En proceso', enProceso, Icons.sync),
              _stat('Cerradas', cerradas, Icons.check_circle_outline),
            ],
          ),
        ],
      ),
    );
  }

  Widget _stat(String label, int valor, IconData icono) {
    return Expanded(
      child: Container(
        margin: const EdgeInsets.only(right: 8),
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.14),
          borderRadius: BorderRadius.circular(14),
        ),
        child: Column(
          children: [
            Icon(icono, color: Colors.white, size: 18),
            const SizedBox(height: 6),
            Text(
              '$valor',
              style: const TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.white.withValues(alpha: 0.85), fontSize: 10.5),
            ),
          ],
        ),
      ),
    );
  }
}

class _SolicitudCard extends StatelessWidget {
  final Solicitud solicitud;
  final VoidCallback onTap;

  const _SolicitudCard({required this.solicitud, required this.onTap});

  Color _colorEstado() {
    switch (solicitud.estado) {
      case 'Abierta':
        return AppColors.abierta;
      case 'En Proceso':
        return AppColors.enProceso;
      case 'Pendiente de Revisión':
        return AppColors.pendiente;
      case 'Cerrada':
        return AppColors.cerrada;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final fecha = solicitud.fechaCreacion;
    final colorEstado = _colorEstado();

    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: Colors.grey.shade200),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: IntrinsicHeight(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Container(
                  width: 5,
                  decoration: BoxDecoration(
                    color: colorEstado,
                    borderRadius: const BorderRadius.horizontal(left: Radius.circular(18)),
                  ),
                ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.all(14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: colorEstado.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Icon(iconoParaTipo(solicitud.tipo), size: 18, color: colorEstado),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Text(
                                solicitud.titulo,
                                style: Theme.of(
                                  context,
                                ).textTheme.titleMedium?.copyWith(height: 1.2),
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            const SizedBox(width: 8),
                            if (solicitud.estado != null) EstadoChip(estado: solicitud.estado!),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          solicitud.descripcion,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.35),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            if (solicitud.prioridad != null) PrioridadChip(prioridad: solicitud.prioridad!),
                            const Spacer(),
                            if (solicitud.responsableActual != null) ...[
                              Icon(Icons.engineering_outlined, size: 13, color: Colors.grey.shade500),
                              const SizedBox(width: 3),
                              Text(
                                solicitud.responsableActual!.nombres,
                                style: TextStyle(fontSize: 11.5, color: Colors.grey.shade600),
                              ),
                              const SizedBox(width: 10),
                            ],
                            if (fecha != null)
                              Text(
                                DateFormat('dd MMM', 'es').format(fecha),
                                style: TextStyle(fontSize: 11.5, color: Colors.grey.shade500),
                              ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
