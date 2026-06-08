<?php

namespace App\Repositories\Reports;

interface ReportsRepository{
  public function getByFilter(array $filtros = [], string $sort = 'titulo', string $direction = 'desc');
  public function create($request);
  public function bulkInsertSubmission($submissoes);
  public function update($request, $uuid);
} 