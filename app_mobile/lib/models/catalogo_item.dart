/// Generic {id, nombre} item shared by estados, tipos, prioridades,
/// ubicaciones and roles.
class CatalogoItem {
  final int id;
  final String nombre;
  final int? nivel;

  CatalogoItem({required this.id, required this.nombre, this.nivel});

  factory CatalogoItem.fromJson(Map<String, dynamic> json) {
    return CatalogoItem(
      id: json['id'] as int,
      nombre: json['nombre'] as String,
      nivel: json['nivel'] as int?,
    );
  }
}
