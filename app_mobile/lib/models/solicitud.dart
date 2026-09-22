import 'archivo_adjunto.dart';
import 'asignacion.dart';
import 'comentario.dart';
import 'historial_item.dart';
import 'usuario.dart';

class Solicitud {
  final int id;
  final String titulo;
  final String descripcion;
  final DateTime? fechaCreacion;
  final DateTime? fechaActualizacion;
  final DateTime? fechaCierre;

  final int estadoId;
  final String? estado;
  final int tipoId;
  final String? tipo;
  final int prioridadId;
  final String? prioridad;
  final int ubicacionId;
  final String? ubicacion;
  final int? recursoId;
  final String? recurso;

  final Usuario? solicitante;
  final Usuario? responsableActual;

  final List<Comentario> comentarios;
  final List<ArchivoAdjunto> archivosAdjuntos;
  final List<Asignacion> asignaciones;
  final List<HistorialItem> historial;

  Solicitud({
    required this.id,
    required this.titulo,
    required this.descripcion,
    this.fechaCreacion,
    this.fechaActualizacion,
    this.fechaCierre,
    required this.estadoId,
    this.estado,
    required this.tipoId,
    this.tipo,
    required this.prioridadId,
    this.prioridad,
    required this.ubicacionId,
    this.ubicacion,
    this.recursoId,
    this.recurso,
    this.solicitante,
    this.responsableActual,
    this.comentarios = const [],
    this.archivosAdjuntos = const [],
    this.asignaciones = const [],
    this.historial = const [],
  });

  bool get estaAbierta => estado == 'Abierta';
  bool get estaCerrada => estado == 'Cerrada';

  factory Solicitud.fromJson(Map<String, dynamic> json) {
    DateTime? parseDate(String? value) => value != null ? DateTime.tryParse(value) : null;

    return Solicitud(
      id: json['id'] as int,
      titulo: json['titulo'] as String? ?? '',
      descripcion: json['descripcion'] as String? ?? '',
      fechaCreacion: parseDate(json['fecha_creacion'] as String?),
      fechaActualizacion: parseDate(json['fecha_actualizacion'] as String?),
      fechaCierre: parseDate(json['fecha_cierre'] as String?),
      estadoId: json['estado_id'] as int,
      estado: json['estado'] as String?,
      tipoId: json['tipo_id'] as int,
      tipo: json['tipo'] as String?,
      prioridadId: json['prioridad_id'] as int,
      prioridad: json['prioridad'] as String?,
      ubicacionId: json['ubicacion_id'] as int,
      ubicacion: json['ubicacion'] as String?,
      recursoId: json['recurso_id'] as int?,
      recurso: json['recurso'] as String?,
      solicitante: json['solicitante'] is Map<String, dynamic>
          ? Usuario.fromJson(json['solicitante'] as Map<String, dynamic>)
          : null,
      responsableActual: json['responsable_actual'] is Map<String, dynamic>
          ? Usuario.fromJson(json['responsable_actual'] as Map<String, dynamic>)
          : null,
      comentarios: (json['comentarios'] as List<dynamic>? ?? [])
          .map((e) => Comentario.fromJson(e as Map<String, dynamic>))
          .toList(),
      archivosAdjuntos: (json['archivos_adjuntos'] as List<dynamic>? ?? [])
          .map((e) => ArchivoAdjunto.fromJson(e as Map<String, dynamic>))
          .toList(),
      asignaciones: (json['asignaciones'] as List<dynamic>? ?? [])
          .map((e) => Asignacion.fromJson(e as Map<String, dynamic>))
          .toList(),
      historial: (json['historial'] as List<dynamic>? ?? [])
          .map((e) => HistorialItem.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
