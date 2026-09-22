import 'usuario.dart';

class Asignacion {
  final int id;
  final int solicitudId;
  final DateTime? fechaAsignacion;
  final String? comentariosAsignacion;
  final String estadoAsignacion;
  final Usuario? usuarioAsignado;
  final Usuario? asignadoPor;

  Asignacion({
    required this.id,
    required this.solicitudId,
    this.fechaAsignacion,
    this.comentariosAsignacion,
    required this.estadoAsignacion,
    this.usuarioAsignado,
    this.asignadoPor,
  });

  factory Asignacion.fromJson(Map<String, dynamic> json) {
    return Asignacion(
      id: json['id'] as int,
      solicitudId: json['solicitud_id'] as int,
      fechaAsignacion: json['fecha_asignacion'] != null
          ? DateTime.tryParse(json['fecha_asignacion'] as String)
          : null,
      comentariosAsignacion: json['comentarios_asignacion'] as String?,
      estadoAsignacion: json['estado_asignacion'] as String? ?? '',
      usuarioAsignado: json['usuario_asignado'] is Map<String, dynamic>
          ? Usuario.fromJson(json['usuario_asignado'] as Map<String, dynamic>)
          : null,
      asignadoPor: json['asignado_por'] is Map<String, dynamic>
          ? Usuario.fromJson(json['asignado_por'] as Map<String, dynamic>)
          : null,
    );
  }
}
