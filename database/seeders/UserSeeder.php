<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //----------------------------------------------
        DB::table('users')->truncate(); //delete Old Data in Table

        $users = array(
            array('id' => '1','name' => 'Site Manager','phone' => NULL,'type_id' => '1','email' => 'superadmin@site.com','password' => bcrypt('aa112233') ,'isActivated' => '1','image' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2022-08-11 20:40:54','updated_at' => '2024-03-25 09:28:36'),
            array('id' => '2','name' => 'Admin','phone' => NULL,'type_id' => '2','email' => 'admin@site.com','password' => bcrypt('aa112233'),'isActivated' => '1','image' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2022-08-11 20:40:54','updated_at' => '2023-04-10 09:50:26'),
            array('id' => '3','name' => 'User 1','phone' => NULL,'type_id' => '3','email' => 'user@site.com','password' => bcrypt('1234567890'),'isActivated' => '1','image' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2022-08-11 20:40:54','updated_at' => '2022-08-11 20:40:54'),
        );
        //---------------------------------------
        DB::table('users')->insert($users);
        //---------------------------------------
    }
}
