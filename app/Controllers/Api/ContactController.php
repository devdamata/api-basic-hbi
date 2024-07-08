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
        return $this->respond($this->contactModel->findAll());
    }

    public function create()
    {
        try {
            $data = $this->request;

            $arrData = $this->contactService->mountArrayData($data);

            $response = $this->contactService->createContactComplete($this->dataCleansingService->escapeArray($arrData));

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
