import '../core/api_client.dart';
import '../models/comentario.dart';
import '../models/historial_item.dart';
import '../models/solicitud.dart';

class SolicitudService {
  final ApiClient _client;

  SolicitudService({ApiClient? client}) : _client = client ?? ApiClient.instance;

  /// Listing is automatically scoped by the API according to the caller's
  /// role: an Estudiante sees only their own, a Tecnico sees only what's
  /// assigned to them, and an Administrativo sees the full bandeja.
  Future<List<Solicitud>> listar({int? estadoId, String? busqueda}) async {
    final json = await _client.get(
      '/solicitudes',
      query: {'estado_id': estadoId, 'q': busqueda, 'per_page': 50},
    );
    return (json['data'] as List<dynamic>)
        .map((e) => Solicitud.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<Solicitud> obtener(int id) async {
    final json = await _client.get('/solicitudes/$id');
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<Solicitud> crear({
    required String titulo,
    required String descripcion,
    required int tipoId,
    required int ubicacionId,
    int? recursoId,
  }) async {
    final json = await _client.post(
      '/solicitudes',
      body: {
        'titulo': titulo,
        'descripcion': descripcion,
        'tipo_id': tipoId,
        'ubicacion_id': ubicacionId,
        if (recursoId != null) 'recurso_id': recursoId,
      },
    );
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<Solicitud> actualizar(
    int id, {
    String? titulo,
    String? descripcion,
    int? tipoId,
    int? ubicacionId,
    int? recursoId,
  }) async {
    final json = await _client.put(
      '/solicitudes/$id',
      body: {
        if (titulo != null) 'titulo': titulo,
        if (descripcion != null) 'descripcion': descripcion,
        if (tipoId != null) 'tipo_id': tipoId,
        if (ubicacionId != null) 'ubicacion_id': ubicacionId,
        if (recursoId != null) 'recurso_id': recursoId,
      },
    );
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<void> eliminar(int id) => _client.delete('/solicitudes/$id');

  Future<Solicitud> clasificar(int id, {int? tipoId, int? prioridadId}) async {
    final json = await _client.patch(
      '/solicitudes/$id/clasificar',
      body: {
        if (tipoId != null) 'tipo_id': tipoId,
        if (prioridadId != null) 'prioridad_id': prioridadId,
      },
    );
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<Solicitud> asignar(int id, {required int usuarioAsignadoId, String? comentario}) async {
    final json = await _client.post(
      '/solicitudes/$id/asignar',
      body: {
        'usuario_asignado_id': usuarioAsignadoId,
        if (comentario != null && comentario.isNotEmpty) 'comentarios_asignacion': comentario,
      },
    );
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<Solicitud> cambiarEstado(int id, {required int estadoId, String? comentario}) async {
    final json = await _client.patch(
      '/solicitudes/$id/estado',
      body: {
        'estado_id': estadoId,
        if (comentario != null && comentario.isNotEmpty) 'comentario': comentario,
      },
    );
    return Solicitud.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<Comentario> agregarComentario(int id, String texto) async {
    final json = await _client.post(
      '/solicitudes/$id/comentarios',
      body: {'texto_comentario': texto},
    );
    return Comentario.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<List<HistorialItem>> historial(int id) async {
    final json = await _client.get('/solicitudes/$id/historial');
    return (json['data'] as List<dynamic>)
        .map((e) => HistorialItem.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<void> adjuntarEvidencia(int id, String filePath, {String? fileName}) {
    return _client.uploadFile(
      '/solicitudes/$id/adjuntos',
      fieldName: 'archivo',
      filePath: filePath,
      fileName: fileName,
    );
  }
}
