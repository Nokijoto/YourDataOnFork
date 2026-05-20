<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        // Seed default Sherlock services
        $services = [
            ['name' => 'GitHub', 'url_pattern' => 'https://github.com/{}'],
            ['name' => 'Twitter / X', 'url_pattern' => 'https://x.com/{}'],
            ['name' => 'Reddit', 'url_pattern' => 'https://reddit.com/user/{}'],
            ['name' => 'Instagram', 'url_pattern' => 'https://instagram.com/{}'],
            ['name' => 'TikTok', 'url_pattern' => 'https://tiktok.com/@{}'],
            ['name' => 'LinkedIn', 'url_pattern' => 'https://linkedin.com/in/{}'],
        ];

        $serviceModels = [];
        foreach ($services as $service) {
            $serviceModels[$service['name']] = \App\Models\SherlockService::firstOrCreate(
                ['name' => $service['name']],
                ['url_pattern' => $service['url_pattern'], 'is_active' => true]
            );
        }

        // Seed default Sherlock rules
        $rules = [
            [
                'username' => 'admin',
                'service' => 'GitHub',
                'is_found' => true,
            ],
            [
                'username' => 'admin',
                'service' => 'Twitter / X',
                'is_found' => true,
            ],
            [
                'username' => 'admin',
                'service' => 'Reddit',
                'is_found' => false,
            ],
            [
                'username' => 'guest',
                'service' => 'Reddit',
                'is_found' => true,
            ],
            [
                'username' => 'guest',
                'service' => 'Instagram',
                'is_found' => true,
            ],
            [
                'username' => 'guest',
                'service' => 'GitHub',
                'is_found' => false,
            ],
        ];

        foreach ($rules as $rule) {
            $service = $serviceModels[$rule['service']] ?? null;
            if ($service) {
                \App\Models\SherlockRule::firstOrCreate(
                    [
                        'username' => $rule['username'],
                        'service_id' => $service->id,
                    ],
                    [
                        'is_found' => $rule['is_found'],
                    ]
                );
            }
        }
    }
}
