<?php

namespace App\Services;

use App\Models\Email;
use App\Models\Phone;
use App\Models\Contact;
use App\Models\Address;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DataException;


class ContactService
{
    protected $db;
    protected $contactModel;
    protected $addressModel;
    protected $phoneModel;
    protected $emailModel;
    protected $viaCepService;
    protected $validationRulesModelsService;

    public function __construct(
        Contact $contactModel,
        Address $addressModel,
        Phone $phoneModel,
        Email $emailModel,
        ViaCepService $viaCepService,
        ValidationRulesModelsService $validationRulesModelsService
    ){
        $this->db = \Config\Database::connect();
        $this->contactModel = $contactModel;
        $this->addressModel = $addressModel;
        $this->phoneModel = $phoneModel;
        $this->emailModel = $emailModel;
        $this->viaCepService = $viaCepService;
        $this->validationRulesModelsService = $validationRulesModelsService;
    }

    public function mountArrayData($data)
    {
        $fields = [
            'name', 'description', 'zip_code', 'country', 'state',
            'street_address', 'address_number', 'city', 'address_line',
            'neighborhood', 'phone', 'email'
        ];

        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $data->getVar($field) ?? $data->getPost($field) ?? null;
        }

        return $result;
    }

    public function createContactComplete($data)
    {
        $this->db->transStart();

        try {

            $contactData = [
                'name' => $data['name'],
                'description' => $data['description']
            ];

            $validate = $this->contactModel->insert($contactData);

            $contactId = $this->contactModel->insertID();

            if (!$validate){

                $this->db->transRollback();

                return $this->validationRulesModelsService->validate($this->contactModel);
            }

            if (empty($data['zip_code'])) {
                $this->db->transRollback();
                return $this->validationRulesModelsService->validate($this->addressModel);
            }

            $zipCode = preg_replace("/[^0-9]/", "", $data['zip_code']);
            $viaCep = strlen($zipCode) == 8 ? $this->viaCepService->requestZipCode($zipCode) : null;

            $addressData = [
                'id_contact' => $contactId,
                'zip_code' => $zipCode,
                'country' => $data['country'],
                'state' => $viaCep['uf']??null,
                'street_address' => $viaCep['logradouro']??null,
                'address_number' => $data['address_number'],
                'city' => $viaCep['localidade']??null,
                'address_line' => $data['address_number'].', '.$viaCep['logradouro'].' - '.$viaCep['bairro']??null,
                'neighborhood' => $viaCep['bairro']??null
            ];


            $validate = $this->addressModel->insert($addressData);

            if (!$validate){

                $this->db->transRollback();

                return $this->validationRulesModelsService->validate($this->addressModel);
            }

            $phoneData = [
                'id_contact' => $contactId,
                'phone' => $data['phone']
            ];

            $validate = $this->phoneModel->insert($phoneData);

            if (!$validate){

                $this->db->transRollback();

                return $this->validationRulesModelsService->validate($this->phoneModel);
            }


            $emailData = [
                'id_contact' => $contactId,
                'email' => $data['email']
            ];

            $validate = $this->emailModel->insert($emailData);

            if (!$validate){

                $this->db->transRollback();

                return $this->validationRulesModelsService->validate($this->emailModel);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new DataException('Erro ao inserir dados');
            }

            return [
                'status' => 201,
                'error' => null,
                'messages' => [
                    'success' => 'Contact created successfully'
                ]
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function updateContact($id, $data)
    {
        $this->db->transStart();

        try {

            $contactData = [
                'name' => $data['name'],
                'description' => $data['description']
            ];
            if (count(array_filter($contactData)) != 0) {

                $validate = $this->contactModel->update($id, array_filter($contactData));

                if (!$validate){
                    $this->db->transRollback();

                    return $this->validationRulesModelsService->validate($this->contactModel);
                }
            }

            $zipCode = null;
            $viaCep = [];

            $addressData = [
                'zip_code' => $data['zip_code'],
                'country' => $data['country'],
                'state' => $data['state'],
                'street_address' => $data['street_address'],
                'address_number' => $data['address_number'],
                'city' => $data['city'],
                'address_line' => $data['address_line'],
                'neighborhood' => $data['neighborhood']
            ];

            if (!empty($data['zip_code'])) {
                $zipCode = preg_replace("/[^0-9]/", "", $data['zip_code']);

                $viaCep = strlen($zipCode) == 8 ? $this->viaCepService->requestZipCode($zipCode) : null;
                $addressData = [
                    'zip_code' => $zipCode,
                    'country' => $data['country'],
                    'state' => $viaCep['uf']??null,
                    'street_address' => $viaCep['logradouro']??null,
                    'address_number' => $data['address_number']??null,
                    'city' => $viaCep['localidade']??null,

                    'address_line' => $data['address_number'].', '.$viaCep['logradouro'].' - '.$viaCep['bairro']??null,

                    'neighborhood' => $viaCep['bairro']??null
                ];
            }

            if (count(array_filter($addressData)) != 0) {

                $validate = $this->addressModel->where('id_contact', $id)->set(array_filter($addressData))->update();

                if (!$validate){
                    $this->db->transRollback();

                    return [
                        'status' => 400,
                        'error' => 400,
                        'messages' => [
                            'error' => $this->addressModel->errors()
                        ]
                    ];
                }
            }

            $phoneData = [
                'phone' => $data['phone']
            ];

            if (count(array_filter($phoneData)) != 0) {

                $validate = $this->phoneModel->where('id_contact', $id)->set(array_filter($phoneData))->update();

                if (!$validate){
                    $this->db->transRollback();

                    return [
                        'status' => 400,
                        'error' => 400,
                        'messages' => [
                            'error' => $this->phoneModel->errors()
                        ]
                    ];
                }
            }

            $emailData = [
                'email' => $data['email']
            ];

            if (count(array_filter($emailData)) != 0) {

                $validate = $this->emailModel->where('id_contact', $id)->set(array_filter($emailData))->update();

                if (!$validate){
                    $this->db->transRollback();

                    return [
                        'status' => 400,
                        'error' => 400,
                        'messages' => [
                            'error' => $this->emailModel->errors()
                        ]
                    ];
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new DataException('Erro ao atualizar dados');
            }

            return [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Contact updated successfully'
                ]
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function deleteContact($id)
    {
        $this->db->transStart();

        try {

            $this->addressModel->where('id_contact', $id)->delete();


            $this->phoneModel->where('id_contact', $id)->delete();


            $this->emailModel->where('id_contact', $id)->delete();


            $this->contactModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new DataException('Erro ao deletar dados');
            }

            return [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Contact deleted successfully'
                ]
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

}

