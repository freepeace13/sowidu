<?php

namespace Database\Seeders;

use App\Actions\RegistersUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->registerUsers([
            [
                'username' => 'sg',
                'first_name' => 'Sebastian',
                'last_name' => 'Goebel',
                'email' => 'sg@gs-goebel-haustechnik.de',
                'password' => 'S0widu@2018',
            ],
            [
                'username' => 'rjfabiania',
                'first_name' => 'Rj',
                'last_name' => 'Fabiania',
                'email' => 'rj.fabiania.wamal@gmail.com',
                'password' => 'secret',
            ],
            [
                'username' => 'kin.basco',
                'first_name' => 'Kin',
                'last_name' => 'Basco',
                'email' => 'freepeace13@gmail.com',
                'password' => 'secret',
            ],
        ]);
    }

    protected function registerUsers(array $users)
    {
        foreach ($users as $attribute) {
            if (User::whereUsername($attribute['username'])->doesntExist()) {
                app(RegistersUser::class)->register($attribute);
            }
        }
    }
}
