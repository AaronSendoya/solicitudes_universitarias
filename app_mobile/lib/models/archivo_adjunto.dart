import '../core/api_config.dart';
import 'usuario.dart';

class ArchivoAdjunto {
  final int id;
  final int solicitudId;
  final String nombreArchivo;
  final String urlArchivo;
  final String? tipoArchivo;
  final int? tamanoBytes;
  final DateTime? fechaSubida;
  final Usuario? subidoPor;

  ArchivoAdjunto({
    required this.id,
    required this.solicitudId,
    required this.nombreArchivo,
    required this.urlArchivo,
    this.tipoArchivo,
    this.tamanoBytes,
    this.fechaSubida,
    this.subidoPor,
  });

  bool get esImagen => tipoArchivo?.startsWith('image/') ?? false;

  /// The API returns a host-relative path (e.g. "/storage/adjuntos/1/x.png")
  /// so it can be resolved against whichever base URL this client used.
  static String _resolverUrl(String? path) {
    if (path == null) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return '${ApiConfig.storageBaseUrl}$path';
  }

  factory ArchivoAdjunto.fromJson(Map<String, dynamic> json) {
    return ArchivoAdjunto(
      id: json['id'] as int,
      solicitudId: json['solicitud_id'] as int,
      nombreArchivo: json['nombre_archivo'] as String? ?? '',
      urlArchivo: _resolverUrl(json['url_archivo'] as String?),
      tipoArchivo: json['tipo_archivo'] as String?,
      tamanoBytes: json['tamano_bytes'] as int?,
      fechaSubida: json['fecha_subida'] != null
          ? DateTime.tryParse(json['fecha_subida'] as String)
          : null,
      subidoPor: json['subido_por'] is Map<String, dynamic>
          ? Usuario.fromJson(json['subido_por'] as Map<String, dynamic>)
          : null,
    );
  }
}
