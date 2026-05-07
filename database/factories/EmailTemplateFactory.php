<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'name'         => 'Follow Up - ' . $this->faker->word(),
            'subject'      => $this->faker->sentence(),
            'content'      => '<p>' . $this->faker->paragraph() . '</p>',
            'type'         => 'implementer',
            'category'     => null,
            'thread_label' => 'Test Label',
        ];
    }

    public function kickOff(): self
    {
        return $this->state(fn () => [
            'name'         => 'Session - Completed Online Kick-Off Meeting',
            'thread_label' => 'Kick-Off Meeting',
        ]);
    }

    public function withoutLabel(): self
    {
        return $this->state(fn () => ['thread_label' => null]);
    }
}
