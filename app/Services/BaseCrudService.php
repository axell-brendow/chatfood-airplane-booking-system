<?php

namespace App\Services;

use App\Interfaces\Services\ICrudService;

abstract class BaseCrudService implements ICrudService
{
    public function index()
    {
        return $this->model()::all();
    }

    protected function associateRelations($model, $params)
    {
        foreach ($params as $key => $param)
            if (str_ends_with($key, "_id"))
            {
                $key_without_id = substr($key, 0, strlen($key) - 3);
                $snake_pascal_case = ucwords($key_without_id, "_");
                $relation_method_name = str_replace("_", "", $snake_pascal_case);
                $relation_method_name[0] = strtolower($relation_method_name[0]);
                $model->$relation_method_name()->associate($params[$key]);

            }
    }

    public function store(array $params)
    {
        $validatedData = \Validator::make($params, $this->rulesStore())->validate();
        $modelName = $this->model();
        $model = new $modelName($validatedData);
        $this->associateRelations($model, $params);
        $model->save();
        $model->refresh();
        return $model;
    }

    protected function findOrFail($id)
    {
        $modelClass = $this->model();
        $keyName = (new $modelClass)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

    public function update(array $params, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = \Validator::make($params, $this->rulesUpdate())->validate();
        $this->associateRelations($obj, $params);
        $obj->update($validatedData);
        $obj->save();
        return $obj;
    }

    public function destroy($id)
    {
        $model = $this->findOrFail($id);
        $model->delete();
        return response()->noContent();
    }
}
