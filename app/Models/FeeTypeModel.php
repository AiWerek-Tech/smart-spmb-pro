<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeTypeModel extends Model
{
    protected $table = 'fee_types';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'amount',
        'billing_period',
        'is_required',
        'is_active',
        'show_on_homepage',
        'requires_payment_before_form',
        'auto_invoice',
        'icon',
        'sort_order',
    ];

    protected $validationRules = [
        'code' => 'required|alpha_dash|max_length[60]',
        'name' => 'required|max_length[150]',
        'amount' => 'required|decimal',
    ];

    public function activeHomepageFees(): array
    {
        return $this->where('is_active', 1)
            ->where('show_on_homepage', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
