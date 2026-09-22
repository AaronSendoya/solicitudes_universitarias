import '../core/api_client.dart';
import '../models/usuario.dart';

class UsuarioService {
  final ApiClient _client;

  UsuarioService({ApiClient? client}) : _client = client ?? ApiClient();

  Future<List<Usuario>> listar({String? rol}) async {
    final json = await _client.get('/usuarios', query: {'rol': rol});
    return (json['data'] as List<dynamic>)
        .map((e) => Usuario.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<Usuario>> tecnicos() => listar(rol: 'Tecnico');
}
