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
        try {
            $data = $this->request;

            $arrData = $this->contactService->mountArrayData($data);

            $response = $this->contactService->createContactComplete($arrData);

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

            $response = $this->contactService->updateContact($id, $arrData);

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
