import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class EstadoChip extends StatelessWidget {
  final String estado;
  final bool grande;

  const EstadoChip({super.key, required this.estado, this.grande = false});

  Color _color() {
    switch (estado) {
      case 'Abierta':
        return AppColors.abierta;
      case 'En Proceso':
        return AppColors.enProceso;
      case 'Pendiente de Revisión':
        return AppColors.pendiente;
      case 'Cerrada':
        return AppColors.cerrada;
      default:
        return Colors.grey;
    }
  }

  IconData _icono() {
    switch (estado) {
      case 'Abierta':
        return Icons.radio_button_unchecked;
      case 'En Proceso':
        return Icons.sync;
      case 'Pendiente de Revisión':
        return Icons.rate_review_outlined;
      case 'Cerrada':
        return Icons.check_circle_outline;
      default:
        return Icons.circle_outlined;
    }
  }

  @override
  Widget build(BuildContext context) {
    final color = _color();
    return Container(
      padding: EdgeInsets.symmetric(horizontal: grande ? 14 : 10, vertical: grande ? 8 : 5),
      decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(20)),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(_icono(), size: grande ? 16 : 13, color: Colors.white),
          const SizedBox(width: 5),
          Text(
            estado,
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w700,
              fontSize: grande ? 13 : 11.5,
            ),
          ),
        ],
      ),
    );
  }
}
