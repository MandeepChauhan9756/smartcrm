<?php

namespace App\Controllers;

use App\Models\LeadModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = [
            'id'   => (int) session()->get('user_id'),
            'name' => (string) session()->get('user_name'),
            'role' => (string) session()->get('user_role'),
        ];

        $leadModel = new LeadModel();

        $stats = $leadModel->getDashboardStats($user);

        return view('dashboard/index', [
            'title'         => 'Dashboard',
            'stats'         => $stats,
            'statusCounts'  => $leadModel->getLeadStatusCounts($user),
            'monthlyLeads'  => $leadModel->getMonthlyLeadCounts($user),
            'recentLeads'   => $leadModel->getRecentVisibleLeads($user, 5),
        ]);
    }
}
