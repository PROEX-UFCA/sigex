<?php

namespace App\Repositories\Actions;

interface ActionsRepository{
  public function getByFilter(array $filtros = []);
  public function getAllByUuid($uuid);
  public function getByUserUuid($user_uuid, $uuid);
  public function create($request);
} 