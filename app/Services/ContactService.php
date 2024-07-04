<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Phone;
use CodeIgniter\Database\Exceptions\DataException;

class ContactService
{
    protected $db;
    protected $contactModel;
    protected $addressModel;
    protected $phoneModel;
    protected $emailModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->contactModel = new Contact();
        $this->addressModel = new Address();
        $this->phoneModel = new Phone();
        $this->emailModel = new Email();
    }

//    public function createContact($data)
//    {
//        $this->db->transStart();
//
//        try {
//            $contact = $this->contactModel->insert($data);
//            if ($contact) {
//                $response = [
//                    'status' => 201,
//                    'error' => null,
//                    'messages' => [
//                        'success' => 'Contact created successfully'
//                    ]
//                ];
//            }
//        }catch (\Exception $e) {
//            $response = [
//                'status' => 400,
//                'error' => $this->contactModel->errors(),
//                'messages' => [
//                    'error' => 'Error saving contact'
//                ]
//            ];
//        }
//
//        return $this->response->setJSON($response);
//    }

    public function createContact($data)
    {
        $this->db->transStart();

        try {
            // Inserir contato
            $contactData = [
                'name' => $data['name'],
                'description' => $data['description']
            ];
            $this->contactModel->insert($contactData);
            $contactId = $this->contactModel->insertID();

            // Inserir endereço
            $addressData = [
                'id_contact' => $contactId,
                'zip_code' => $data['zip_code'],
                'country' => $data['country'],
                'state' => $data['state'],
                'street_address' => $data['street_address'],
                'address_number' => $data['address_number'],
                'city' => $data['city'],
                'address_line' => $data['address_line'],
                'neighborhood' => $data['neighborhood']
            ];
            $this->addressModel->insert($addressData);

            // Inserir telefone
            $phoneData = [
                'id_contact' => $contactId,
                'phone' => $data['phone']
            ];
            $this->phoneModel->insert($phoneData);

            // Inserir email
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
}