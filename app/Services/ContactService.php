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

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->contactModel = new Contact();
        $this->addressModel = new Address();
        $this->phoneModel = new Phone();
        $this->emailModel = new Email();
        $this->viaCepService = new ViaCepService();
    }

    public function mountArrayData($data)
    {
        return [
            'name' => $data->getVar('name')??$data->getPost('name')??null,
            'description' => $data->getVar('description')??$data->getPost('description')??null,
            'zip_code' => $data->getVar('zip_code')??$data->getPost('zip_code')??null,
            'country' => $data->getVar('country')??$data->getPost('country')??null,
            'state' => $data->getVar('state')??$data->getPost('state')??null,
            'street_address' => $data->getVar('street_address')??$data->getPost('street_address')??null,
            'address_number' => $data->getVar('address_number')??$data->getPost('address_number')??null,
            'city' => $data->getVar('city')??$data->getPost('city')??null,
            'address_line' => $data->getVar('address_line')??$data->getPost('address_line')??null,
            'neighborhood' => $data->getVar('neighborhood')??$data->getPost('neighborhood')??null,
            'phone' => $data->getVar('phone')??$data->getPost('phone')??null,
            'email' => $data->getVar('email')??$data->getPost('email')??null
        ];
    }

    public function createContactComplete($data)
    {
        $this->db->transStart();

        try {

            $contactData = [
                'name' => $data['name'],
                'description' => $data['description']
            ];

            $this->contactModel->insert($contactData);

            $contactId = $this->contactModel->insertID();

            if (empty($data['zip_code'])) {
                $this->db->transRollback();
                throw new DataException('Erro ao inserir dados, zip_code obrigatório!!!');
            }

            $zipCode = preg_replace("/[^0-9]/", "", $data['zip_code']);
            $viaCep = strlen($zipCode) == 8 ? $this->viaCepService->requestZipCode($zipCode) : null;

            $addressData = [
                'id_contact' => $contactId,
                'zip_code' => $zipCode,
                'country' => $data['country'],
                'address_number' => $data['address_number'],
                'state' => $viaCep['uf']??null,
                'street_address' => $viaCep['logradouro']??null,
                'city' => $viaCep['localidade']??null,
                'address_line' => $data['address_number'].', '.$viaCep['logradouro'].' - '.$viaCep['bairro']??null,
                'neighborhood' => $viaCep['bairro']??null
            ];

            $validate = $this->addressModel->insert($addressData);

            if (!$validate){
                $this->db->transRollback();
                throw new DataException('Erro ao inserir dados, zip_code obrigatório!!!');
            }

            $phoneData = [
                'id_contact' => $contactId,
                'phone' => $data['phone']
            ];
            $this->phoneModel->insert($phoneData);


            $emailData = [
                'id_contact' => $contactId,
                'email' => $data['email']
            ];
            $this->emailModel->insert($emailData);

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
                $this->contactModel->update($id, array_filter($contactData));
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
                $this->addressModel->where('id_contact', $id)->set(array_filter($addressData))->update();
            }

            $phoneData = [
                'phone' => $data['phone']
            ];

            if (count(array_filter($phoneData)) != 0) {
                $this->phoneModel->where('id_contact', $id)->set(array_filter($phoneData))->update();
            }

            $emailData = [
                'email' => $data['email']
            ];

            if (count(array_filter($emailData)) != 0) {
                $this->emailModel->where('id_contact', $id)->set(array_filter($emailData))->update();
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

