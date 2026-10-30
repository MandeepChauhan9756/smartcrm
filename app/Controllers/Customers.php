<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Customers extends BaseController
{
    protected CustomerModel $customers;

    public function __construct()
    {
        $this->customers = new CustomerModel();
    }

    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));

        if ($search !== '') {
            $this->customers
                ->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('company', $search)
                ->groupEnd();
        }

        return view('customers/index', [
            'title'     => 'Customers',
            'customers' => $this->customers
                ->orderBy('id', 'DESC')
                ->paginate(10),
            'pager'     => $this->customers->pager,
            'search'    => $search,
        ]);
    }

    public function create()
    {
        return view('customers/form', [
            'title'    => 'Add Customer',
            'customer' => null,
            'errors'   => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->to(site_url('customers/create'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->customers->insert($this->customerData());

        return redirect()->to(site_url('customers'))
            ->with('success', 'Customer added successfully.');
    }

    public function edit($id)
    {
        $customer = $this->customers->find($id);

        if (! $customer) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        return view('customers/form', [
            'title'    => 'Edit Customer',
            'customer' => $customer,
            'errors'   => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update($id)
    {
        if (! $this->customers->find($id)) {
            throw PageNotFoundException::forPageNotFound('Customer not found.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->to(site_url("customers/{$id}/edit"))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->customers->update($id, $this->customerData());

        return redirect()->to(site_url('customers'))
            ->with('success', 'Customer updated successfully.');
    }

    public function delete($id)
    {
        if (! $this->customers->find($id)) {
            return redirect()->to(site_url('customers'))
                ->with('error', 'Customer not found.');
        }

        $this->customers->delete($id);

        return redirect()->to(site_url('customers'))
            ->with('success', 'Customer deleted successfully.');
    }

    private function validationRules(): array
    {
        return [
            'name'    => 'required|min_length[2]|max_length[150]',
            'email'   => 'permit_empty|valid_email|max_length[190]',
            'phone'   => 'permit_empty|max_length[30]',
            'company' => 'permit_empty|max_length[150]',
            'address' => 'permit_empty|max_length[5000]',
            'notes'   => 'permit_empty|max_length[5000]',
        ];
    }

    private function customerData(): array
    {
        $fields = ['name', 'email', 'phone', 'company', 'address', 'notes'];
        $data = [];

        foreach ($fields as $field) {
            $value = trim((string) $this->request->getPost($field));
            $data[$field] = $value !== '' ? $value : null;
        }

        return $data;
    }
}