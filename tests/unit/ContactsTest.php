<?php

namespace unit;

use App\Models\Contact;
use App\Models\Email;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ContactsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    public function mockData()
    {
        return [
            [
                'name' => 'John Doe',
                'description' => 'A test contact',
                'zip_code' => '01001000',
                'country' => 'Country',
                'state' => 'State',
                'street_address' => 'Street Address',
                'address_number' => '123',
                'city' => 'City',
                'address_line' => 'Address Line',
                'neighborhood' => 'Neighborhood',
                'phone' => '1234567890',
                'email' => 'john@example.com'
            ],
            [
                'name' => 'Jane Doe',
                'description' => 'Another test contact',
                'zip_code' => '01001000',
                'country' => 'Brasil',
                'state' => 'São Paulo',
                'street_address' => 'Rua Carlópolis',
                'address_number' => '200',
                'city' => 'Itaquaquecetuba',
                'address_line' => 'Rua Carlópolis 200',
                'neighborhood' => 'Ribeiro',
                'phone' => '+55 11 987654321',
                'email' => 'jane@example.com'
            ]
        ];
    }

    public function testMockDbTesting()
    {
        $data = $this->mockData();

        foreach ($data as $contact) {
            $result = $this->call('post', 'api/contacts', $contact);
            $result->assertStatus(201);
        }
    }

    public function testIndex()
    {

        $this->testMockDbTesting();

        $result = $this->call('get', 'api/contacts');

        $responseArray = json_decode($result->getJSON(), true);

        $this->assertCount(2, $responseArray);
        $this->assertEquals('John Doe', $responseArray[0]['name']);
        $this->assertEquals('Jane Doe', $responseArray[1]['name']);
    }

    public function testCreate()
    {

        $newContact = [
            'name' => 'Alice Smith',
            'description' => 'A new test contact',
            'zip_code' => '01001000',
            'country' => 'CountryX',
            'state' => 'StateX',
            'street_address' => 'Another Street',
            'address_number' => '456',
            'city' => 'Another City',
            'address_line' => 'Another Address Line',
            'neighborhood' => 'Another Neighborhood',
            'phone' => '0987654321',
            'email' => 'alice@example.com'
        ];

        $result = $this->call('post', 'api/contacts', $newContact);

        $result->assertStatus(201);

        $emailModel = new Email();
        $contact = $emailModel->join('contacts', 'contacts.id = emails.id_contact')
            ->where('emails.email', 'alice@example.com')->first();

        $this->assertNotNull($contact);
        $this->assertEquals('Alice Smith', $contact['name']);
        $this->assertEquals('A new test contact', $contact['description']);
    }

    public function testUpdate()
    {
        $contactModel = new Contact();

        $this->testMockDbTesting();

        $existingContact = $contactModel->where('id', 1)->where('name', 'John Doe')->first();

        $this->assertNotNull($existingContact, 'O contato não foi encontrado no banco de dados.');

        $updateData = [
            'name' => 'John Doe Updated',
            'description' => 'An updated test contact',
            'zip_code' => '08590510',
            'country' => 'Updated Country',
            'state' => 'Updated State',
            'street_address' => 'Updated Street Address',
            'address_number' => '321',
            'city' => 'Updated City',
            'address_line' => 'Updated Address Line',
            'neighborhood' => 'Updated Neighborhood',
            'phone' => '9876543210',
            'email' => 'john_updated@example.com'
        ];

        $jsonData = json_encode($updateData);

        $result = $this->withBody($jsonData)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->call('put', 'api/contacts/1');

        $result->assertStatus(200);

        $updatedContact = $contactModel->join('addresses', 'addresses.id_contact = contacts.id')
            ->join('emails', 'emails.id_contact = contacts.id')
            ->join('phones', 'phones.id_contact = contacts.id')
            ->where('contacts.id', 1)->first();

        $this->assertNotNull($updatedContact);
        $this->assertEquals('John Doe Updated', $updatedContact['name']);
        $this->assertEquals('An updated test contact', $updatedContact['description']);
        $this->assertEquals('08590510', $updatedContact['zip_code']);
        $this->assertEquals('Updated Country', $updatedContact['country']);
        $this->assertEquals('Updated State', $updatedContact['state']);
        $this->assertEquals('Updated Street Address', $updatedContact['street_address']);
        $this->assertEquals('321', $updatedContact['address_number']);
        $this->assertEquals('Updated City', $updatedContact['city']);
        $this->assertEquals('Updated Address Line', $updatedContact['address_line']);
        $this->assertEquals('Updated Neighborhood', $updatedContact['neighborhood']);
        $this->assertEquals('9876543210', $updatedContact['phone']);
        $this->assertEquals('john_updated@example.com', $updatedContact['email']);
    }

    public function testDelete()
    {
        $contactModel = new Contact();

        $this->testMockDbTesting();

        $existingContactJohn = $contactModel->where('name', 'John Doe')->first();
        $this->assertNotNull($existingContactJohn, 'O contato John Doe não foi encontrado no banco de dados.');

        $existingContactJane = $contactModel->where('name', 'Jane Doe')->first();
        $this->assertNotNull($existingContactJane, 'O contato Jane Doe não foi encontrado no banco de dados.');

        $result = $this->call('delete', 'api/contacts/' . $existingContactJohn['id']);
        $result->assertStatus(200);

        $deletedContactJohn = $contactModel->where('id', $existingContactJohn['id'])->first();
        $this->assertNull($deletedContactJohn, 'O contato John Doe não foi deletado do banco de dados.');

        $result = $this->call('delete', 'api/contacts/' . $existingContactJane['id']);
        $result->assertStatus(200);

        $deletedContactJane = $contactModel->where('id', $existingContactJane['id'])->first();
        $this->assertNull($deletedContactJane, 'O contato Jane Doe não foi deletado do banco de dados.');
    }
}
