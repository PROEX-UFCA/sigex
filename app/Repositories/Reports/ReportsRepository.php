<?php

namespace App\Repositories\Reports;

interface ReportsRepository{
  public function getByFilter(array $filtros = [], string $sort = 'titulo', string $direction = 'desc');
  public function create($request);
  public function bulkInsertSubmission($submissoes);
  public function update($request, $uuid);
  public function getById($uuid);
  public function getSubmissionById($uuid);
  public function getSubmissionsByIdReport($uuid, array $filtros = [], string $sort = 'titulo', string $direction = 'desc');
  public function getSubmissionsForDownload($whatReport, $quais);
} 