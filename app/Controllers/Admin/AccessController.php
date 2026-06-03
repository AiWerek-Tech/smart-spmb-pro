<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\RolePermissionModel;
use App\Models\SettingModel;
use App\Services\RbacService;

class AccessController extends BaseController
{
    private RoleModel $roleModel;
    private PermissionModel $permissionModel;
    private RolePermissionModel $rolePermissionModel;
    private SettingModel $settingModel;
    private RbacService $rbacService;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
        $this->settingModel = new SettingModel();
        $this->rbacService = new RbacService();
    }

    public function index()
    {
        $roles = $this->roleModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[(int) $role['id']] = $this->rolePermissionModel->permissionKeysForRole((int) $role['id']);
        }

        return view('admin/access/index', [
            'title'           => 'Mode & Hak Akses',
            'roles'           => $roles,
            'permissions'     => $this->permissionModel->activeGrouped(),
            'rolePermissions' => $rolePermissions,
            'operationalMode' => $this->settingModel->getValue('school_operational_mode', 'small'),
        ]);
    }

    public function saveMode()
    {
        $mode = (string) $this->request->getPost('school_operational_mode');
        if (!in_array($mode, ['small', 'standard', 'foundation'], true)) {
            return redirect()->back()->with('error', 'Mode operasional sekolah tidak valid.');
        }

        $this->settingModel->setValue('school_operational_mode', $mode);

        return redirect()->to('admin/access')->with('success', 'Mode operasional sekolah berhasil diperbarui.');
    }

    public function storeRole()
    {
        $rules = [
            'name'        => 'required|max_length[120]',
            'slug'        => 'required|alpha_dash|max_length[50]|is_unique[roles.slug]',
            'base_role'   => 'required|in_list[admin,operator,pendaftar]',
            'description' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->rbacService->createRole([
            'name'        => $this->request->getPost('name'),
            'slug'        => strtolower((string) $this->request->getPost('slug')),
            'base_role'   => $this->request->getPost('base_role'),
            'description' => $this->request->getPost('description'),
        ], $this->request->getPost('permissions') ?? []);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('admin/access')->with('success', $result['message']);
    }

    public function updateRole(int $id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('admin/access')->with('error', 'Role tidak ditemukan.');
        }

        $rules = [
            'name'        => 'required|max_length[120]',
            'base_role'   => 'required|in_list[admin,operator,pendaftar]',
            'description' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->rbacService->updateRole($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active'   => $this->request->getPost('is_active') !== null ? 1 : 0,
            'base_role'   => $this->request->getPost('base_role'),
        ], $this->request->getPost('permissions') ?? []);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('admin/access')->with('success', $result['message']);
    }

    public function duplicateRole(int $id)
    {
        $result = $this->rbacService->duplicateRole($id);

        return redirect()->to('admin/access')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function deleteRole(int $id)
    {
        $result = $this->rbacService->deleteRole($id);

        return redirect()->to('admin/access')->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
