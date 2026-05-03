<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;

class DocenteSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = (string) $request->query('q', '');

        $query = Docente::query()->select(['id', 'apellido', 'nombre', 'dni']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('apellido', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%");
            });
        }

        $results = $query->orderBy('apellido')->limit(25)->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'text' => trim(($d->apellido ?? '') . ', ' . ($d->nombre ?? '')) . ($d->dni ? ' (' . $d->dni . ')' : ''),
            ];
        });

        return response()->json($results);
    }
}
