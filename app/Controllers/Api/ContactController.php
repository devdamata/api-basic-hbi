<?php

namespace App\Controllers\Api;

use App\Models\Contact;
use App\Services\ContactService;
use CodeIgniter\RESTful\ResourceController;

class ContactController extends ResourceController
{
    private $contactModel;
    private $contactService;

    public function __construct()
    {
        $this->contactModel = new Contact();
        $this->contactService = new ContactService();
    }

    public function index()
    {
        return $this->response->setJSON($this->contactModel->findAll());
    }

    public function create()
    {
//        $data = $this->request->getJSON();
        try {
            $data = [
                'name' => $this->request->getVar('name'),
                'description' => $this->request->getVar('description'),
                'zip_code' => $this->request->getVar('zip_code'),
                'country' => $this->request->getVar('country'),
                'state' => $this->request->getVar('state'),
                'street_address' => $this->request->getVar('street_address'),
                'address_number' => $this->request->getVar('address_number'),
                'city' => $this->request->getVar('city'),
                'address_line' => $this->request->getVar('address_line'),
                'neighborhood' => $this->request->getVar('neighborhood'),
                'phone' => $this->request->getVar('phone'),
                'email' => $this->request->getVar('email')
            ];

            $response = $this->contactService->createContact($data);

            return $this->respondCreated($response);

        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
