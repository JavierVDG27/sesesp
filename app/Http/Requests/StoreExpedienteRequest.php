<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'anio_ejercicio' => ['required','integer','min:2000','max:2100'],
            'entidad'        => ['nullable','string','max:10'],

            'area_ejecutora' => ['nullable','string','max:10'],

            // Eje Programa Subprograma (obligatorio)
            'eje'         => ['required','string','max:10'],
            'programa'    => ['required','string','max:10'],
            'subprograma' => ['required','string','max:10'],

            // Datos generales
            'nombre_proyecto' => ['required','string','max:255'],
            'dependencia'     => ['required','string','max:255'],
            'tema'            => ['nullable','string','max:255'],

            // Clasificador
            'capitulo'          => ['required','string','max:10'],
            'concepto'          => ['required','string','max:10'],
            'partida_generica'  => ['required','string','max:10'],
            'bien'              => ['required','string','max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'eje.required' => 'Selecciona un Eje asignado.',
            'programa.required' => 'Selecciona un Programa.',
            'subprograma.required' => 'Selecciona un Subprograma.',
            'capitulo.required' => 'Selecciona un Capítulo.',
            'concepto.required' => 'Selecciona un Concepto.',
            'partida_generica.required' => 'Selecciona una Partida genérica.',
            'bien.required' => 'Selecciona un Bien (partida específica).',
        ];
    }
}
