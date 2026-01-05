<?php

namespace App\Http\Controllers\Revision;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\HistorialModificacion;
use Illuminate\Http\Request;

class ExpedienteRevisionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'validacion'); // validacion | validados | rechazados
        $q   = trim((string)$request->get('q', ''));

        $query = Expediente::query()
            ->with([
                'usuario.institucion',
                'areaEjecutora',
                'historiales.usuario', // para "quién rechazó/aprobó"
            ]);

        // Tabs
        if ($tab === 'validados') {
            $query->where('estatus', Expediente::ESTADO_APROBADO);
        } elseif ($tab === 'rechazados') {
            $query->where('estatus', Expediente::ESTADO_RECHAZADO);
        } else {
            // default: en validación
            $query->where('estatus', Expediente::ESTADO_EN_VALIDACION);
        }

        // Búsqueda global
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('folio', 'like', "%{$q}%")
                ->orWhere('nombre_proyecto', 'like', "%{$q}%")
                ->orWhere('anio_ejercicio', 'like', "%{$q}%")
                ->orWhereHas('usuario', function ($u) use ($q) {
                    $u->where('nombres', 'like', "%{$q}%")
                        ->orWhere('apellido_paterno', 'like', "%{$q}%")
                        ->orWhere('apellido_materno', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('institucion', function ($i) use ($q) {
                            $i->where('nombre', 'like', "%{$q}%")
                            ->orWhere('siglas', 'like', "%{$q}%");
                        });
                });
            });
        }

        $expedientes = $query->latest('updated_at')->paginate(15)->withQueryString();

        // Contadores para tabs
        $counts = [
            'validacion' => Expediente::where('estatus', Expediente::ESTADO_EN_VALIDACION)->count(),
            'validados'  => Expediente::where('estatus', Expediente::ESTADO_APROBADO)->count(),
            'rechazados' => Expediente::where('estatus', Expediente::ESTADO_RECHAZADO)->count(),
        ];

        return view('revision.expedientes.index', compact('expedientes', 'tab', 'q', 'counts'));
    }


    public function show(Expediente $expediente)
    {
        // Permitir ver en validación, aprobado o rechazado (histórico)
        abort_if(!in_array($expediente->estatus, [
            Expediente::ESTADO_EN_VALIDACION,
            Expediente::ESTADO_APROBADO,
            Expediente::ESTADO_RECHAZADO,
        ], true), 404);

        $expediente->load([
            'usuario.institucion',
            'areaEjecutora',
            'detalle',
            'historiales.usuario',
        ]);

        return view('revision.expedientes.show', compact('expediente'));
    }

    public function aprobar(Request $request, Expediente $expediente)
    {
        abort_if($expediente->estatus !== Expediente::ESTADO_EN_VALIDACION, 422);

        $anterior = $expediente->estatus;
        $expediente->estatus = Expediente::ESTADO_APROBADO;
        $expediente->save();

        HistorialModificacion::create([
            'expediente_id'   => $expediente->id,
            'usuario_id'      => auth()->id(),
            'estado_anterior' => $anterior,
            'estado_nuevo'    => Expediente::ESTADO_APROBADO,
            'observaciones'   => 'Aprobado.',
        ]);

        return redirect()->route('revision.index')->with('success', 'Expediente aprobado.');
    }

    public function rechazar(Request $request, Expediente $expediente)
    {
        abort_if($expediente->estatus !== Expediente::ESTADO_EN_VALIDACION, 422);

        $data = $request->validate([
            'observaciones' => ['required', 'string', 'max:5000'],
        ]);

        $anterior = $expediente->estatus;
        $expediente->estatus = Expediente::ESTADO_RECHAZADO;
        $expediente->save();

        HistorialModificacion::create([
            'expediente_id'   => $expediente->id,
            'usuario_id'      => auth()->id(),
            'estado_anterior' => $anterior,
            'estado_nuevo'    => Expediente::ESTADO_RECHAZADO,
            'observaciones'   => $data['observaciones'],
        ]);

        return redirect()->route('revision.index')->with('success', 'Expediente rechazado con observaciones.');
    }
}
