import 'usuario.dart';

class Comentario {
  final int id;
  final int solicitudId;
  final String textoComentario;
  final DateTime? fechaComentario;
  final Usuario? autor;

  Comentario({
    required this.id,
    required this.solicitudId,
    required this.textoComentario,
    this.fechaComentario,
    this.autor,
  });

  factory Comentario.fromJson(Map<String, dynamic> json) {
    return Comentario(
      id: json['id'] as int,
      solicitudId: json['solicitud_id'] as int,
      textoComentario: json['texto_comentario'] as String? ?? '',
      fechaComentario: json['fecha_comentario'] != null
          ? DateTime.tryParse(json['fecha_comentario'] as String)
          : null,
      autor: json['autor'] is Map<String, dynamic>
          ? Usuario.fromJson(json['autor'] as Map<String, dynamic>)
          : null,
    );
  }
}
