class Recurso {
  final int id;
  final String nombre;
  final String? tipo;
  final String? estado;
  final int? ubicacionId;
  final String? ubicacion;

  Recurso({
    required this.id,
    required this.nombre,
    this.tipo,
    this.estado,
    this.ubicacionId,
    this.ubicacion,
  });

  factory Recurso.fromJson(Map<String, dynamic> json) {
    return Recurso(
      id: json['id'] as int,
      nombre: json['nombre'] as String,
      tipo: json['tipo'] as String?,
      estado: json['estado'] as String?,
      ubicacionId: json['ubicacion_id'] as int?,
      ubicacion: json['ubicacion'] as String?,
    );
  }
}
