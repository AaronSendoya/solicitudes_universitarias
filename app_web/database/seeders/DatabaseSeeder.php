<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $studentRole = Role::create(['name' => 'Student']);
        $managerRole = Role::create(['name' => 'Manager']);
        $technicianRole = Role::create(['name' => 'Technician']);

        // 2. Usuarios
        $admin = User::create([
            'name' => 'Administrador Gestor',
            'email' => 'admin@campus.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole($managerRole);

        $tecnico = User::create([
            'name' => 'Soporte Técnico',
            'email' => 'tecnico@campus.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $tecnico->assignRole($technicianRole);

        $estudiante = User::create([
            'name' => 'Estudiante Prueba',
            'email' => 'estudiante@campus.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $estudiante->assignRole($studentRole);

        // 3. Categorías
        $cat1 = Category::create(['name' => 'Soporte Tecnológico', 'description' => 'Problemas con hardware, software o red.']);
        $cat2 = Category::create(['name' => 'Mantenimiento de Infraestructura', 'description' => 'Problemas en aulas, laboratorios, baños, etc.']);
        $cat3 = Category::create(['name' => 'Equipamiento', 'description' => 'Solicitud de equipos o reparación de mobiliario.']);
        $cat4 = Category::create(['name' => 'Servicios Generales', 'description' => 'Limpieza, seguridad y otros.']);

        // 4. Solicitudes de Prueba
        Request::create([
            'user_id' => $estudiante->id,
            'category_id' => $cat1->id,
            'title' => 'No hay internet en el Laboratorio 3',
            'description' => 'Desde hace dos días no hay conexión en las PC del laboratorio.',
            'priority' => 'Alta',
            'status' => 'Pendiente',
        ]);

        Request::create([
            'user_id' => $estudiante->id,
            'category_id' => $cat2->id,
            'assigned_to' => $tecnico->id,
            'title' => 'Foco quemado en el aula 101',
            'description' => 'Se requiere cambiar el foco principal del aula.',
            'priority' => 'Baja',
            'status' => 'Asignado',
        ]);
        
        Request::create([
            'user_id' => $estudiante->id,
            'category_id' => $cat3->id,
            'assigned_to' => $tecnico->id,
            'title' => 'Proyector dañado',
            'description' => 'El proyector del auditorio no enciende, parpadea luz roja.',
            'priority' => 'Crítica',
            'status' => 'En Proceso',
        ]);
    }
}
