<?php

namespace App\Http\Controllers;

use App\DTOs\EmpleadoCreateDTO;
use App\Models\ConfiguracionNomina;
use App\Models\Empleado;
use App\Services\EmpleadoService;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function __construct(
        private EmpleadoService $empleadoService
    ) {}

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
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'required|email|unique:usuarios,email',
            'rol'      => 'required|in:FOTOGRAFO,ADMINISTRADOR',
            'estado'   => 'required|in:ACTIVO,INACTIVO',
            'experiencia_laboral' => 'nullable|string',
            'salario_base' => 'nullable|numeric|min:0',
        ]);

        $dto      = EmpleadoCreateDTO::fromRequest($request);
        $resultado = $this->empleadoService->crear($dto);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado creado correctamente.')
            ->with('nueva_password', $resultado['password_inicial'])
            ->with('nuevo_email', $request->email);
    }

    public function show(Empleado $empleado)
    {
        $empleado->load('usuario.persona', 'fotografo');
        $configDefault = ConfiguracionNomina::actual()->salario_base;
        return view('admin.empleados.show', compact('empleado', 'configDefault'));
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('usuario.persona', 'fotografo');
        $configDefault = ConfiguracionNomina::actual()->salario_base;
        return view('admin.empleados.edit', compact('empleado', 'configDefault'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'required|email|unique:usuarios,email,' . $empleado->usuario->id,
            'rol'      => 'required|in:FOTOGRAFO,ADMINISTRADOR',
            'estado'   => 'required|in:ACTIVO,INACTIVO',
            'password' => 'nullable|string|min:8',
            'salario_base' => 'nullable|numeric|min:0'
        ]);

        $dto = EmpleadoCreateDTO::fromRequest($request);
        $this->empleadoService->actualizar($empleado, $dto);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $this->empleadoService->eliminar($empleado);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
