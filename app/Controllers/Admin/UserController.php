<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Services\RbacService;

class UserController extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected UserRoleModel $userRoleModel;
    protected RbacService $rbacService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->rbacService = new RbacService();
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
        $roles = $this->roleModel->activeOrdered();

        $data = [
            'title'  => 'Kelola Pengguna',
            'users'  => $users,
            'roles'  => $roles,
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
            'roles' => $this->roleModel->activeOrdered(),
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
            'role'            => 'required|max_length[50]',
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

        $role = (string) $this->request->getPost('role');
        if (!$this->roleModel->isActiveSlug($role)) {
            return redirect()->back()->withInput()->with('error', 'Peran yang dipilih tidak aktif atau tidak ditemukan.');
        }

        $password = $this->request->getPost('password');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userId = $this->userModel->insert([
            'name'      => $this->request->getPost('name'),
            'email'     => $email,
            'role'      => $role,
            'password'  => $hashedPassword,
            'is_active' => $this->request->getPost('is_active') !== null ? 1 : 0,
        ]);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna.');
        }

        $this->syncPrimaryRole((int) $userId, $role, null);

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
            'title'                => 'Edit Pengguna',
            'user'                 => $user,
            'roles'                => $this->roleModel->activeOrdered(),
            'assignedRoles'        => $this->userRoleModel->getAllRolesForUser($id),
            'effectivePermissions' => service('rbacEngine')->getUserPermissions($id),
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
            'role' => 'required|max_length[50]',
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

        $previousRole = (string) $user['role'];
        $role = (string) $this->request->getPost('role');
        if (!$this->roleModel->isActiveSlug($role)) {
            return redirect()->back()->withInput()->with('error', 'Peran yang dipilih tidak aktif atau tidak ditemukan.');
        }

        $updateData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $email,
            'role'      => $role,
            'is_active' => $this->request->getPost('is_active') !== null ? 1 : 0,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!$this->userModel->update($id, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengguna.');
        }

        $this->syncPrimaryRole($id, $role, $previousRole);
        service('rbacEngine')->invalidateUser($id);

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

    public function assignRole(int $id)
    {
        $roleId = (int) $this->request->getPost('role_id');
        $expiresAt = $this->request->getPost('expires_at');
        $expiresAt = $expiresAt ? date('Y-m-d 23:59:59', strtotime((string) $expiresAt)) : null;

        $result = $this->rbacService->assignRole($id, $roleId, (int) session()->get('user_id'), $expiresAt);

        return redirect()->to('admin/users/'.$id.'/edit')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function revokeRole(int $id, int $roleId)
    {
        $result = $this->rbacService->revokeRole($id, $roleId);

        return redirect()->to('admin/users/'.$id.'/edit')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function syncPrimaryRole(int $userId, string $roleSlug, ?string $previousRoleSlug): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('roles') || ! $db->tableExists('user_roles')) {
            return;
        }

        if ($previousRoleSlug !== null && $previousRoleSlug !== $roleSlug) {
            $previousRole = $db->table('roles')->select('id')->where('slug', $previousRoleSlug)->get()->getRowArray();
            if ($previousRole) {
                $db->table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_id', $previousRole['id'])
                    ->where('assigned_by IS NULL', null, false)
                    ->delete();
            }
        }

        $role = $db->table('roles')->select('id')->where('slug', $roleSlug)->get()->getRowArray();
        if (! $role) {
            return;
        }

        $exists = $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $role['id'])
            ->countAllResults();

        if ($exists === 0) {
            $db->table('user_roles')->insert([
                'user_id'     => $userId,
                'role_id'     => (int) $role['id'],
                'assigned_by' => null,
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at'  => null,
            ]);
        }
    }
}
