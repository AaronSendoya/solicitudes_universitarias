import 'dart:io' show Platform;

import 'package:flutter/foundation.dart' show kIsWeb;

/// Base URL of the Campus Connect Laravel API.
///
/// - Android emulator can't reach the host's "localhost" directly; it must
///   use the special alias 10.0.2.2.
/// - iOS simulator / desktop / web can reach the host via 127.0.0.1.
/// - A physical phone needs the dev machine's LAN IP instead (e.g.
///   192.168.1.50) since it isn't the same "localhost" as the API server.
///   Change [_lanOverride] when testing on a real device.
class ApiConfig {
  static const String? _lanOverride = null; // e.g. '192.168.1.50'

  static String get baseUrl {
    if (_lanOverride != null) {
      return 'http://$_lanOverride:8000/api';
    }
    if (!kIsWeb && Platform.isAndroid) {
      return 'http://10.0.2.2:8000/api';
    }
    return 'http://127.0.0.1:8000/api';
  }

  /// Root URL (without /api) used to resolve storage/file URLs when needed.
  static String get storageBaseUrl => baseUrl.replaceFirst('/api', '');
}
