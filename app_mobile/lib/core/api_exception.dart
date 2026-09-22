class ApiException implements Exception {
  final int statusCode;
  final String message;
  final Map<String, List<String>>? errors;

  ApiException(this.statusCode, this.message, {this.errors});

  /// First validation message, if any, otherwise the general [message].
  String get friendlyMessage {
    if (errors != null && errors!.isNotEmpty) {
      return errors!.values.first.first;
    }
    return message;
  }

  @override
  String toString() => 'ApiException($statusCode): $message';
}
