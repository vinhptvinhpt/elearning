<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Root',
                'guard_name' => 'web',
                'created_at' => '2019-08-16 17:00:00',
                'updated_at' => '2019-10-15 07:03:04',
                'description' => 'Root',
                'mdl_role_id' => 1,
                'status' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'coursecreator',
                'guard_name' => 'web',
                'created_at' => '2019-08-16 17:00:00',
                'updated_at' => '2019-08-16 17:00:00',
                'description' => 'coursecreator',
                'mdl_role_id' => 2,
                'status' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'editingteacher',
                'guard_name' => 'web',
                'created_at' => '2019-08-16 17:00:00',
                'updated_at' => '2019-10-23 16:06:59',
                'description' => 'editingteacher',
                'mdl_role_id' => 3,
                'status' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'teacher',
                'guard_name' => 'web',
                'created_at' => '2019-08-16 17:00:00',
                'updated_at' => '2019-08-16 17:00:00',
                'description' => 'teacher',
                'mdl_role_id' => 4,
                'status' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'student',
                'guard_name' => 'web',
                'created_at' => '2019-08-16 17:00:00',
                'updated_at' => '2019-08-16 17:00:00',
                'description' => 'student',
                'mdl_role_id' => 5,
                'status' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'managemarket',
                'guard_name' => 'web',
                'created_at' => '2019-08-26 08:39:40',
                'updated_at' => '2019-11-14 08:01:12',
                'description' => 'Chuyên viên kinh doanh',
                'mdl_role_id' => 9,
                'status' => 0,
            ),
            6 => 
            array (
                'id' => 10,
                'name' => 'manageagents',
                'guard_name' => 'web',
                'created_at' => '2019-10-16 08:56:53',
                'updated_at' => '2019-10-22 16:52:44',
                'description' => 'Quyền dành cho trưởng đại lý',
                'mdl_role_id' => 59,
                'status' => 0,
            ),
            7 => 
            array (
                'id' => 11,
                'name' => 'managepos',
                'guard_name' => 'web',
                'created_at' => '2019-10-16 08:57:24',
                'updated_at' => '2019-10-16 08:57:24',
                'description' => 'Quyền dành cho trưởng điểm bán',
                'mdl_role_id' => 60,
                'status' => 0,
            ),
            8 => 
            array (
                'id' => 15,
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => '2019-11-03 08:28:46',
                'updated_at' => '2019-11-03 08:28:46',
                'description' => 'Tất cả các quyền',
                'mdl_role_id' => 64,
                'status' => 1,
            ),
            9 => 
            array (
                'id' => 26,
                'name' => 'Leader',
                'guard_name' => 'web',
                'created_at' => '2020-03-09 10:02:15',
                'updated_at' => '2020-03-09 10:02:15',
                'description' => 'Leader of an organization',
                'mdl_role_id' => 77,
                'status' => 1,
            ),
            10 => 
            array (
                'id' => 27,
                'name' => 'Manager',
                'guard_name' => 'web',
                'created_at' => '2020-03-09 10:02:25',
                'updated_at' => '2020-03-09 10:02:25',
                'description' => 'Manager of an organization',
                'mdl_role_id' => 78,
                'status' => 1,
            ),
            11 => 
            array (
                'id' => 28,
                'name' => 'Employee',
                'guard_name' => 'web',
                'created_at' => '2020-03-09 10:03:15',
                'updated_at' => '2020-03-09 10:03:15',
                'description' => 'Employee of an organization',
                'mdl_role_id' => 79,
                'status' => 1,
            ),
        ));
        
        
    }
}