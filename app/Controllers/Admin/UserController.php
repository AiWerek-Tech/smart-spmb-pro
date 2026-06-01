<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan daftar semua pengguna.
     */
    public function index()
    {
        $role = $this->request->getGet('role');
        $status = $this->request->getGet('status');

        $roleVal = !empty($role) ? $role : null;
        $statusVal = $status !== null && $status !== '' ? (int)$status : null;

        $users = $this->userModel->getUsers($roleVal, $statusVal);

        $data = [
            'title'  => 'Kelola Pengguna',
            'users'  => $users,
            'role'   => $role,
            'status' => $status,
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Tampilkan formulir tambah pengguna baru.
     */
    public function create()
    {
        return view('admin/users/create', [
            'title' => 'Tambah Pengguna Baru',
        ]);
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store()
    {
        $rules = [
            'name'            => 'required|min_length[3]|max_length[100]',
            'email'           => 'required|valid_email|max_length[150]',
            'role'            => 'required|in_list[admin,operator,pendaftar]',
            'password'        => 'required|min_length[8]',
            'confirm_password'=> 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        if ($this->userModel->emailExists($email)) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar. Silakan gunakan email lain.');
        }

        $password = $this->request->getPost('password');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userId = $this->userModel->insert([
            'name'      => $this->request->getPost('name'),
            'email'     => $email,
            'role'      => $this->request->getPost('role'),
            'password'  => $hashedPassword,
            'is_active' => $this->request->getPost('is_active') !== null ? 1 : 0,
        ]);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna.');
        }

        // Activity log could be logged here if needed

        return redirect()->to('admin/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit pengguna.
     */
    public function edit(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        return view('admin/users/edit', [
            'title' => 'Edit Pengguna',
            'user'  => $user,
        ]);
    }

    /**
     * Perbarui data pengguna di database.
     */
    public function update(int $id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email'=> 'required|valid_email|max_length[150]',
            'role' => 'required|in_list[admin,operator,pendaftar]',
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]';
            $rules['confirm_password'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        if ($this->userModel->emailExists($email, $id)) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar pada pengguna lain.');
        }

        $updateData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $email,
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') !== null ? 1 : 0,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!$this->userModel->update($id, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna.');
        }

        return redirect()->to('admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif/nonaktif pengguna.
     */
    public function toggle(int $id)
    {
        if ($id === (int)session()->get('user_id')) {
            return redirect()->to('admin/users')->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($this->userModel->toggleActive($id)) {
            return redirect()->to('admin/users')->with('success', 'Status keaktifan pengguna berhasil diperbarui.');
        }

        return redirect()->to('admin/users')->with('error', 'Gagal memperbarui status keaktifan.');
    }

    /**
     * Menghapus pengguna.
     */
    public function delete(int $id)
    {
        if ($id === (int)session()->get('user_id')) {
            return redirect()->to('admin/users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('admin/users')->with('success', 'Pengguna berhasil dihapus.');
        }

        return redirect()->to('admin/users')->with('error', 'Gagal menghapus pengguna.');
    }
}
