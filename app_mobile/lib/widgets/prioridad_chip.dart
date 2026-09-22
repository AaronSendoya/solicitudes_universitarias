import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class PrioridadChip extends StatelessWidget {
  final String prioridad;

  const PrioridadChip({super.key, required this.prioridad});

  Color _color() {
    switch (prioridad) {
      case 'Alta':
        return AppColors.prioridadAlta;
      case 'Media':
        return AppColors.prioridadMedia;
      case 'Baja':
        return AppColors.prioridadBaja;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final color = _color();
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(Icons.flag, size: 14, color: color),
        const SizedBox(width: 4),
        Text(prioridad, style: TextStyle(color: color, fontWeight: FontWeight.w600, fontSize: 12)),
      ],
    );
  }
}
