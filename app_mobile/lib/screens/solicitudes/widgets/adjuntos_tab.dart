import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../models/archivo_adjunto.dart';
import '../../../models/solicitud.dart';
import '../../../services/solicitud_service.dart';
import '../../../widgets/estado_vacio.dart';

class AdjuntosTab extends StatefulWidget {
  final Solicitud solicitud;
  final Future<void> Function() onCambio;
  final void Function(Object) onError;

  const AdjuntosTab({
    super.key,
    required this.solicitud,
    required this.onCambio,
    required this.onError,
  });

  @override
  State<AdjuntosTab> createState() => _AdjuntosTabState();
}

class _AdjuntosTabState extends State<AdjuntosTab> {
  final _service = SolicitudService();
  final _imagePicker = ImagePicker();
  bool _subiendo = false;

  Future<void> _subir(String path, String fileName) async {
    setState(() => _subiendo = true);
    try {
      await _service.adjuntarEvidencia(widget.solicitud.id, path, fileName: fileName);
      await widget.onCambio();
    } catch (e) {
      widget.onError(e);
    } finally {
      if (mounted) setState(() => _subiendo = false);
    }
  }

  Future<void> _tomarFoto() async {
    final foto = await _imagePicker.pickImage(source: ImageSource.camera, imageQuality: 80);
    if (foto != null) await _subir(foto.path, foto.name);
  }

  Future<void> _elegirDeGaleria() async {
    final foto = await _imagePicker.pickImage(source: ImageSource.gallery, imageQuality: 80);
    if (foto != null) await _subir(foto.path, foto.name);
  }

  Future<void> _elegirArchivo() async {
    final resultado = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'],
    );
    final archivo = resultado?.files.single;
    if (archivo?.path != null) await _subir(archivo!.path!, archivo.name);
  }

  void _mostrarOpciones() {
    showModalBottomSheet(
      context: context,
      builder: (ctx) => SafeArea(
        child: Wrap(
          children: [
            ListTile(
              leading: const Icon(Icons.photo_camera_outlined),
              title: const Text('Tomar foto'),
              onTap: () {
                Navigator.pop(ctx);
                _tomarFoto();
              },
            ),
            ListTile(
              leading: const Icon(Icons.photo_library_outlined),
              title: const Text('Elegir de galería'),
              onTap: () {
                Navigator.pop(ctx);
                _elegirDeGaleria();
              },
            ),
            ListTile(
              leading: const Icon(Icons.attach_file),
              title: const Text('Elegir documento'),
              onTap: () {
                Navigator.pop(ctx);
                _elegirArchivo();
              },
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _abrir(ArchivoAdjunto archivo) async {
    if (archivo.esImagen) {
      showDialog(
        context: context,
        builder: (ctx) => Dialog(
          child: InteractiveViewer(child: Image.network(archivo.urlArchivo)),
        ),
      );
      return;
    }
    final uri = Uri.tryParse(archivo.urlArchivo);
    if (uri != null) await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  @override
  Widget build(BuildContext context) {
    final adjuntos = widget.solicitud.archivosAdjuntos;
    final formato = DateFormat('dd/MM/yyyy HH:mm');

    return Stack(
      children: [
        adjuntos.isEmpty
            ? const EstadoVacio(
                mensaje: 'No se ha adjuntado evidencia todavía.',
                icono: Icons.attach_file,
              )
            : GridView.builder(
                padding: const EdgeInsets.all(12),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  mainAxisSpacing: 10,
                  crossAxisSpacing: 10,
                  childAspectRatio: 0.85,
                ),
                itemCount: adjuntos.length,
                itemBuilder: (context, i) {
                  final a = adjuntos[i];
                  return Card(
                    clipBehavior: Clip.antiAlias,
                    child: InkWell(
                      onTap: () => _abrir(a),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          Expanded(
                            child: a.esImagen
                                ? Image.network(a.urlArchivo, fit: BoxFit.cover)
                                : Container(
                                    color: Colors.grey.shade200,
                                    child: const Icon(
                                      Icons.insert_drive_file_outlined,
                                      size: 48,
                                      color: Colors.grey,
                                    ),
                                  ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(8),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  a.nombreArchivo,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                                ),
                                if (a.fechaSubida != null)
                                  Text(
                                    formato.format(a.fechaSubida!),
                                    style: TextStyle(fontSize: 10, color: Colors.grey.shade500),
                                  ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
        Positioned(
          right: 16,
          bottom: 16,
          child: FloatingActionButton(
            onPressed: _subiendo ? null : _mostrarOpciones,
            child: _subiendo
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                  )
                : const Icon(Icons.add_a_photo_outlined),
          ),
        ),
      ],
    );
  }
}
