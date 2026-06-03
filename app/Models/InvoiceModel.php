<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'invoice_number',
        'registration_id',
        'user_id',
        'student_id',
        'academic_year',
        'status',
        'subtotal',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'due_at',
        'issued_at',
        'paid_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function activeForRegistration(int $registrationId): ?array
    {
        return $this->where('registration_id', $registrationId)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('id', 'DESC')
            ->first();
    }
}
