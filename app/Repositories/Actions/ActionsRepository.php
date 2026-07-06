<?php

namespace App\Repositories\Actions;

interface ActionsRepository{
  public function getByFilter(array $filtros = [], string $sort = 'ano', string $direction = 'desc');
  public function getAllByUuid($uuid, array $filtros = []);
  public function getByUserUuid($user_uuid, $uuid);
  public function create($request);
  public function createTeam($request, $id_acao, $action);
  public function createSchedule($request, $id_acao);
  public function getByUuid($uuid);
  public function update($request, $uuid);
  public function getParameters($parameter);
  public function getActionsForReports(array $filtros);
} 