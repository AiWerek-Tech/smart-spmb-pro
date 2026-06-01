<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckDb extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'app:checkdb';
    protected $description = 'Check database rows inside application context';
    protected $usage = 'app:checkdb';
    protected $arguments = [];
    protected $options = [];

    public function run(array $params)
    {
        $gelombangModel = new \App\Models\GelombangModel();
        $gelombang = $gelombangModel->findAll();
        CLI::write("Gelombang Model count: " . count($gelombang));
        foreach ($gelombang as $g) {
            CLI::write(" - ID: {$g['id']}, Name: {$g['name']}, Jalur: {$g['jalur_id']}, Active: {$g['is_active']}");
        }

        $faqModel = new \App\Models\FaqModel();
        $faqs = $faqModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        CLI::write("FAQs Model count: " . count($faqs));
        foreach ($faqs as $f) {
            CLI::write(" - Q: {$f['question']}");
        }
    }
}
