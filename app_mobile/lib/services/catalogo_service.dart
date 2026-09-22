import '../core/api_client.dart';
import '../models/catalogo_item.dart';
import '../models/recurso.dart';

class CatalogoService {
  final ApiClient _client;

  CatalogoService({ApiClient? client}) : _client = client ?? ApiClient();

  Future<List<CatalogoItem>> _fetchLista(String path) async {
    final json = await _client.get(path);
    return (json['data'] as List<dynamic>)
        .map((e) => CatalogoItem.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<CatalogoItem>> estados() => _fetchLista('/catalogos/estados');
  Future<List<CatalogoItem>> tipos() => _fetchLista('/catalogos/tipos');
  Future<List<CatalogoItem>> prioridades() => _fetchLista('/catalogos/prioridades');
  Future<List<CatalogoItem>> ubicaciones() => _fetchLista('/catalogos/ubicaciones');

  Future<List<Recurso>> recursos() async {
    final json = await _client.get('/catalogos/recursos');
    return (json['data'] as List<dynamic>)
        .map((e) => Recurso.fromJson(e as Map<String, dynamic>))
        .toList();
  }
}
