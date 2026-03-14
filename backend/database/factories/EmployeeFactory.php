<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'specialization_id' => function () {
                return Specialization::firstOrCreate(
                    ['title' => 'Test Specialization'],
                    ['title' => 'Test Specialization'],
                )->id;
            },
            'confirmed' => true,
        ];
    }

    /**
     * Indicate that the employee is not confirmed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unconfirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'confirmed' => false,
            ];
        });
    }
}
