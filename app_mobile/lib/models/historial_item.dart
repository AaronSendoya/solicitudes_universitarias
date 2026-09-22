import 'usuario.dart';

class HistorialItem {
  final int id;
  final int solicitudId;
  final String campoCambiado;
  final String? valorAnterior;
  final String? valorNuevo;
  final DateTime? fechaCambio;
  final Usuario? usuario;

  HistorialItem({
    required this.id,
    required this.solicitudId,
    required this.campoCambiado,
    this.valorAnterior,
    this.valorNuevo,
    this.fechaCambio,
    this.usuario,
  });

  factory HistorialItem.fromJson(Map<String, dynamic> json) {
    return HistorialItem(
      id: json['id'] as int,
      solicitudId: json['solicitud_id'] as int,
      campoCambiado: json['campo_cambiado'] as String? ?? '',
      valorAnterior: json['valor_anterior'] as String?,
      valorNuevo: json['valor_nuevo'] as String?,
      fechaCambio: json['fecha_cambio'] != null
          ? DateTime.tryParse(json['fecha_cambio'] as String)
          : null,
      usuario: json['usuario'] is Map<String, dynamic>
          ? Usuario.fromJson(json['usuario'] as Map<String, dynamic>)
          : null,
    );
  }

  /// Human friendly label for the "campo_cambiado" technical key.
  String get campoLegible => switch (campoCambiado) {
    'creacion' => 'Creación',
    'estado_id' => 'Estado',
    'tipo_id' => 'Tipo',
    'prioridad_id' => 'Prioridad',
    'ubicacion_id' => 'Ubicación',
    'recurso_id' => 'Recurso',
    'usuario_responsable_actual_id' => 'Responsable asignado',
    'titulo' => 'Título',
    'descripcion' => 'Descripción',
    _ => campoCambiado,
  };
}
