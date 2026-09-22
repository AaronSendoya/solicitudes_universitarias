import '../core/api_client.dart';
import '../core/token_storage.dart';
import '../models/usuario.dart';

class AuthResult {
  final Usuario usuario;
  final String token;

  AuthResult(this.usuario, this.token);
}

class AuthService {
  final ApiClient _client;
  final TokenStorage _tokenStorage;

  AuthService({ApiClient? client, TokenStorage? tokenStorage})
    : _client = client ?? ApiClient.instance,
      _tokenStorage = tokenStorage ?? TokenStorage();

  Future<AuthResult> login(String correo, String password) async {
    final json = await _client.post(
      '/auth/login',
      body: {'correo_institucional': correo, 'password': password},
    );
    final usuario = Usuario.fromJson(json['usuario'] as Map<String, dynamic>);
    final token = json['token'] as String;
    await _tokenStorage.save(token);
    return AuthResult(usuario, token);
  }

  Future<AuthResult> register({
    required String nombres,
    required String apellidos,
    required String correo,
    required String password,
    required String passwordConfirmation,
  }) async {
    final json = await _client.post(
      '/auth/register',
      body: {
        'nombres': nombres,
        'apellidos': apellidos,
        'correo_institucional': correo,
        'password': password,
        'password_confirmation': passwordConfirmation,
      },
    );
    final usuario = Usuario.fromJson(json['usuario'] as Map<String, dynamic>);
    final token = json['token'] as String;
    await _tokenStorage.save(token);
    return AuthResult(usuario, token);
  }

  Future<Usuario?> intentarSesionGuardada() async {
    final token = await _tokenStorage.read();
    if (token == null) return null;

    try {
      final json = await _client.get('/auth/me');
      return Usuario.fromJson(json['data'] as Map<String, dynamic>);
    } catch (_) {
      await _tokenStorage.clear();
      return null;
    }
  }

  Future<void> logout() async {
    try {
      await _client.post('/auth/logout');
    } catch (_) {
      // Ignore network errors on logout; clear the local token regardless.
    } finally {
      await _tokenStorage.clear();
    }
  }
}
