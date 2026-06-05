<?php

namespace App\Repositories\Forms;

interface FormsRepository{
  public function getByFilter(array $filtros = [], string $sort = 'titulo', string $direction = 'desc');
  public function create($request);
  public function update($request, $uuid);
  public function getSessionById($uuid);
  public function getQuestionById($uuid);
  public function updateSession($request, $uuid);
  public function createQuestion($request, $uuid);
  public function getFormById($uuid);
  public function deleteSessions($uuid);
  public function storeSessions($request, $uuid);
  public function deleteQuestion($uuid);
  public function destroy($uuid);
} 