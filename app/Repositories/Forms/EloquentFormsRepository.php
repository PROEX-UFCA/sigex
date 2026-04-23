<?php

namespace App\Repositories\Forms;

use App\Models\Formulario;
use App\Models\Secao;

class EloquentFormsRepository implements FormsRepository
{
    public function getByFilter(array $filtros = [])
    {
        $query = Formulario::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('titulo', 'like', "%{$search}%");
            });
        });

        return $query->orderBy('created_at', 'desc')->paginate(30);
    }

    public function create($request)
    {
        $form = Formulario::create([
            'titulo' => $request->titulo, 
            'descricao' => $request->descricao
        ]);

        for ($i=1; $i <= $request->numero_secoes; $i++) { 
            Secao::create([
                "id_formulario" => $form->id,
                'titulo' => "Seção ".$i,
                'descricao' => "Descrição da seção ".$i,
                'ordem' => $i
            ]);
        }

        return $form;
    }

    public function update($request, $uuid)
    {
        $form = Formulario::findOrFail($uuid);

        $form->titulo = $request->titulo;
        $form->descricao = $request->descricao;
        $form->status = $request->status;
        if ($form->published == 0) {
            $form->published = $request->status;
        }
        
        $form->save();
        
        return $form;
    }
}
