<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Models\Acao;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class AcaoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Acao::query();

            if ($request->has('titulo')) {
                $query->where('titulo', 'like', '%' . $request->input('titulo') . '%');
            }

            if ($request->has('centro_departamento')) {
                $query->where('centro_departamento', 'like', '%' . $request->input('centro_departamento') . '%');
            }

            if ($request->has('area_tematica')) {
                $query->where('area_tematica', 'like', '%' . $request->input('area_tematica') . '%');
            }

            if ($request->has('modalidade')) {
                $query->where('modalidade', 'like', '%' . $request->input('modalidade') . '%');
            }

            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->input('per_page', 10);

            $acoes = $query->paginate($perPage);

            $acoes->appends($request->all());

            return response()->json([
                'success' => true,
                'data' => $acoes
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return $this->handleError($th, 'Erro ao listar ações.');
        }
    }

    private function handleError(Throwable $th, string $message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => config('app.debug') ? $th->getMessage() : 'Internal Server Error'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
