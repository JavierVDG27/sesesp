<?php

namespace App\Http\Controllers\Revision;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\HistorialModificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpedienteFirmaController extends Controller
{
    public function view(Expediente $expediente): StreamedResponse
    {
        abort_if(empty($expediente->pdf_firmado_path), 404);
        abort_unless(Storage::exists($expediente->pdf_firmado_path), 404);

        $path = $expediente->pdf_firmado_path;

        return Storage::response(
            $path,
            basename($path),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.basename($path).'"',
                'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'              => 'no-cache',
            ]
        );
    }

    public function upload(Request $request, Expediente $expediente)
    {
        abort_if(!in_array($expediente->estatus, [
            Expediente::ESTADO_PENDIENTE_FIRMA,
            Expediente::ESTADO_FIRMADO,
        ], true), 422);

        $data = $request->validate([
            'pdf_firmado' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5 MB
        ], [
            'pdf_firmado.max' => 'El PDF firmado no debe pesar más de 5 MB.',
            'pdf_firmado.mimes' => 'El archivo debe ser un PDF.',
        ]);

        // Si ya existía uno, lo borramos
        if ($expediente->pdf_firmado_path && Storage::exists($expediente->pdf_firmado_path)) {
            Storage::delete($expediente->pdf_firmado_path);
        }

        $safeFolio = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string)($expediente->folio ?? $expediente->id));

        $path = $data['pdf_firmado']->storeAs(
            "expedientes_firmados/{$expediente->id}",
            "Expediente_{$safeFolio}_FIRMADO.pdf"
        );

        $anterior = $expediente->estatus;

        $expediente->pdf_firmado_path = $path;
        $expediente->pdf_firmado_usuario_id = auth()->id();
        $expediente->pdf_firmado_at = now();
        $expediente->estatus = Expediente::ESTADO_FIRMADO;
        $expediente->save();

        HistorialModificacion::create([
            'expediente_id'   => $expediente->id,
            'usuario_id'      => auth()->id(),
            'estado_anterior' => $anterior,
            'estado_nuevo'    => Expediente::ESTADO_FIRMADO,
            'observaciones'   => 'Se cargó PDF firmado.',
        ]);

        return redirect()
            ->route('revision.show', $expediente->id)
            ->with('success', 'PDF firmado cargado. Expediente marcado como firmado.');
    }

    public function download(Expediente $expediente)
    {
        abort_if(empty($expediente->pdf_firmado_path), 404);
        abort_unless(Storage::exists($expediente->pdf_firmado_path), 404);

        return Storage::download($expediente->pdf_firmado_path);
    }

    public function destroy(Expediente $expediente)
    {
        // Solo admin/validador
        abort_if($expediente->estatus !== Expediente::ESTADO_FIRMADO, 422);
        abort_if(empty($expediente->pdf_firmado_path), 404);

        if (Storage::exists($expediente->pdf_firmado_path)) {
            Storage::delete($expediente->pdf_firmado_path);
        }

        $anterior = $expediente->estatus;

        $expediente->pdf_firmado_path = null;
        $expediente->pdf_firmado_usuario_id = null;
        $expediente->pdf_firmado_at = null;

        // Regresa a pendiente de firma
        $expediente->estatus = Expediente::ESTADO_PENDIENTE_FIRMA;
        $expediente->save();

        HistorialModificacion::create([
            'expediente_id'   => $expediente->id,
            'usuario_id'      => auth()->id(),
            'estado_anterior' => $anterior,
            'estado_nuevo'    => Expediente::ESTADO_PENDIENTE_FIRMA,
            'observaciones'   => 'Se eliminó el PDF firmado (revertido a pendiente de firma).',
        ]);

        return redirect()
            ->route('revision.show', $expediente->id)
            ->with('success', 'PDF firmado eliminado. El expediente volvió a Pendiente de firma.');
    }
}