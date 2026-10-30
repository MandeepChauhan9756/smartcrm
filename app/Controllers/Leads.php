<?php

namespace App\Controllers;

use App\Models\LeadModel;

class Leads extends BaseController
{
    private function currentUser(): array
    {
        return [
            'id'   => (int) session()->get('user_id'),
            'name' => (string) session()->get('user_name'),
            'role' => (string) session()->get('user_role'),
        ];
    }

    public function index()
    {
        $user  = $this->currentUser();
        $model = new LeadModel();

        $search = trim((string) $this->request->getGet('q'));

        $allowedStatuses = ['new', 'contacted', 'qualified', 'won', 'lost'];
        $allowedPriorities = ['low', 'medium', 'high'];

        $status = (string) $this->request->getGet('status');
        $priority = (string) $this->request->getGet('priority');

        if (! in_array($status, $allowedStatuses, true)) {
            $status = '';
        }

        if (! in_array($priority, $allowedPriorities, true)) {
            $priority = '';
        }

        $leads = $model->getVisibleLeads(
            $user,
            $search,
            $status,
            $priority
        );

        return view('leads/index', [
            'title'            => 'Leads',
            'leads'            => $leads,
            'search'           => $search,
            'selectedStatus'   => $status,
            'selectedPriority' => $priority,
            'pager'            => $model->pager,
        ]);
    }

    public function create()
    {
        return view('leads/form', [
            'title'  => 'Create Lead',
            'lead'   => null,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[190]',
            'phone' => 'permit_empty|max_length[30]',
            'company' => 'permit_empty|max_length[150]',
            'source' => 'required|in_list[website,referral,linkedin,other]',
            'status' => 'required|in_list[new,contacted,qualified,won,lost]',
            'notes' => 'permit_empty|max_length[5000]',
            'priority' => 'required|in_list[low,medium,high]',
            'follow_up_date' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = $this->currentUser();

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'email'       => $this->nullablePost('email'),
            'phone'       => $this->nullablePost('phone'),
            'company'     => $this->nullablePost('company'),
            'source'      => $this->request->getPost('source'),
            'status'      => $this->request->getPost('status'),
            'notes'       => $this->nullablePost('notes'),
            'priority' => $this->request->getPost('priority'),
            'follow_up_date' => $this->nullablePost('follow_up_date'),
            'assigned_to' => $user['id'],
            'created_by'  => $user['id'],
        ];

        $model = new LeadModel();

        if (! $model->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Could not create lead. Please try again.');
        }

        return redirect()->to(site_url('leads'))
            ->with('success', 'Lead created successfully.');
    }

    public function edit(int $id)
    {
        $user = $this->currentUser();
        $model = new LeadModel();
        $lead = $model->findVisibleLead($id, $user);

        if (! $lead) {
            return $this->response->setStatusCode(404)
                ->setBody('Lead not found.');
        }

        return view('leads/form', [
            'title'  => 'Edit Lead',
            'lead'   => $lead,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update(int $id)
    {
        $user = $this->currentUser();
        $model = new LeadModel();
        $lead = $model->findVisibleLead($id, $user);

        if (! $lead) {
            return $this->response->setStatusCode(404)
                ->setBody('Lead not found.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[190]',
            'phone' => 'permit_empty|max_length[30]',
            'company' => 'permit_empty|max_length[150]',
            'source' => 'required|in_list[website,referral,linkedin,other]',
            'status' => 'required|in_list[new,contacted,qualified,won,lost]',
            'notes' => 'permit_empty|max_length[5000]',
            'priority' => 'required|in_list[low,medium,high]',
            'follow_up_date' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'    => trim((string) $this->request->getPost('name')),
            'email'   => $this->nullablePost('email'),
            'phone'   => $this->nullablePost('phone'),
            'company' => $this->nullablePost('company'),
            'source'  => $this->request->getPost('source'),
            'status'  => $this->request->getPost('status'),
            'notes'   => $this->nullablePost('notes'),
            'priority' => $this->request->getPost('priority'),
            'follow_up_date' => $this->nullablePost('follow_up_date'),
        ];

        if (! $model->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Could not update lead.');
        }

        return redirect()->to(site_url('leads'))
            ->with('success', 'Lead updated successfully.');
    }

    public function delete(int $id)
    {
        if (session()->get('user_role') !== 'admin') {
            return $this->response->setStatusCode(403)
                ->setBody('Forbidden: admin permission required.');
        }

        $model = new LeadModel();
        $lead = $model->find($id);

        if (! $lead) {
            return $this->response->setStatusCode(404)
                ->setBody('Lead not found.');
        }

        $model->delete($id);

        return redirect()->to(site_url('leads'))
            ->with('success', 'Lead deleted successfully.');
    }

    private function nullablePost(string $field): ?string
    {
        $value = trim((string) $this->request->getPost($field));

        return $value === '' ? null : $value;
    }
}