<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Rules\ValidRut;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'type'           => 'company',
                'name'           => 'Constructora Del Valle SpA',
                'rut'            => '76.123.456-7',
                'email'          => 'contacto@delvalle.cl',
                'phone'          => '+56 2 2345 6789',
                'contact_person' => 'Carlos Mendoza',
                'city'           => 'Santiago',
                'region'         => 'Metropolitana',
                'active'         => true,
            ],
            [
                'type'           => 'company',
                'name'           => 'Clínica Salud Norte Ltda.',
                'rut'            => '79.876.543-2',
                'email'          => 'admin@saludnorte.cl',
                'phone'          => '+56 55 234 5678',
                'contact_person' => 'María Fuentes',
                'city'           => 'Antofagasta',
                'region'         => 'Antofagasta',
                'active'         => true,
            ],
            [
                'type'           => 'person',
                'name'           => 'Roberto Soto Araya',
                'rut'            => '12.345.678-9',
                'email'          => 'roberto.soto@gmail.com',
                'phone'          => '+56 9 8765 4321',
                'city'           => 'Valparaíso',
                'region'         => 'Valparaíso',
                'active'         => true,
            ],
            [
                'type'           => 'company',
                'name'           => 'Distribuidora Pacifico S.A.',
                'rut'            => '96.543.210-K',
                'email'          => 'tecnologia@pacifico.cl',
                'phone'          => '+56 2 2987 6543',
                'contact_person' => 'Ana González',
                'city'           => 'Santiago',
                'region'         => 'Metropolitana',
                'active'         => true,
            ],
        ];

        foreach ($clients as $data) {
            // El mutator del modelo se encarga del formato del RUT
            Client::create($data);
        }
    }
}
