<?php

namespace Tests;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Setting\Staff\Role as DefaultRoles;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Setting\Staff\Permission as DefaultPermission;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        $this->CreateRolesAndPermission();
    }

    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        $this->actingAs($user);

        return $this;
    }

    protected function signInOwner($owner = null)
    {
        $owner = $owner ?: create('App\User');
        $owner->assignRole('owner');

        $this->actingAs($owner);

        return $this;
    }

    protected function signInAdministrator($administrator = null)
    {
        $administrator = $administrator ?: create('App\User');
        $administrator->assignRole('administrator');

        $this->actingAs($administrator);

        return $this;
    }

    protected function signInAdminGroup($adminGroup = null)
    {
        $adminGroup = $adminGroup ?: create('App\User');
        $adminGroup->assignRole('admin-group');

        $this->actingAs($adminGroup);

        return $this;
    }

    protected function signInDoctor($doctor = null)
    {
        $doctor = $doctor ?: create('App\User');
        $doctor->assignRole('doctor');

        $this->actingAs($doctor);

        return $this;
    }

    protected function signInAdminCounter($adminCounter = null)
    {
        $adminCounter = $adminCounter ?: create('App\User');
        $adminCounter->assignRole('admin-counter');

        $this->actingAs($adminCounter);

        return $this;
    }

    protected function CreateRolesAndPermission()
    {
        $permissions = DefaultPermission::defaultPermissions();

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $roles = DefaultRoles::defaultRoles();

        foreach ($roles as $role) {
            $role = Role::firstOrCreate(['name' => $role]);

            if ($role->name == 'owner') {
                $role->syncPermissions(Permission::all());
            }

            if ($role->name == 'administrator') {
                $role->givePermissionTo([
                    'edit-groups', 'view-invitation-groups', 'view-staffs-groups',
                ]);
            }

            if ($role->name == 'admin-group') {
                $role->givePermissionTo([
                    'edit-group', 'checkin-appointment-group', 'view-invitation-group', 'view-staffs-group',
                ]);
            }

            $this->app->make(PermissionRegistrar::class)->registerPermissions();
        }
    }
}
