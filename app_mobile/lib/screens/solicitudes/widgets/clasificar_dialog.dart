import 'package:flutter/material.dart';

import '../../../core/api_exception.dart';
import '../../../models/catalogo_item.dart';
import '../../../models/solicitud.dart';
import '../../../services/catalogo_service.dart';
import '../../../services/solicitud_service.dart';

Future<bool?> mostrarDialogoClasificar(BuildContext context, Solicitud solicitud) {
  return showDialog<bool>(
    context: context,
    builder: (_) => _ClasificarDialog(solicitud: solicitud),
  );
}

class _ClasificarDialog extends StatefulWidget {
  final Solicitud solicitud;

  const _ClasificarDialog({required this.solicitud});

  @override
  State<_ClasificarDialog> createState() => _ClasificarDialogState();
}

class _ClasificarDialogState extends State<_ClasificarDialog> {
  final _catalogoService = CatalogoService();
  final _solicitudService = SolicitudService();

  List<CatalogoItem> _tipos = [];
  List<CatalogoItem> _prioridades = [];
  int? _tipoId;
  int? _prioridadId;
  bool _cargando = true;
  bool _guardando = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _tipoId = widget.solicitud.tipoId;
    _prioridadId = widget.solicitud.prioridadId;
    _cargarCatalogos();
  }

  Future<void> _cargarCatalogos() async {
    final resultados = await Future.wait([
      _catalogoService.tipos(),
      _catalogoService.prioridades(),
    ]);
    setState(() {
      _tipos = resultados[0];
      _prioridades = resultados[1];
      _cargando = false;
    });
  }

  Future<void> _guardar() async {
    setState(() {
      _guardando = true;
      _error = null;
    });
    try {
      await _solicitudService.clasificar(
        widget.solicitud.id,
        tipoId: _tipoId,
        prioridadId: _prioridadId,
      );
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      setState(() {
        _error = e is ApiException ? e.friendlyMessage : 'No se pudo clasificar la solicitud.';
        _guardando = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Clasificar y priorizar'),
      content: _cargando
          ? const SizedBox(height: 80, child: Center(child: CircularProgressIndicator()))
          : Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (_error != null) ...[
                  Text(_error!, style: const TextStyle(color: Colors.red)),
                  const SizedBox(height: 8),
                ],
                DropdownButtonFormField<int>(
                  initialValue: _tipoId,
                  decoration: const InputDecoration(labelText: 'Tipo'),
                  items: _tipos
                      .map((t) => DropdownMenuItem(value: t.id, child: Text(t.nombre)))
                      .toList(),
                  onChanged: (v) => setState(() => _tipoId = v),
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<int>(
                  initialValue: _prioridadId,
                  decoration: const InputDecoration(labelText: 'Prioridad'),
                  items: _prioridades
                      .map((p) => DropdownMenuItem(value: p.id, child: Text(p.nombre)))
                      .toList(),
                  onChanged: (v) => setState(() => _prioridadId = v),
                ),
              ],
            ),
      actions: [
        TextButton(
          onPressed: _guardando ? null : () => Navigator.pop(context, false),
          child: const Text('Cancelar'),
        ),
        FilledButton(
          onPressed: (_cargando || _guardando) ? null : _guardar,
          child: const Text('Guardar'),
        ),
      ],
    );
  }
}
