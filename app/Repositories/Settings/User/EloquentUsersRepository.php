<?php

namespace App\Repositories\Settings\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EloquentUsersRepository implements UsersRepository
{
    public function getByEmail($email){
        return User::where('email', $email)->first();
    }

    public function getForCoordinator(){
        return User::all();
    }

    public function getByUuid($uuid){
        return User::where('uuid', $uuid)->first();
    }

    public function getAll(array $filtros = [], string $sort = 'name', string $direction = 'desc')
    {
        $query = User::query();

        $query->when($filtros['search'] ?? null, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('centro_departamento', 'like', "%{$search}%")
                    ->orWhere('matricula_siape', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $camposFiltro = [
            'centro_departamento', 'status'
        ];

        foreach ($camposFiltro as $campo) {
            $valor = $filtros[$campo] ?? null;

            if ($valor !== null && $valor !== '') {
            $query->where($campo, $valor);
            }
        }

        return $query->orderBy($sort, $direction)->paginate(30)->withQueryString();
    }

    public function store($request){

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            'remember_token' => Str::random(10),
        ]);

        return $user;
    }

    public function store_all($request, $password){

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            'password' => Hash::make($password),
            'status' => true
        ]);

        return $user;
    }

    public function update($uuid, $request){
        $user = $this->getByUuid($uuid);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        
        if ($request->filled('status')) {
            $user->status = $request->status;
        }       
        
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }        
            
        if ($request->filled('centro_departamento')) {
            $user->centro_departamento = $request->centro_departamento;
        }

        if ($request->filled('matricula_siape')) {
            $user->matricula_siape = $request->matricula_siape;
        }

        $user->save();

        return $user;
    }

    public function updatePassword($request, $uuid){
        $user = $this->getByUuid($uuid);
        $user->password = Hash::make($request->password);
        $user->save();

        return $user;
    }

    public function updateStatus($uuid, $status){
        $user = $this->getByUuid($uuid);
        $user->status = $status;
        $user->save();

        return $user;
    }

    public function updateLastLogin($uuid){
        $user = $this->getByUuid($uuid);
        $user->timestamps = false;
        $user->last_login_at = now();
        $user->save();
        $user->timestamps = true;
        return $user;
    }

    public function delete($uuid){
        $user = $this->getByUuid($uuid);
        $user->delete();
    }

}