class ConteoItem {
  final String nombre;
  final int total;

  ConteoItem({required this.nombre, required this.total});

  factory ConteoItem.fromJson(Map<String, dynamic> json) {
    return ConteoItem(nombre: json['nombre'] as String, total: json['total'] as int);
  }
}

class ReporteGestion {
  final int totalSolicitudes;
  final double? tiempoPromedioResolucionHoras;
  final List<ConteoItem> porEstado;
  final List<ConteoItem> porTipo;
  final List<ConteoItem> porPrioridad;
  final List<ConteoItem> porTecnico;

  ReporteGestion({
    required this.totalSolicitudes,
    this.tiempoPromedioResolucionHoras,
    required this.porEstado,
    required this.porTipo,
    required this.porPrioridad,
    required this.porTecnico,
  });

  factory ReporteGestion.fromJson(Map<String, dynamic> json) {
    List<ConteoItem> parseLista(String key) {
      return (json[key] as List<dynamic>? ?? [])
          .map((e) => ConteoItem.fromJson(e as Map<String, dynamic>))
          .toList();
    }

    return ReporteGestion(
      totalSolicitudes: json['total_solicitudes'] as int? ?? 0,
      tiempoPromedioResolucionHoras: (json['tiempo_promedio_resolucion_horas'] as num?)
          ?.toDouble(),
      porEstado: parseLista('por_estado'),
      porTipo: parseLista('por_tipo'),
      porPrioridad: parseLista('por_prioridad'),
      porTecnico: parseLista('por_tecnico'),
    );
  }
}
