<?php

namespace App\Repositories\Forms;

interface FormsRepository{
  public function getByFilter(array $filtros = []);
  public function create($request);
  public function update($request, $uuid);
} 