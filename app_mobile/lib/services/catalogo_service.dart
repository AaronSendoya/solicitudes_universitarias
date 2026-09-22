import '../core/api_client.dart';
import '../models/catalogo_item.dart';
import '../models/recurso.dart';

/// Estados, tipos, prioridades, ubicaciones and recursos essentially never
/// change during a session, but nearly every screen and dialog needs at
/// least one of them. Caching them in memory after the first fetch turns
/// every subsequent dialog open into an instant, network-free read instead
/// of another round trip to the API.
class CatalogoService {
  final ApiClient _client;

  CatalogoService({ApiClient? client}) : _client = client ?? ApiClient.instance;

  static List<CatalogoItem>? _estados;
  static List<CatalogoItem>? _tipos;
  static List<CatalogoItem>? _prioridades;
  static List<CatalogoItem>? _ubicaciones;
  static List<Recurso>? _recursos;

  static Future<List<CatalogoItem>>? _estadosInFlight;
  static Future<List<CatalogoItem>>? _tiposInFlight;
  static Future<List<CatalogoItem>>? _prioridadesInFlight;
  static Future<List<CatalogoItem>>? _ubicacionesInFlight;
  static Future<List<Recurso>>? _recursosInFlight;

  Future<List<CatalogoItem>> _fetchLista(String path) async {
    final json = await _client.get(path);
    return (json['data'] as List<dynamic>)
        .map((e) => CatalogoItem.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<CatalogoItem>> estados() {
    if (_estados case final cached?) return Future.value(cached);
    return _estadosInFlight ??= _fetchLista('/catalogos/estados').then((v) {
      _estados = v;
      _estadosInFlight = null;
      return v;
    });
  }

  Future<List<CatalogoItem>> tipos() {
    if (_tipos case final cached?) return Future.value(cached);
    return _tiposInFlight ??= _fetchLista('/catalogos/tipos').then((v) {
      _tipos = v;
      _tiposInFlight = null;
      return v;
    });
  }

  Future<List<CatalogoItem>> prioridades() {
    if (_prioridades case final cached?) return Future.value(cached);
    return _prioridadesInFlight ??= _fetchLista('/catalogos/prioridades').then((v) {
      _prioridades = v;
      _prioridadesInFlight = null;
      return v;
    });
  }

  Future<List<CatalogoItem>> ubicaciones() {
    if (_ubicaciones case final cached?) return Future.value(cached);
    return _ubicacionesInFlight ??= _fetchLista('/catalogos/ubicaciones').then((v) {
      _ubicaciones = v;
      _ubicacionesInFlight = null;
      return v;
    });
  }

  Future<List<Recurso>> recursos() {
    if (_recursos case final cached?) return Future.value(cached);
    return _recursosInFlight ??= _client.get('/catalogos/recursos').then((json) {
      final v = (json['data'] as List<dynamic>)
          .map((e) => Recurso.fromJson(e as Map<String, dynamic>))
          .toList();
      _recursos = v;
      _recursosInFlight = null;
      return v;
    });
  }

  /// Fetches everything up front (in parallel) so the first screen after
  /// login already has warm catalogs and every later dialog opens instantly.
  static Future<void> precargar() {
    final service = CatalogoService();
    return Future.wait([
      service.estados(),
      service.tipos(),
      service.prioridades(),
      service.ubicaciones(),
      service.recursos(),
    ]);
  }
}
