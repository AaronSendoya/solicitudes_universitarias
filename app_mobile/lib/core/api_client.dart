import 'dart:convert';

import 'package:http/http.dart' as http;

import 'api_config.dart';
import 'api_exception.dart';
import 'token_storage.dart';

/// Thin wrapper around [http] that attaches the bearer token, serializes
/// JSON bodies and turns non-2xx responses into [ApiException].
///
/// Services default to [ApiClient.instance] so every screen shares the same
/// underlying [http.Client] (and therefore its HTTP keep-alive connection
/// pool) instead of paying for a fresh TCP handshake every time a screen or
/// dialog is built.
class ApiClient {
  static final ApiClient instance = ApiClient._();

  final TokenStorage _tokenStorage;
  final http.Client _http;

  ApiClient._({TokenStorage? tokenStorage, http.Client? httpClient})
    : _tokenStorage = tokenStorage ?? TokenStorage(),
      _http = httpClient ?? http.Client();

  /// For tests only — production code should use [ApiClient.instance].
  factory ApiClient.test({TokenStorage? tokenStorage, http.Client? httpClient}) {
    return ApiClient._(tokenStorage: tokenStorage, httpClient: httpClient);
  }

  Future<Map<String, String>> _headers({bool json = true}) async {
    final token = await _tokenStorage.read();
    return {
      'Accept': 'application/json',
      if (json) 'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  Uri _uri(String path, [Map<String, dynamic>? query]) {
    final Map<String, String> cleanQuery = {};
    query?.forEach((key, value) {
      if (value != null) cleanQuery[key] = value.toString();
    });
    return Uri.parse(
      '${ApiConfig.baseUrl}$path',
    ).replace(queryParameters: cleanQuery.isEmpty ? null : cleanQuery);
  }

  dynamic _decode(http.Response response) {
    if (response.statusCode == 204 || response.body.isEmpty) return null;
    return jsonDecode(response.body);
  }

  dynamic _handle(http.Response response) {
    final body = _decode(response);
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return body;
    }

    String message = 'Ocurrió un error inesperado.';
    Map<String, List<String>>? errors;

    if (body is Map<String, dynamic>) {
      message = body['message']?.toString() ?? message;
      if (body['errors'] is Map) {
        errors = (body['errors'] as Map).map(
          (k, v) => MapEntry(k.toString(), List<String>.from(v as List)),
        );
      }
    }

    if (response.statusCode == 401) {
      message = 'Sesión expirada. Vuelve a iniciar sesión.';
    }

    throw ApiException(response.statusCode, message, errors: errors);
  }

  Future<dynamic> get(String path, {Map<String, dynamic>? query}) async {
    final response = await _http.get(_uri(path, query), headers: await _headers());
    return _handle(response);
  }

  Future<dynamic> post(String path, {Object? body}) async {
    final response = await _http.post(
      _uri(path),
      headers: await _headers(),
      body: body == null ? null : jsonEncode(body),
    );
    return _handle(response);
  }

  Future<dynamic> patch(String path, {Object? body}) async {
    final response = await _http.patch(
      _uri(path),
      headers: await _headers(),
      body: body == null ? null : jsonEncode(body),
    );
    return _handle(response);
  }

  Future<dynamic> put(String path, {Object? body}) async {
    final response = await _http.put(
      _uri(path),
      headers: await _headers(),
      body: body == null ? null : jsonEncode(body),
    );
    return _handle(response);
  }

  Future<dynamic> delete(String path) async {
    final response = await _http.delete(_uri(path), headers: await _headers());
    return _handle(response);
  }

  /// Multipart upload (used for adjuntar evidencia).
  Future<dynamic> uploadFile(
    String path, {
    required String fieldName,
    required String filePath,
    String? fileName,
  }) async {
    final request = http.MultipartRequest('POST', _uri(path));
    request.headers.addAll(await _headers(json: false));
    request.files.add(
      await http.MultipartFile.fromPath(fieldName, filePath, filename: fileName),
    );

    final streamed = await _http.send(request);
    final response = await http.Response.fromStream(streamed);
    return _handle(response);
  }
}
