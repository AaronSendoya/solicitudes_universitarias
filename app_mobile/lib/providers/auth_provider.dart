import 'dart:async';

import 'package:flutter/foundation.dart';

import '../core/api_exception.dart';
import '../models/usuario.dart';
import '../services/auth_service.dart';
import '../services/catalogo_service.dart';

enum EstadoAuth { cargando, autenticado, invitado }

class AuthProvider extends ChangeNotifier {
  final AuthService _authService;

  AuthProvider({AuthService? authService}) : _authService = authService ?? AuthService() {
    _restaurarSesion();
  }

  EstadoAuth estado = EstadoAuth.cargando;
  Usuario? usuario;
  String? errorMensaje;

  Future<void> _restaurarSesion() async {
    final u = await _authService.intentarSesionGuardada();
    usuario = u;
    estado = u != null ? EstadoAuth.autenticado : EstadoAuth.invitado;
    notifyListeners();
    if (u != null) unawaited(CatalogoService.precargar());
  }

  Future<bool> login(String correo, String password) async {
    errorMensaje = null;
    try {
      final resultado = await _authService.login(correo, password);
      usuario = resultado.usuario;
      estado = EstadoAuth.autenticado;
      notifyListeners();
      unawaited(CatalogoService.precargar());
      return true;
    } catch (e) {
      errorMensaje = _mensajeDeError(e);
      notifyListeners();
      return false;
    }
  }

  Future<bool> register({
    required String nombres,
    required String apellidos,
    required String correo,
    required String password,
    required String passwordConfirmation,
  }) async {
    errorMensaje = null;
    try {
      final resultado = await _authService.register(
        nombres: nombres,
        apellidos: apellidos,
        correo: correo,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );
      usuario = resultado.usuario;
      estado = EstadoAuth.autenticado;
      notifyListeners();
      unawaited(CatalogoService.precargar());
      return true;
    } catch (e) {
      errorMensaje = _mensajeDeError(e);
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await _authService.logout();
    usuario = null;
    estado = EstadoAuth.invitado;
    notifyListeners();
  }

  String _mensajeDeError(Object e) {
    if (e is ApiException) return e.friendlyMessage;
    return 'No se pudo conectar con el servidor.';
  }
}
