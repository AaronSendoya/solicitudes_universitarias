import 'package:flutter/material.dart';

import '../../../core/api_exception.dart';
import '../../../models/catalogo_item.dart';
import '../../../models/solicitud.dart';
import '../../../services/catalogo_service.dart';
import '../../../services/solicitud_service.dart';

Future<bool?> mostrarDialogoCambiarEstado(BuildContext context, Solicitud solicitud) {
  return showDialog<bool>(
    context: context,
    builder: (_) => _CambiarEstadoDialog(solicitud: solicitud),
  );
}

class _CambiarEstadoDialog extends StatefulWidget {
  final Solicitud solicitud;

  const _CambiarEstadoDialog({required this.solicitud});

  @override
  State<_CambiarEstadoDialog> createState() => _CambiarEstadoDialogState();
}

class _CambiarEstadoDialogState extends State<_CambiarEstadoDialog> {
  final _catalogoService = CatalogoService();
  final _solicitudService = SolicitudService();
  final _comentarioController = TextEditingController();

  List<CatalogoItem> _estados = [];
  int? _estadoId;
  bool _cargando = true;
  bool _guardando = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _estadoId = widget.solicitud.estadoId;
    _cargarEstados();
  }

  @override
  void dispose() {
    _comentarioController.dispose();
    super.dispose();
  }

  Future<void> _cargarEstados() async {
    final estados = await _catalogoService.estados();
    setState(() {
      _estados = estados;
      _cargando = false;
    });
  }

  Future<void> _guardar() async {
    if (_estadoId == null) return;
    setState(() {
      _guardando = true;
      _error = null;
    });
    try {
      await _solicitudService.cambiarEstado(
        widget.solicitud.id,
        estadoId: _estadoId!,
        comentario: _comentarioController.text.trim(),
      );
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      setState(() {
        _error = e is ApiException ? e.friendlyMessage : 'No se pudo cambiar el estado.';
        _guardando = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Cambiar estado'),
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
                  initialValue: _estadoId,
                  decoration: const InputDecoration(labelText: 'Nuevo estado'),
                  items: _estados
                      .map((e) => DropdownMenuItem(value: e.id, child: Text(e.nombre)))
                      .toList(),
                  onChanged: (v) => setState(() => _estadoId = v),
                ),
                const SizedBox(height: 16),
                TextField(
                  controller: _comentarioController,
                  decoration: const InputDecoration(
                    labelText: 'Comentario / acción realizada (opcional)',
                  ),
                  maxLines: 3,
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
