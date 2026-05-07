<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'lead_owner' => $this->faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
