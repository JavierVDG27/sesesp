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
        /**
         * Tabs:
         * - validacion        => en validación
         * - pendiente_firma   => validado, pendiente por subir firmado
         * - firmados          => ya con PDF firmado
         * - rechazados        => rechazados con observaciones
         *
         * (compat) validados => antiguos "aprobado" si aún existen
         */
        $tab = (string) $request->get('tab', 'validacion');
        $q   = trim((string) $request->get('q', ''));

        $query = Expediente::query()
            ->with([
                'usuario.institucion',
                'areaEjecutora',
                'historiales.usuario',
            ]);

        // ===== Tabs =====
        switch ($tab) {
            case 'pendiente_firma':
                $query->where('estatus', Expediente::ESTADO_PENDIENTE_FIRMA);
                break;

            case 'firmados':
                $query->where('estatus', Expediente::ESTADO_FIRMADO);
                break;

            case 'rechazados':
                $query->where('estatus', Expediente::ESTADO_RECHAZADO);
                break;

            case 'validados': // antiguos "aprobado"
                $query->where('estatus', Expediente::ESTADO_APROBADO);
                break;

            case 'validacion':
            default:
                $tab = 'validacion';
                $query->where('estatus', Expediente::ESTADO_EN_VALIDACION);
                break;
        }

        // ===== Búsqueda global =====
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

        $expedientes = $query
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        // ===== Contadores para tabs (siempre globales) =====
        $counts = [
            'validacion'       => Expediente::where('estatus', Expediente::ESTADO_EN_VALIDACION)->count(),
            'pendiente_firma'  => Expediente::where('estatus', Expediente::ESTADO_PENDIENTE_FIRMA)->count(),
            'firmados'         => Expediente::where('estatus', Expediente::ESTADO_FIRMADO)->count(),
            'rechazados'       => Expediente::where('estatus', Expediente::ESTADO_RECHAZADO)->count(),

            'validados'        => Expediente::where('estatus', Expediente::ESTADO_APROBADO)->count(),
        ];

        return view('revision.expedientes.index', compact('expedientes', 'tab', 'q', 'counts'));
    }

    public function show(Expediente $expediente)
    {
        // Permitir ver en validación y también posteriores (firma/firmado) y rechazados (histórico)
        abort_if(!in_array($expediente->estatus, [
            Expediente::ESTADO_EN_VALIDACION,
            Expediente::ESTADO_PENDIENTE_FIRMA,
            Expediente::ESTADO_FIRMADO,
            Expediente::ESTADO_RECHAZADO,

            // compat
            Expediente::ESTADO_APROBADO,
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
        // Solo se puede aprobar si está EN VALIDACIÓN
        abort_if($expediente->estatus !== Expediente::ESTADO_EN_VALIDACION, 422);

        $anterior = $expediente->estatus;

        // En vez de "aprobado", pasa a pendiente de firma
        $expediente->estatus = Expediente::ESTADO_PENDIENTE_FIRMA;
        $expediente->save();

        HistorialModificacion::create([
            'expediente_id'   => $expediente->id,
            'usuario_id'      => auth()->id(),
            'estado_anterior' => $anterior,
            'estado_nuevo'    => Expediente::ESTADO_PENDIENTE_FIRMA,
            'observaciones'   => 'Validado. Pendiente de firma.',
        ]);

        return redirect()
            ->route('revision.index', ['tab' => 'pendiente_firma'])
            ->with('success', 'Expediente validado. Quedó pendiente de firma.');
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

        return redirect()
            ->route('revision.index', ['tab' => 'rechazados'])
            ->with('success', 'Expediente rechazado con observaciones.');
    }
}