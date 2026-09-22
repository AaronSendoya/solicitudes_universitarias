import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'reportes/reportes_screen.dart';
import 'solicitudes/crear_solicitud_screen.dart';
import 'solicitudes/solicitudes_list_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _tabIndex = 0;

  Future<void> _confirmarCerrarSesion(BuildContext context) async {
    final confirmar = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Cerrar sesión'),
        content: const Text('¿Seguro que deseas cerrar sesión?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancelar')),
          FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Salir')),
        ],
      ),
    );
    if (confirmar == true && context.mounted) {
      await context.read<AuthProvider>().logout();
    }
  }

  @override
  Widget build(BuildContext context) {
    final usuario = context.watch<AuthProvider>().usuario!;

    final esAdministrativo = usuario.esAdministrativo;
    final tituloBandeja = usuario.esEstudiante
        ? 'Mis solicitudes'
        : usuario.esTecnico
        ? 'Asignadas a mí'
        : 'Bandeja de solicitudes';

    final paginas = <Widget>[
      SolicitudesListScreen(titulo: tituloBandeja),
      if (esAdministrativo) const ReportesScreen(),
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(_tabIndex == 0 ? '' : 'Reportes de gestión'),
        backgroundColor: _tabIndex == 0 ? AppColors.primary : null,
        foregroundColor: _tabIndex == 0 ? Colors.white : null,
        systemOverlayStyle: _tabIndex == 0 ? SystemUiOverlayStyle.light : null,
        actions: [
          PopupMenuButton<String>(
            icon: const CircleAvatar(child: Icon(Icons.person)),
            onSelected: (value) {
              if (value == 'logout') _confirmarCerrarSesion(context);
            },
            itemBuilder: (context) => [
              PopupMenuItem(
                enabled: false,
                child: Text(
                  '${usuario.nombreCompleto}\n${usuario.rol}',
                  style: const TextStyle(fontWeight: FontWeight.w600),
                ),
              ),
              const PopupMenuDivider(),
              const PopupMenuItem(
                value: 'logout',
                child: ListTile(
                  leading: Icon(Icons.logout),
                  title: Text('Cerrar sesión'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
            ],
          ),
        ],
      ),
      // Rebuilt (not IndexedStack) on purpose: each tab visit should fetch
      // fresh data — e.g. Reportes must reflect changes just made in Bandeja.
      body: paginas[_tabIndex],
      bottomNavigationBar: esAdministrativo
          ? NavigationBar(
              selectedIndex: _tabIndex,
              onDestinationSelected: (i) => setState(() => _tabIndex = i),
              destinations: const [
                NavigationDestination(icon: Icon(Icons.inbox_outlined), label: 'Bandeja'),
                NavigationDestination(icon: Icon(Icons.bar_chart_outlined), label: 'Reportes'),
              ],
            )
          : null,
      floatingActionButton: usuario.esEstudiante
          ? FloatingActionButton.extended(
              onPressed: () => Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const CrearSolicitudScreen()),
              ),
              icon: const Icon(Icons.add),
              label: const Text('Nueva solicitud'),
            )
          : null,
    );
  }
}
