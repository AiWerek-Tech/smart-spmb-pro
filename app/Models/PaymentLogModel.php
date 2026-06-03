<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentLogModel extends Model
{
    protected $table = 'payment_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'invoice_id',
        'payment_id',
        'action',
        'old_status',
        'new_status',
        'actor_id',
        'notes',
        'meta',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
