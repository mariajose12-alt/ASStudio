<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Fotografo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('usuario.persona')->paginate(10);
        return view('admin.empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('admin.empleados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'required|email|unique:usuarios,email',
            'rol'           => 'required|in:FOTOGRAFO,ADMINISTRADOR',
            'estado'        => 'required|in:ACTIVO,INACTIVO',
            // Solo si es fotógrafo
            'experiencia_laboral' => 'nullable|string',
        ]);

        // 1. Crear la Persona
        $persona = Persona::create([
            'nombre'   => $data['nombre'],
            'apellido' => $data['apellido'],
            'telefono' => $data['telefono'] ?? null,
        ]);

        // 2. Generar contraseña aleatoria y crear el Usuario
        $passwordPlano = Str::random(10);

        $usuario = Usuario::create([
            'persona_id' => $persona->id,
            'email'      => $data['email'],
            'contrasena' => Hash::make($passwordPlano),
            'estado'     => $data['estado'],
        ]);

        // 3. Crear el Empleado
        $empleado = Empleado::create([
            'usuario_id' => $usuario->id,
            'rol'        => $data['rol'],
        ]);

        // 4. Si es fotógrafo, crear el registro en fotografos
        if ($data['rol'] === 'FOTOGRAFO') {
            Fotografo::create([
                'empleado_id'         => $empleado->id,
                'experiencia_laboral' => $data['experiencia_laboral'] ?? null,
                'certificaciones'     => array_filter(array_map('trim', explode(',', $request->input('certificaciones', '')))),
            ]);
        }

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado creado correctamente.')
            ->with('nueva_password', $passwordPlano)
            ->with('nuevo_email', $usuario->email);
    }

    public function show(Empleado $empleado)
    {
        $empleado->load('usuario.persona', 'fotografo');
        return view('admin.empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('usuario.persona', 'fotografo');
        return view('admin.empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'required|email|unique:usuarios,email,' . $empleado->usuario->id,
            'rol'           => 'required|in:FOTOGRAFO,ADMINISTRADOR',
            'estado'        => 'required|in:ACTIVO,INACTIVO',
            'experiencia_laboral' => 'nullable|string',
            'password'            => 'nullable|string|min:8',
        ]);

        // Actualizar Persona
        $empleado->usuario->persona->update([
            'nombre'   => $data['nombre'],
            'apellido' => $data['apellido'],
            'telefono' => $data['telefono'] ?? null,
        ]);

        // Actualizar Usuario
        $usuarioData = [
            'email'  => $data['email'],
            'estado' => $data['estado'],
        ];

        // Solo actualizar contraseña si se envió
        if (!empty($data['password'])) {
            $usuarioData['contrasena'] = Hash::make($data['password']);
        }

        $empleado->usuario->update($usuarioData);

        // Actualizar Empleado
        $empleado->update(['rol' => $data['rol']]);

        // Manejar el registro de Fotografo
        if ($data['rol'] === 'FOTOGRAFO') {
            Fotografo::updateOrCreate(
                ['empleado_id' => $empleado->id],
                [
                    'experiencia_laboral' => $data['experiencia_laboral'] ?? null,
                    'certificaciones' => array_filter(array_map('trim', explode(',', $request->input('certificaciones', '')))),
                ]
            );
        }

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // Al eliminar el empleado, por cascade en la BD se eliminan
        // fotografo, usuario y persona
        $usuario = $empleado->usuario;
        $persona = $usuario->persona;

        $empleado->delete();
        $usuario->delete();
        $persona->delete();

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
