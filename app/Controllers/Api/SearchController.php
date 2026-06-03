<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SearchController — API endpoint for the Command Palette (Ctrl+K)
 * 
 * Searches across registrations, users, and returns JSON results
 * for the frontend command palette to display.
 */
class SearchController extends BaseController
{
    /**
     * GET /api/search?q={query}
     * 
     * Returns JSON results for command palette search.
     */
    public function index(): ResponseInterface
    {
        $query = $this->request->getGet('q');
        $role = session()->get('user_base_role') ?? session()->get('user_role');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $results = [];

        // Search registrations (admin and operator only)
        if (in_array($role, ['admin', 'operator'])) {
            $results = array_merge($results, $this->searchRegistrations($query));
        }

        // Search users (admin only)
        if ($role === 'admin') {
            $results = array_merge($results, $this->searchUsers($query));
        }

        // Limit results
        $results = array_slice($results, 0, 10);

        return $this->response->setJSON(['results' => $results]);
    }

    /**
     * Search registrations by name, NIK, or registration number.
     */
    private function searchRegistrations(string $query): array
    {
        $db = \Config\Database::connect();
        $results = [];

        // Check if registrations table exists
        if (!$db->tableExists('registrations')) {
            return $results;
        }

        $builder = $db->table('registrations');
        
        // Check which columns exist
        $fields = $db->getFieldNames('registrations');
        
        $builder->groupStart();
        
        if (in_array('full_name', $fields)) {
            $builder->like('full_name', $query);
        }
        if (in_array('nik', $fields)) {
            $builder->orLike('nik', $query);
        }
        if (in_array('registration_number', $fields)) {
            $builder->orLike('registration_number', $query);
        }
        
        $builder->groupEnd();
        $builder->limit(5);
        
        $rows = $builder->get()->getResultArray();

        foreach ($rows as $row) {
            $name = $row['full_name'] ?? ($row['nama_lengkap'] ?? 'Unknown');
            $subtitle = $row['registration_number'] ?? ($row['nik'] ?? '');
            $id = $row['id'] ?? $row['registration_id'] ?? 0;
            
            $results[] = [
                'type'     => 'registrant',
                'title'    => $name,
                'subtitle' => $subtitle ? "No. Pendaftaran: {$subtitle}" : '',
                'url'      => base_url("operator/registrants/{$id}"),
                'icon'     => 'graduation-cap',
                'badge'    => 'Pendaftar',
            ];
        }

        return $results;
    }

    /**
     * Search users by name or email.
     */
    private function searchUsers(string $query): array
    {
        $db = \Config\Database::connect();
        $results = [];

        if (!$db->tableExists('users')) {
            return $results;
        }

        $builder = $db->table('users');
        
        $builder->groupStart()
                ->like('name', $query)
                ->orLike('email', $query)
                ->groupEnd()
                ->limit(5);
        
        $rows = $builder->get()->getResultArray();

        foreach ($rows as $row) {
            $results[] = [
                'type'     => 'user',
                'title'    => $row['name'] ?? $row['email'],
                'subtitle' => $row['email'] ?? '',
                'url'      => base_url("admin/users/{$row['id']}/edit"),
                'icon'     => 'user',
                'badge'    => ucfirst($row['role'] ?? 'user'),
            ];
        }

        return $results;
    }
}
