<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\SoftwareHandover;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoftwareHandoverFactory extends Factory
{
    protected $model = SoftwareHandover::class;

    public function definition(): array
    {
        return [
            'lead_id'      => Lead::factory(),
            'company_name' => $this->faker->company(),
            'implementer'  => $this->faker->name(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
