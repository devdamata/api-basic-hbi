<?php

namespace App\Models;

use CodeIgniter\Model;

class Contact extends Model
{
    protected $table            = 'contacts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]',
        'description' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'O campo NAME é obrigatório.',
            'min_length' => 'O campo NAME teve o tamanho mínimo atingido, mínimo permitido são 3 caracteres.'
        ],
        'description' => [
            'required' => 'O campo DESCRIPTION é obrigatório.',
            'min_length' => 'O campo DESCRIPTION teve o tamanho máximo atingido, tamanho permitido 255 caracteres.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

}
