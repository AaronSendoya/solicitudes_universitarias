class Usuario {
  final int id;
  final String nombres;
  final String apellidos;
  final String nombreCompleto;
  final String correoInstitucional;
  final String? rol;
  final int? rolId;

  Usuario({
    required this.id,
    required this.nombres,
    required this.apellidos,
    required this.nombreCompleto,
    required this.correoInstitucional,
    this.rol,
    this.rolId,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'] as int,
      nombres: json['nombres'] as String? ?? '',
      apellidos: json['apellidos'] as String? ?? '',
      nombreCompleto: json['nombre_completo'] as String? ?? '',
      correoInstitucional: json['correo_institucional'] as String? ?? '',
      rol: json['rol'] as String?,
      rolId: json['rol_id'] as int?,
    );
  }

  bool get esEstudiante => rol == 'Estudiante';
  bool get esAdministrativo => rol == 'Administrativo';
  bool get esTecnico => rol == 'Tecnico';
}
