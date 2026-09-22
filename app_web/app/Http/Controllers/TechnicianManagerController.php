<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TechnicianManagerController extends Controller
{
    /**
     * Lista todos los técnicos registrados.
     */
    public function index()
    {
        $technicians = User::role('Technician')->orderBy('name')->paginate(15);
        return view('manager.technicians.index', compact('technicians'));
    }

    /**
     * Muestra el formulario para crear un técnico.
     */
    public function create()
    {
        return view('manager.technicians.create');
    }

    /**
     * Almacena un nuevo técnico en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole('Technician');

        return redirect()->route('manager.technicians.index')->with('success', 'Técnico registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un técnico.
     */
    public function edit($id)
    {
        $technician = User::role('Technician')->findOrFail($id);
        return view('manager.technicians.edit', compact('technician'));
    }

    /**
     * Actualiza los datos de un técnico (excepto contraseña y rol).
     */
    public function update(Request $request, $id)
    {
        $technician = User::role('Technician')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$technician->id],
        ]);

        $technician->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('manager.technicians.index')->with('success', 'Técnico actualizado correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo de un técnico.
     */
    public function toggleActive($id)
    {
        $technician = User::role('Technician')->findOrFail($id);
        $technician->update([
            'is_active' => !$technician->is_active,
        ]);

        $statusMessage = $technician->is_active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Técnico {$statusMessage} correctamente.");
    }
}
