<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table            = 'leads';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'priority',
        'follow_up_date',
        'assigned_to',
        'created_by',
        'notes',
    ];

    public function getVisibleLeads(array $user, string $search = '', string $status = '', string $priority = ''): array
    {
        $builder = $this->select(
            'leads.*, owner.name AS owner_name'
        )->join(
            'users AS owner',
            'owner.id = leads.assigned_to',
            'left'
        );

        if ($user['role'] !== 'admin') {
            $builder->where('leads.assigned_to', $user['id']);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('leads.name', $search)
                ->orLike('leads.email', $search)
                ->orLike('leads.phone', $search)
                ->orLike('leads.company', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('leads.status', $status);
        }

        if ($priority !== '') {
            $builder->where('leads.priority', $priority);
        }

        return $builder
            ->orderBy('leads.created_at', 'DESC')
            ->paginate(10);
    }

    public function findVisibleLead(int $id, array $user): ?array
    {
        $builder = $this->where('id', $id);

        if ($user['role'] !== 'admin') {
            $builder->where('assigned_to', $user['id']);
        }

        $lead = $builder->first();

        return $lead ?: null;
    }


    /**
     * Return the number of leads matching the user's access scope.
     */
    private function analyticsLeadsTable(array $user)
    {
        $builder = $this->db->table('leads');

        if ($user['role'] !== 'admin') {
            $builder->where('assigned_to', $user['id']);
        }

        return $builder;
    }

    public function getDashboardStats(array $user): array
    {
        $today = date('Y-m-d');

        $totalLeads = $this->analyticsLeadsTable($user)
            ->countAllResults();

        $convertedLeads = $this->analyticsLeadsTable($user)
            ->where('status', 'won')
            ->countAllResults();

        $pendingFollowUps = $this->analyticsLeadsTable($user)
            ->where('follow_up_date >=', $today)
            ->whereNotIn('status', ['won', 'lost'])
            ->countAllResults();

        $overdueFollowUps = $this->analyticsLeadsTable($user)
            ->where('follow_up_date <', $today)
            ->whereNotIn('status', ['won', 'lost'])
            ->countAllResults();

        return [
            'totalLeads'       => $totalLeads,
            'convertedLeads'   => $convertedLeads,
            'pendingFollowUps' => $pendingFollowUps,
            'overdueFollowUps' => $overdueFollowUps,
        ];
    }

    public function getLeadStatusCounts(array $user): array
    {
        $builder = $this->analyticsLeadsTable($user);

        $rows = $builder
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $counts = [
            'new'       => 0,
            'contacted' => 0,
            'qualified' => 0,
            'won'       => 0,
            'lost'      => 0,
        ];

        foreach ($rows as $row) {
            $status = $row['status'];

            if (array_key_exists($status, $counts)) {
                $counts[$status] = (int) $row['total'];
            }
        }

        return $counts;
    }

    public function getMonthlyLeadCounts(array $user): array
    {
        // Include the current month and the previous five months.
        $startDate = date('Y-m-01', strtotime('-5 months'));
        $endDate   = date('Y-m-01', strtotime('+1 month'));

        $builder = $this->analyticsLeadsTable($user);

        $rows = $builder
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS month_key", false)
            ->select('COUNT(*) AS total')
            ->where('created_at >=', $startDate)
            ->where('created_at <', $endDate)
            ->groupBy('month_key')
            ->orderBy('month_key', 'ASC')
            ->get()
            ->getResultArray();

        $monthlyCounts = [];

        // Fill missing months with zero.
        for ($i = 5; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-{$i} months"));
            $monthlyCounts[$key] = 0;
        }

        foreach ($rows as $row) {
            if (array_key_exists($row['month_key'], $monthlyCounts)) {
                $monthlyCounts[$row['month_key']] = (int) $row['total'];
            }
        }

        $labels = [];
        $values = [];

        foreach ($monthlyCounts as $month => $count) {
            $labels[] = date('M Y', strtotime($month . '-01'));
            $values[] = $count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function getRecentVisibleLeads(array $user, int $limit = 5): array
    {
        $builder = $this->db->table('leads')
            ->select(
                'leads.id, leads.name, leads.company, leads.status, ' .
                    'leads.priority, leads.follow_up_date, leads.created_at, ' .
                    'owner.name AS owner_name'
            )
            ->join(
                'users AS owner',
                'owner.id = leads.assigned_to',
                'left'
            );

        if ($user['role'] !== 'admin') {
            $builder->where('leads.assigned_to', $user['id']);
        }

        return $builder
            ->orderBy('leads.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
