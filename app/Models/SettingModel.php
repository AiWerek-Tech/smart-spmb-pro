<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'key',
        'value',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'key' => 'required|max_length[100]',
    ];

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        $row = $this->where('key', $key)->first();

        return $row !== null ? $row['value'] : $default;
    }

    /**
     * Set nilai setting berdasarkan key (upsert).
     */
    public function setValue(string $key, mixed $value): bool
    {
        $existing = $this->where('key', $key)->first();

        if ($existing !== null) {
            return $this->update($existing['id'], ['value' => $value]);
        }

        return $this->insert(['key' => $key, 'value' => $value]) !== false;
    }

    /**
     * Ambil semua setting sebagai array asosiatif key => value.
     */
    public function getAllAsArray(): array
    {
        $rows   = $this->findAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }

    /**
     * Set banyak setting sekaligus dari array asosiatif key => value.
     */
    public function setMultiple(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            if (! $this->setValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ambil setting profil sekolah.
     */
    public function getSchoolProfile(): array
    {
        $keys = [
            'school_name',
            'school_logo',
            'school_description',
            'academic_year',
            'phone',
            'email',
            'address',
            'whatsapp',
            'maps_embed',
            'accreditation',
            'accreditation_year',
            'npsn',
            'vision',
            'mission',
            'history',
            'tagline',
            'app_version',
            'developer_name',
            'developer_phone',
            'developer_email',
            'school_facilities',
            'campus_title',
            'campus_description',
            'privacy_policy',
            'terms_conditions',
        ];

        $rows   = $this->whereIn('key', $keys)->findAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }
}
