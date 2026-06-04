<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FeeTypeModel;
use App\Models\InvoiceItemModel;

class FeeTypeController extends BaseController
{
    protected FeeTypeModel $feeTypeModel;
    protected InvoiceItemModel $invoiceItemModel;

    public function __construct()
    {
        $this->feeTypeModel = new FeeTypeModel();
        $this->invoiceItemModel = new InvoiceItemModel();
    }

    /**
     * Tampilkan daftar jenis biaya.
     */
    public function index()
    {
        $feeTypes = $this->feeTypeModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'    => 'Pengaturan Jenis Biaya',
            'feeTypes' => $feeTypes,
        ];

        return view('admin/fee_types/index', $data);
    }

    /**
     * Simpan jenis biaya baru.
     */
    public function store()
    {
        $rules = [
            'code'                         => 'required|alpha_dash|max_length[60]|is_unique[fee_types.code]',
            'name'                         => 'required|max_length[150]',
            'amount'                       => 'required|numeric',
            'billing_period'               => 'required|in_list[Satu Kali,Bulanan,Tahunan]',
            'sort_order'                   => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->feeTypeModel->insert([
            'code'                         => $this->request->getPost('code'),
            'name'                         => $this->request->getPost('name'),
            'description'                  => $this->request->getPost('description'),
            'amount'                       => (float) $this->request->getPost('amount'),
            'billing_period'               => $this->request->getPost('billing_period'),
            'is_required'                  => $this->request->getPost('is_required') !== null ? 1 : 0,
            'is_active'                    => $this->request->getPost('is_active') !== null ? 1 : 0,
            'show_on_homepage'             => $this->request->getPost('show_on_homepage') !== null ? 1 : 0,
            'requires_payment_before_form' => $this->request->getPost('requires_payment_before_form') !== null ? 1 : 0,
            'auto_invoice'                 => $this->request->getPost('auto_invoice') !== null ? 1 : 0,
            'icon'                         => $this->request->getPost('icon') ?: 'wallet',
            'sort_order'                   => $this->request->getPost('sort_order') !== '' ? (int)$this->request->getPost('sort_order') : 100,
        ]);

        return redirect()->to('admin/fee-types')->with('success', 'Jenis biaya baru berhasil ditambahkan.');
    }

    /**
     * Perbarui jenis biaya.
     */
    public function update(int $id)
    {
        $feeType = $this->feeTypeModel->find($id);
        if (!$feeType) {
            return redirect()->to('admin/fee-types')->with('error', 'Jenis biaya tidak ditemukan.');
        }

        $rules = [
            'code'                         => "required|alpha_dash|max_length[60]|is_unique[fee_types.code,id,{$id}]",
            'name'                         => 'required|max_length[150]',
            'amount'                       => 'required|numeric',
            'billing_period'               => 'required|in_list[Satu Kali,Bulanan,Tahunan]',
            'sort_order'                   => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->feeTypeModel->update($id, [
            'code'                         => $this->request->getPost('code'),
            'name'                         => $this->request->getPost('name'),
            'description'                  => $this->request->getPost('description'),
            'amount'                       => (float) $this->request->getPost('amount'),
            'billing_period'               => $this->request->getPost('billing_period'),
            'is_required'                  => $this->request->getPost('is_required') !== null ? 1 : 0,
            'is_active'                    => $this->request->getPost('is_active') !== null ? 1 : 0,
            'show_on_homepage'             => $this->request->getPost('show_on_homepage') !== null ? 1 : 0,
            'requires_payment_before_form' => $this->request->getPost('requires_payment_before_form') !== null ? 1 : 0,
            'auto_invoice'                 => $this->request->getPost('auto_invoice') !== null ? 1 : 0,
            'icon'                         => $this->request->getPost('icon') ?: 'wallet',
            'sort_order'                   => $this->request->getPost('sort_order') !== '' ? (int)$this->request->getPost('sort_order') : 100,
        ]);

        return redirect()->to('admin/fee-types')->with('success', 'Jenis biaya berhasil diperbarui.');
    }

    /**
     * Toggle status aktif.
     */
    public function toggle(int $id)
    {
        $feeType = $this->feeTypeModel->find($id);
        if (!$feeType) {
            return redirect()->to('admin/fee-types')->with('error', 'Jenis biaya tidak ditemukan.');
        }

        $newStatus = (int)$feeType['is_active'] === 1 ? 0 : 1;
        $this->feeTypeModel->update($id, ['is_active' => $newStatus]);

        return redirect()->to('admin/fee-types')->with('success', 'Status jenis biaya berhasil diubah.');
    }

    /**
     * Hapus jenis biaya.
     */
    public function delete(int $id)
    {
        $feeType = $this->feeTypeModel->find($id);
        if (!$feeType) {
            return redirect()->to('admin/fee-types')->with('error', 'Jenis biaya tidak ditemukan.');
        }

        // Validasi: tidak bisa hapus jika sudah dipakai di invoice_items (Req 2.1)
        $usedCount = $this->invoiceItemModel->where('fee_type_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return redirect()->to('admin/fee-types')->with('error', 'Jenis biaya ini tidak dapat dihapus karena sudah digunakan dalam tagihan (invoice) pendaftar.');
        }

        if ($this->feeTypeModel->delete($id)) {
            return redirect()->to('admin/fee-types')->with('success', 'Jenis biaya berhasil dihapus.');
        }

        return redirect()->to('admin/fee-types')->with('error', 'Gagal menghapus jenis biaya.');
    }
}
