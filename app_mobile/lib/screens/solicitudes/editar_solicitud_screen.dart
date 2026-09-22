import 'package:flutter/material.dart';

import '../../core/api_exception.dart';
import '../../models/catalogo_item.dart';
import '../../models/recurso.dart';
import '../../models/solicitud.dart';
import '../../services/catalogo_service.dart';
import '../../services/solicitud_service.dart';

class EditarSolicitudScreen extends StatefulWidget {
  final Solicitud solicitud;

  const EditarSolicitudScreen({super.key, required this.solicitud});

  @override
  State<EditarSolicitudScreen> createState() => _EditarSolicitudScreenState();
}

class _EditarSolicitudScreenState extends State<EditarSolicitudScreen> {
  final _formKey = GlobalKey<FormState>();
  final _catalogoService = CatalogoService();
  final _solicitudService = SolicitudService();

  late final _tituloController = TextEditingController(text: widget.solicitud.titulo);
  late final _descripcionController = TextEditingController(text: widget.solicitud.descripcion);

  List<CatalogoItem> _tipos = [];
  List<CatalogoItem> _ubicaciones = [];
  List<Recurso> _recursos = [];

  late int? _tipoId = widget.solicitud.tipoId;
  late int? _ubicacionId = widget.solicitud.ubicacionId;
  late int? _recursoId = widget.solicitud.recursoId;

  bool _cargandoCatalogos = true;
  bool _guardando = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _cargarCatalogos();
  }

  Future<void> _cargarCatalogos() async {
    try {
      final resultados = await Future.wait([
        _catalogoService.tipos(),
        _catalogoService.ubicaciones(),
        _catalogoService.recursos(),
      ]);
      setState(() {
        _tipos = resultados[0] as List<CatalogoItem>;
        _ubicaciones = resultados[1] as List<CatalogoItem>;
        _recursos = resultados[2] as List<Recurso>;
        _cargandoCatalogos = false;
      });
    } catch (e) {
      setState(() {
        _error = 'No se pudieron cargar los catálogos.';
        _cargandoCatalogos = false;
      });
    }
  }

  List<Recurso> get _recursosFiltrados =>
      _recursos.where((r) => r.ubicacionId == _ubicacionId).toList();

  Future<void> _guardar() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _guardando = true;
      _error = null;
    });

    try {
      await _solicitudService.actualizar(
        widget.solicitud.id,
        titulo: _tituloController.text.trim(),
        descripcion: _descripcionController.text.trim(),
        tipoId: _tipoId,
        ubicacionId: _ubicacionId,
        recursoId: _recursoId,
      );
      if (mounted) Navigator.of(context).pop(true);
    } catch (e) {
      setState(() {
        _error = e is ApiException ? e.friendlyMessage : 'No se pudo actualizar la solicitud.';
      });
    } finally {
      if (mounted) setState(() => _guardando = false);
    }
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _descripcionController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Editar solicitud')),
      body: _cargandoCatalogos
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    if (_error != null) ...[
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.red.shade50,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(_error!, style: TextStyle(color: Colors.red.shade800)),
                      ),
                      const SizedBox(height: 16),
                    ],
                    TextFormField(
                      controller: _tituloController,
                      decoration: const InputDecoration(labelText: 'Título de la solicitud'),
                      validator: (v) => (v == null || v.isEmpty) ? 'Ingresa un título' : null,
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _descripcionController,
                      decoration: const InputDecoration(labelText: 'Descripción del problema'),
                      maxLines: 4,
                      validator: (v) =>
                          (v == null || v.isEmpty) ? 'Describe la solicitud' : null,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      initialValue: _tipoId,
                      decoration: const InputDecoration(labelText: 'Tipo de solicitud'),
                      items: _tipos
                          .map((t) => DropdownMenuItem(value: t.id, child: Text(t.nombre)))
                          .toList(),
                      onChanged: (v) => setState(() => _tipoId = v),
                      validator: (v) => v == null ? 'Selecciona un tipo' : null,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      initialValue: _ubicacionId,
                      decoration: const InputDecoration(labelText: 'Ubicación'),
                      items: _ubicaciones
                          .map((u) => DropdownMenuItem(value: u.id, child: Text(u.nombre)))
                          .toList(),
                      onChanged: (v) => setState(() {
                        _ubicacionId = v;
                        _recursoId = null;
                      }),
                      validator: (v) => v == null ? 'Selecciona una ubicación' : null,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      initialValue: _recursoId,
                      decoration: const InputDecoration(
                        labelText: 'Recurso relacionado (opcional)',
                      ),
                      items: _recursosFiltrados
                          .map((r) => DropdownMenuItem(value: r.id, child: Text(r.nombre)))
                          .toList(),
                      onChanged: (v) => setState(() => _recursoId = v),
                    ),
                    const SizedBox(height: 24),
                    FilledButton(
                      onPressed: _guardando ? null : _guardar,
                      child: _guardando
                          ? const SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            )
                          : const Text('Guardar cambios'),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
