<?php

namespace App\Repositories\Actions;

interface ActionsRepository{
  public function getByFilter(array $filtros = []);
} 