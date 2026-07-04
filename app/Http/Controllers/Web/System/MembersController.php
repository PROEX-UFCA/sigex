<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function previewImport(Request $request)
    {
        $cacheKey = 'import_preview_' . auth()->id();

        if ($request->hasFile('csv')) {
            $request->validate([
                'csv' => 'required|file|mimes:csv,txt|max:10240'
            ], [
                'csv.mimes' => 'O arquivo precisa ser um CSV válido.'
            ]);

            $file = $request->file('csv');
            $handle = fopen($file->getRealPath(), "r");

            $headerLine = fgets($handle);
            if ($headerLine === false) {
                fclose($handle);
                return redirect()->back()->with("toast_error", "Arquivo CSV vazio ou inválido.");
            }

            $delimiter = substr_count($headerLine, ';') > substr_count($headerLine, ',') ? ';' : ',';

            $projectsData = [];
            $rowIndex = 1;
            $duplicadosIgnorados = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = array_map(fn($field) => trim(mb_convert_encoding($field, 'UTF-8', 'auto')), $row);

                $checkData = [
                    'id_projeto' => $row[0] ?? null,
                    'id_pessoa' => $row[1] ?? null,
                    'nome' => $row[2] ?? null,
                    'tipo_membro' => $row[3] ?? null,
                    'categoria_membro' => $row[4] ?? null,
                    'email' => $row[5] ?? null,
                    'status' => $row[6] ?? null,
                    'data_inicio' => $row[7] ?? null,
                    'data_fim' => $row[8] ?? null,
                    'tipo_vinculo' => $row[9] ?? null,
                ];

                if (Acao::where($checkData)->exists()) {
                    $duplicadosIgnorados++;
                    $rowIndex++;
                    continue; 
                }

                $errors = [];
                if (empty($row[0])) $errors['id_projeto'] = 'Campo obrigatório';
                if (empty($row[1])) $errors['ano'] = 'Campo obrigatório';
                if (empty($row[2])) $errors['titulo'] = 'Campo obrigatório';
                if (empty($row[3])) $errors['modalidade_edital'] = 'Campo obrigatório';
                if (!isset($row[4]) || trim((string) $row[4]) === '') $errors['bolsas_solicitadas'] = 'Campo obrigatório';
                if (!isset($row[5]) || trim((string) $row[5]) === '') $errors['bolsas_concedidas'] = 'Campo obrigatório';
                if (empty($row[6])) $errors['financiamento_interno'] = 'Campo obrigatório';
                if (empty($row[7])) $errors['financiamento_externo'] = 'Campo obrigatório';
                if (empty($row[8])) $errors['situacao'] = 'Campo obrigatório';
                if (empty($row[9])) $errors['data_cadastro'] = 'Campo obrigatório';
                if (empty($row[10])) $errors['data_inicio'] = 'Campo obrigatório';
                if (empty($row[11])) $errors['data_fim'] = 'Campo obrigatório';
                if (empty($row[12])) $errors['data_atualizacao'] = 'Campo obrigatório';
                if (empty($row[13])) $errors['centro_departamento_sigla'] = 'Campo obrigatório';
                if (empty($row[14])) $errors['tipo_acao'] = 'Campo obrigatório';
                if (empty($row[15])) $errors['area_tematica'] = 'Campo obrigatório';
                // if (empty($row[16])) $errors['resumo'] = 'Campo obrigatório';
                // if (empty($row[17])) $errors['palavras_chave'] = 'Campo obrigatório';
                // if (empty($row[18])) $errors['ods'] = 'Campo obrigatório';
                if (empty($row[19])) $errors['contexto'] = 'Campo obrigatório';

                $projectsData[$rowIndex] = [
                    'id_projeto' => $row[0] ?? null,
                    'ano' => $row[1] ?? null,
                    'titulo' => $row[2] ?? null,
                    'modalidade_edital' => $row[3] ?? null,
                    'bolsas_solicitadas' => $row[4] ?? null,
                    'bolsas_concedidas' => $row[5] ?? null,
                    'financiamento_interno' => $row[6] ?? null,
                    'financiamento_externo' => $row[7] ?? null,
                    'situacao' => $row[8] ?? null,
                    'data_cadastro' => $row[9] ?? null,
                    'data_inicio' => $row[10] ?? null,
                    'data_fim' => $row[11] ?? null,
                    'data_atualizacao' => $row[12] ?? null,
                    'centro_departamento_sigla' => $row[13] ?? null,
                    'tipo_acao' => $row[14] ?? null,
                    'area_tematica' => $row[15] ?? null,
                    'resumo' => $row[16] ?? null,
                    'palavras_chave' => $row[17] ?? null,
                    'ods' => $row[18] ?? null,
                    'contexto' => $row[19] ?? null,
                    'errors' => $errors,
                    'row_index' => $rowIndex
                ];
                $rowIndex++;
            }
            fclose($handle);

            Cache::put($cacheKey, [
                'projects' => $projectsData,
                'duplicados' => $duplicadosIgnorados
            ], now()->addHours(2));

        } else {
            $cacheData = Cache::get($cacheKey);
            if (!$cacheData || empty($cacheData['projects'])) {
                return redirect()->route('actions.index')->with('toast_error', 'A sessão de importação expirou ou não há dados.');
            }
            
            $allProjects = $cacheData['projects'];
            $duplicadosIgnorados = $cacheData['duplicados'];
            $cacheFoiAtualizado = false;

            if ($request->filled('deleted_indexes')) {
                $deletedIndexes = explode(',', $request->deleted_indexes);
                foreach ($deletedIndexes as $idx) {
                    if (isset($allProjects[$idx])) {
                        unset($allProjects[$idx]);
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($request->has('projects')) {
                foreach ($request->projects as $index => $submittedData) {
                    if (isset($allProjects[$index])) {
                        $allProjects[$index] = array_merge($allProjects[$index], $submittedData);
                        
                        $errors = [];
                        if (empty($allProjects[$index]['id_projeto'])) $errors['id_projeto'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['ano'])) $errors['ano'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['titulo'])) $errors['titulo'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['modalidade_edital'])) $errors['modalidade_edital'] = 'Campo obrigatório';
                        if (!isset($allProjects[$index]['bolsas_solicitadas']) || trim((string) $allProjects[$index]['bolsas_solicitadas']) === '') $errors['bolsas_solicitadas'] = 'Campo obrigatório';
                        if (!isset($allProjects[$index]['bolsas_concedidas']) || trim((string) $allProjects[$index]['bolsas_concedidas']) === '') $errors['bolsas_concedidas'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['financiamento_interno'])) $errors['financiamento_interno'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['financiamento_externo'])) $errors['financiamento_externo'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['situacao'])) $errors['situacao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_cadastro'])) $errors['data_cadastro'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_inicio'])) $errors['data_inicio'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_fim'])) $errors['data_fim'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['data_atualizacao'])) $errors['data_atualizacao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['centro_departamento_sigla'])) $errors['centro_departamento_sigla'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['tipo_acao'])) $errors['tipo_acao'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['area_tematica'])) $errors['area_tematica'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['resumo'])) $errors['resumo'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['palavras_chave'])) $errors['palavras_chave'] = 'Campo obrigatório';
                        // if (empty($allProjects[$index]['ods'])) $errors['ods'] = 'Campo obrigatório';
                        if (empty($allProjects[$index]['contexto'])) $errors['contexto'] = 'Campo obrigatório';

                        $allProjects[$index]['errors'] = $errors;
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($cacheFoiAtualizado) {
                Cache::put($cacheKey, [
                    'projects' => $allProjects,
                    'duplicados' => $duplicadosIgnorados
                ], now()->addHours(2));
            }

            $projectsData = $allProjects;
        }

        $collection = collect($projectsData)->sortByDesc(fn($project) => !empty($project['errors']))->values();
        
        $totalErrors = $collection->filter(fn($item) => count($item['errors']) > 0)->count();

        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedProjects = new LengthAwarePaginator(
            $currentItems, 
            $collection->count(), 
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pages.actions.preview', compact('paginatedProjects', 'totalErrors', 'duplicadosIgnorados'));
    }
}
