<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\Lead;
use App\Models\SoftwareHandover;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImplementerTicketFactory extends Factory
{
    protected $model = ImplementerTicket::class;

    public function definition(): array
    {
        return [
            'customer_id'          => Customer::factory(),
            'implementer_user_id'  => User::factory(),
            'implementer_name'     => $this->faker->name(),
            'lead_id'              => Lead::factory(),
            'software_handover_id' => SoftwareHandover::factory(),
            'subject'              => $this->faker->sentence(),
            'description'          => $this->faker->paragraph(),
            'status'               => 'open',
            'priority'             => 'medium',
            'category'             => 'Kick-Off Meeting',
            'module'               => 'General',
        ];
    }
}
