<?php

namespace Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class ContactsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    public function testGetAllContacts()
    {
        $result = $this->call('GET', 'api/contacts');
        $result->assertStatus(200);
    }

    public function testCreateContact()
    {
        $data = [
            'name' => 'John Doe',
            'description' => 'A test contact',
            'zip_code' => '08590-510',
            'country' => 'Country',
            'address_number' => '123',
            'phone' => '(11) 92345-6789',
            'email' => 'john@example.com'
        ];

        $result = $this->call('POST', 'api/contacts', $data);
        $result->assertStatus(201);
    }

    public function testUpdateContact()
    {

        $this->db->table('contacts')->insert([
            'name' => 'John Doe',
            'description' => 'A test contact',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $contactId = $this->db->insertID();

        $data = [
            'name' => 'John Doe Updated',
            'description' => 'An updated test contact',
            'zip_code' => '01001-000',
            'country' => 'Updated Country',
            'state' => 'Updated State',
            'street_address' => 'Updated Street Address',
            'address_number' => '321',
            'city' => 'Updated City',
            'address_line' => 'Updated Address Line',
            'neighborhood' => 'Updated Neighborhood',
            'phone' => '(11) 98765-4321',
            'email' => 'john_updated@example.com'
        ];

        $result = $this->call('PUT', "api/contacts/{$contactId}", $data);
        $result->assertStatus(200);
    }

    public function testDeleteContact()
    {
        
        $this->db->table('contacts')->insert([
            'name' => 'John Doe',
            'description' => 'A test contact',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $contactId = $this->db->insertID();

        $result = $this->call('DELETE', "api/contacts/{$contactId}");
        $result->assertStatus(200);
    }
}
