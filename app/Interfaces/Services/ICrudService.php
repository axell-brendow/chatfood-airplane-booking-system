<?php

namespace App\Interfaces\Services;

use Illuminate\Database\Eloquent\Model;

interface ICrudService
{
    public function index();

    public function store(array $params);

    public function show($id);

    public function update(array $params, $id);

    public function destroy($id);

    /** @return class-string<Model> */
    public function model(): string;

    public function rulesStore(): array;

    public function rulesUpdate(): array;
}
