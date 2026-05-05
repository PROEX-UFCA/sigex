<?php

namespace App\Repositories\Forms;

interface FormsRepository{
  public function getByFilter(array $filtros = []);
  public function create($request);
  public function update($request, $uuid);
  public function getSessionById($uuid);
  public function updateSession($request, $uuid);
  public function createQuestion($request, $uuid);
} 