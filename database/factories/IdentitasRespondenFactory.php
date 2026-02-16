<?php

namespace Database\Factories;

use App\IdentitasResponden;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdentitasRespondenFactory extends Factory
{
    protected $model = IdentitasResponden::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'identitas_instansi_pemerintah' => fake()->randomElement(['satuan kerja', 'direktorat', 'departemen']),
            'alamat' => fake()->address(),
            'nomor_hp' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'nik' => fake()->numerify('################'),
            'nip' => fake()->numerify('##################'),
            'jabatan' => fake()->randomElement(['Direktur', 'Manager', 'Officer']),
        ];
    }
}
