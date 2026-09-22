import 'package:flutter/material.dart';

import '../../../core/api_exception.dart';
import '../../../models/solicitud.dart';
import '../../../models/usuario.dart';
import '../../../services/solicitud_service.dart';
import '../../../services/usuario_service.dart';

Future<bool?> mostrarDialogoAsignar(BuildContext context, Solicitud solicitud) {
  return showDialog<bool>(
    context: context,
    builder: (_) => _AsignarDialog(solicitud: solicitud),
  );
}

class _AsignarDialog extends StatefulWidget {
  final Solicitud solicitud;

  const _AsignarDialog({required this.solicitud});

  @override
  State<_AsignarDialog> createState() => _AsignarDialogState();
}

class _AsignarDialogState extends State<_AsignarDialog> {
  final _usuarioService = UsuarioService();
  final _solicitudService = SolicitudService();
  final _comentarioController = TextEditingController();

  List<Usuario> _tecnicos = [];
  int? _tecnicoId;
  bool _cargando = true;
  bool _guardando = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _tecnicoId = widget.solicitud.responsableActual?.id;
    _cargarTecnicos();
  }

  @override
  void dispose() {
    _comentarioController.dispose();
    super.dispose();
  }

  Future<void> _cargarTecnicos() async {
    final tecnicos = await _usuarioService.tecnicos();
    setState(() {
      _tecnicos = tecnicos;
      _cargando = false;
    });
  }

  Future<void> _guardar() async {
    if (_tecnicoId == null) {
      setState(() => _error = 'Selecciona un técnico.');
      return;
    }
    setState(() {
      _guardando = true;
      _error = null;
    });
    try {
      await _solicitudService.asignar(
        widget.solicitud.id,
        usuarioAsignadoId: _tecnicoId!,
        comentario: _comentarioController.text.trim(),
      );
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      setState(() {
        _error = e is ApiException ? e.friendlyMessage : 'No se pudo asignar el responsable.';
        _guardando = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Asignar responsable'),
      content: _cargando
          ? const SizedBox(height: 80, child: Center(child: CircularProgressIndicator()))
          : Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (_error != null) ...[
                  Text(_error!, style: const TextStyle(color: Colors.red)),
                  const SizedBox(height: 8),
                ],
                if (_tecnicos.isEmpty)
                  const Text('No hay técnicos registrados en el sistema.')
                else
                  DropdownButtonFormField<int>(
                    initialValue: _tecnicoId,
                    decoration: const InputDecoration(labelText: 'Técnico'),
                    items: _tecnicos
                        .map((t) => DropdownMenuItem(value: t.id, child: Text(t.nombreCompleto)))
                        .toList(),
                    onChanged: (v) => setState(() => _tecnicoId = v),
                  ),
                const SizedBox(height: 16),
                TextField(
                  controller: _comentarioController,
                  decoration: const InputDecoration(
                    labelText: 'Indicaciones (opcional)',
                  ),
                  maxLines: 2,
                ),
              ],
            ),
      actions: [
        TextButton(
          onPressed: _guardando ? null : () => Navigator.pop(context, false),
          child: const Text('Cancelar'),
        ),
        FilledButton(
          onPressed: (_cargando || _guardando || _tecnicos.isEmpty) ? null : _guardar,
          child: const Text('Asignar'),
        ),
      ],
    );
  }
}
