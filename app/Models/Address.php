<?php

namespace App\Models;

use CodeIgniter\Model;

class Address extends Model
{
    protected $table            = 'addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_contact',
        'zip_code',
        'country',
        'state',
        'street_address',
        'address_number',
        'city',
        'address_line',
        'neighborhood'
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
        'zip_code'          => 'required|regex_match[/^\d{5}\d{3}$/]',
        'country'           => 'required|min_length[3]|max_length[50]',
        'state'             => 'permit_empty|min_length[2]|max_length[50]',
        'street_address'    => 'permit_empty|max_length[100]',
        'address_number'    => 'required|max_length[10]',
        'city'              => 'permit_empty|min_length[3]|max_length[50]',
        'address_line'      => 'permit_empty|max_length[100]',
        'neighborhood'      => 'permit_empty|max_length[50]',
    ];
    protected $validationMessages   = [
        'zip_code' => [
            'required' => 'O campo CEP é obrigatório.',
            'regex_match' => 'CEP inválido. O formato correto é 12345-678.'
        ],
        'country' => [
            'required' => 'O campo COUNTRY é obrigatório.',
            'min_length' => 'O campo COUNTRY teve o seu tamanho mínimo excedido.',
            'max_length' => 'O campo COUNTRY teve o seu tamanho máximo excedido.'
        ],
        'state' => [
            'min_length' => 'O campo STATE teve o seu tamanho mínimo excedido.',
            'max_length' => 'O campo STATE teve o seu tamanho máximo excedido.'
        ],
        'street_address' => [
            'max_length' => 'O campo STREET ADDRESS teve o seu tamanho máximo excedido.'
        ],
        'address_number' => [
            'required' => 'O campo ADDRESS NUMBER é obrigatório.',
            'regex_match' => 'O campo ADDRESS NUMBER teve o seu tamanho máximo excedido.'
        ],
        'city' => [
            'min_length' => 'O campo CITY teve o seu tamanho mínimo excedido.',
            'max_length' => 'O campo CITY teve o seu tamanho máximo excedido.'
        ],
        'address_line' => [
            'max_length' => 'O campo ADDRESS LINE teve o seu tamanho máximo excedido.'
        ],
        'neighborhood' => [
            'max_length' => 'O campo NEIGHBORHOOD teve o seu tamanho máximo excedido.'
        ],
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
