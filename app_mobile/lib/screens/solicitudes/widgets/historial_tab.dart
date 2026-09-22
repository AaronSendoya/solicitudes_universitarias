import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../models/solicitud.dart';
import '../../../widgets/estado_vacio.dart';

class HistorialTab extends StatelessWidget {
  final Solicitud solicitud;

  const HistorialTab({super.key, required this.solicitud});

  @override
  Widget build(BuildContext context) {
    final historial = solicitud.historial;
    if (historial.isEmpty) {
      return const EstadoVacio(
        mensaje: 'Sin movimientos registrados todavía.',
        icono: Icons.history,
      );
    }

    final formato = DateFormat('dd/MM/yyyy HH:mm');

    return ListView.separated(
      padding: const EdgeInsets.all(12),
      itemCount: historial.length,
      separatorBuilder: (_, _) => const Divider(),
      itemBuilder: (context, i) {
        final h = historial[i];
        return ListTile(
          contentPadding: EdgeInsets.zero,
          leading: const Icon(Icons.fiber_manual_record, size: 14),
          title: Text(h.campoLegible, style: const TextStyle(fontWeight: FontWeight.w600)),
          subtitle: Text(
            h.valorAnterior != null
                ? '${h.valorAnterior} → ${h.valorNuevo ?? "-"}'
                : (h.valorNuevo ?? ''),
          ),
          trailing: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              if (h.fechaCambio != null)
                Text(
                  formato.format(h.fechaCambio!),
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
                ),
              if (h.usuario != null)
                Text(
                  h.usuario!.nombreCompleto,
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
                ),
            ],
          ),
        );
      },
    );
  }
}
