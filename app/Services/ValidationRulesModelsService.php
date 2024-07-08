<?php

namespace App\Services;

class ValidationRulesModelsService
{


    public function validate($model)
    {
        return [
            'status' => 400,
            'error' => 400,
            'messages' => [
                'error' => $model->errors()
            ]
        ];
    }
}