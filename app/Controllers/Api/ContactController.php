<?php

namespace App\Controllers\Api;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Phone;
use App\Services\ContactService;
use App\Services\DataCleansingService;
use App\Services\ValidationRulesModelsService;
use App\Services\ViaCepService;
use CodeIgniter\RESTful\ResourceController;

class ContactController extends ResourceController
{
    protected $contactModel;
    private $contactService;
    protected $dataCleansingService;

    public function __construct()
    {
        $contactModel = new Contact();
        $addressModel = new Address();
        $phoneModel = new Phone();
        $emailModel = new Email();
        $viaCepService = new ViaCepService();
        $validationRulesModelsService = new ValidationRulesModelsService();

        $this->contactModel = new Contact();
        $this->contactService = new ContactService($contactModel, $addressModel, $phoneModel, $emailModel, $viaCepService, $validationRulesModelsService);
        $this->dataCleansingService = new DataCleansingService();
    }

    public function index()
    {
//        $result = $this->contactModel
//            ->join('phones', 'phones.id_contact = contacts.id', 'left')
//            ->join('emails', 'emails.id_contact = contacts.id', 'left')->findAll();
//            ->join('addresses', 'addresses.id_contact = contacts.id', 'left')

        $result = $this->contactModel->select([
            'contacts.id',
            'contacts.name',
            'contacts.description',
            'addresses.zip_code',
            'addresses.country',
            'addresses.state',
            'addresses.street_address',
            'addresses.address_number',
            'addresses.city',
            'addresses.address_line',
            'addresses.neighborhood',
            'phones.phone',
            'emails.email',
            'contacts.created_at',
            'contacts.updated_at'

        ])
            ->join('addresses', 'addresses.id_contact = contacts.id')
            ->join('emails', 'emails.id_contact = contacts.id')
            ->join('phones', 'phones.id_contact = contacts.id')->findAll();
        return $this->respond($result);
//
//        return $this->respond($this->contactModel->findAll());
    }

    public function create()
    {
        try {
            $data = $this->request;

            $arrData = $this->contactService->mountArrayData($data);

//            $response = $this->contactService->createContactComplete($this->dataCleansingService->escapeArray($arrData));
            $response = $this->contactService->createContactComplete($arrData);

            if ($response['status'] == 400) {
                return $this->fail($response['messages']);
            }

            return $this->respondCreated($response);

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function update($id = null)
    {
        try {
            $data = $this->request;

            $arrData = $this->contactService->mountArrayData($data);

            $response = $this->contactService->updateContact($id, $this->dataCleansingService->escapeArray($arrData));

            if ($response['status'] == 400) {
                return $this->fail($response['messages']);
            }

            return $this->respond($response);

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        try {
            $response = $this->contactService->deleteContact($id);
            return $this->respondDeleted($response);

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


}
