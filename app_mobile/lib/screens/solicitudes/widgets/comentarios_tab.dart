import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../models/solicitud.dart';
import '../../../services/solicitud_service.dart';
import '../../../widgets/estado_vacio.dart';

class ComentariosTab extends StatefulWidget {
  final Solicitud solicitud;
  final Future<void> Function() onCambio;
  final void Function(Object) onError;

  const ComentariosTab({
    super.key,
    required this.solicitud,
    required this.onCambio,
    required this.onError,
  });

  @override
  State<ComentariosTab> createState() => _ComentariosTabState();
}

class _ComentariosTabState extends State<ComentariosTab> {
  final _service = SolicitudService();
  final _controller = TextEditingController();
  bool _enviando = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _enviar() async {
    final texto = _controller.text.trim();
    if (texto.isEmpty) return;

    setState(() => _enviando = true);
    try {
      await _service.agregarComentario(widget.solicitud.id, texto);
      _controller.clear();
      await widget.onCambio();
    } catch (e) {
      widget.onError(e);
    } finally {
      if (mounted) setState(() => _enviando = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final comentarios = widget.solicitud.comentarios;
    final formato = DateFormat('dd/MM/yyyy HH:mm');

    return Column(
      children: [
        Expanded(
          child: comentarios.isEmpty
              ? const EstadoVacio(
                  mensaje: 'Aún no hay comentarios.',
                  icono: Icons.chat_bubble_outline,
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(12),
                  itemCount: comentarios.length,
                  separatorBuilder: (_, _) => const Divider(),
                  itemBuilder: (context, i) {
                    final c = comentarios[i];
                    return ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: const CircleAvatar(child: Icon(Icons.person_outline)),
                      title: Text(c.autor?.nombreCompleto ?? 'Usuario'),
                      subtitle: Text(c.textoComentario),
                      trailing: c.fechaComentario != null
                          ? Text(
                              formato.format(c.fechaComentario!),
                              style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
                            )
                          : null,
                    );
                  },
                ),
        ),
        SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _controller,
                    decoration: const InputDecoration(
                      hintText: 'Escribe un comentario...',
                      border: OutlineInputBorder(),
                      isDense: true,
                    ),
                    minLines: 1,
                    maxLines: 3,
                  ),
                ),
                const SizedBox(width: 8),
                _enviando
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : IconButton.filled(
                        onPressed: _enviar,
                        icon: const Icon(Icons.send),
                      ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
