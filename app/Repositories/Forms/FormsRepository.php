<?php

namespace App\Repositories\Forms;

interface FormsRepository{
  public function getByFilter(array $filtros = [], string $sort = 'titulo', string $direction = 'desc');
  public function create($request);
  public function update($request, $uuid);
  public function getSessionById($uuid);
  public function updateSession($request, $uuid);
  public function createQuestion($request, $uuid);
} 