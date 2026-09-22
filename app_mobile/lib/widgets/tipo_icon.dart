import 'package:flutter/material.dart';

/// Maps a "tipo de solicitud" name to a representative icon so list cards
/// have a visual anchor instead of just text.
IconData iconoParaTipo(String? tipo) {
  switch (tipo) {
    case 'Mantenimiento':
      return Icons.build_outlined;
    case 'Soporte Tecnológico':
      return Icons.computer_outlined;
    case 'Infraestructura':
      return Icons.apartment_outlined;
    case 'Equipamiento':
      return Icons.inventory_2_outlined;
    default:
      return Icons.assignment_outlined;
  }
}
