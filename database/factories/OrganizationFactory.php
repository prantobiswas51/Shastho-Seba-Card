<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'logo' => 'images/organizations/' . $this->faker->unique()->uuid . '.png',
            'address' => $this->faker->address,
            'maxDiscount' => $this->faker->numberBetween(5, 30),
            'minDiscount' => $this->faker->numberBetween(1, 10),
            'district_id' => $this->faker->numberBetween(1, 2), // Adjust as needed
            'sub_district_id' => $this->faker->numberBetween(1, 2), // Adjust as needed
        ];
    }
}
