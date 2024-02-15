<?php

namespace Database\Factories;

use App\Models\Admin\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'
            =>
            $this->faker->sentence(5),
            'meta_title'
            =>
            $this->faker->realText(rand(80, 255)),
            'meta_keywords'
            =>
            $this->faker->realText(rand(80, 255)),
            'description'
            => $this->faker->realText(rand(80, 255)),
            'meta_description'
            => $this->faker->realText(rand(80, 255)),
            'name'
            => $this->faker->sentence(5),
            'page_slug'
            =>
            str_replace('--', '-', strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', trim($this->faker->sentence(5))))),
            'is_static' =>
            rand(0, 1),
            'status'
            => rand(0, 1),
            'page_header_image'
            => NULL,
            'created_by'
            =>
            User::factory(),
            'updated_by'
            =>
            User::factory(),
            'created_at'
            => now(),
        ];
    }
}
