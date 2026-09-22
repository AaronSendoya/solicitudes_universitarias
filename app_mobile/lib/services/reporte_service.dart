import '../core/api_client.dart';
import '../models/reporte_gestion.dart';

class ReporteService {
  final ApiClient _client;

  ReporteService({ApiClient? client}) : _client = client ?? ApiClient.instance;

  Future<ReporteGestion> gestion({
    DateTime? fechaDesde,
    DateTime? fechaHasta,
    int? tipoId,
    int? prioridadId,
    int? ubicacionId,
  }) async {
    String? fmt(DateTime? d) => d == null
        ? null
        : '${d.year.toString().padLeft(4, '0')}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';

    final json = await _client.get(
      '/reportes/gestion',
      query: {
        'fecha_desde': fmt(fechaDesde),
        'fecha_hasta': fmt(fechaHasta),
        'tipo_id': tipoId,
        'prioridad_id': prioridadId,
        'ubicacion_id': ubicacionId,
      },
    );
    return ReporteGestion.fromJson(json as Map<String, dynamic>);
  }
}
