<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $user    = User::first();
        $clients = Client::all();

        $quotes = [
            [
                'client'  => $clients[0],
                'title'   => 'Sistema de gestión de obras',
                'status'  => 'approved',
                'items'   => [
                    ['description' => 'Análisis y levantamiento de requerimientos', 'quantity' => 1, 'unit_price' => 350000],
                    ['description' => 'Diseño de base de datos y arquitectura', 'quantity' => 1, 'unit_price' => 280000],
                    ['description' => 'Módulo de gestión de obras', 'detail' => 'CRUD completo con seguimiento de avance', 'quantity' => 1, 'unit_price' => 850000],
                    ['description' => 'Módulo de personal y asistencia', 'quantity' => 1, 'unit_price' => 620000],
                    ['description' => 'Módulo de materiales e inventario', 'quantity' => 1, 'unit_price' => 580000],
                    ['description' => 'Panel de reportes y estadísticas', 'quantity' => 1, 'unit_price' => 420000],
                    ['description' => 'Configuración de servidor en producción', 'quantity' => 1, 'unit_price' => 180000],
                    ['description' => 'Capacitación y documentación', 'detail' => '8 horas de capacitación al equipo', 'quantity' => 1, 'unit_price' => 240000],
                ],
            ],
            [
                'client'  => $clients[1],
                'title'   => 'Portal de pacientes online',
                'status'  => 'sent',
                'valid_until' => now()->addDays(15),
                'items'   => [
                    ['description' => 'Diseño UX/UI del portal', 'quantity' => 1, 'unit_price' => 450000],
                    ['description' => 'Módulo de agendamiento de horas', 'detail' => 'Con notificaciones por email y SMS', 'quantity' => 1, 'unit_price' => 980000],
                    ['description' => 'Integración con sistema interno de la clínica', 'quantity' => 1, 'unit_price' => 650000],
                    ['description' => 'App móvil (iOS y Android)', 'detail' => 'React Native, publicación en stores', 'quantity' => 1, 'unit_price' => 1800000],
                    ['description' => 'Configuración y despliegue en AWS', 'quantity' => 1, 'unit_price' => 320000],
                ],
            ],
            [
                'client'  => $clients[3],
                'title'   => 'E-commerce con integración Webpay',
                'status'  => 'draft',
                'valid_until' => now()->addDays(30),
                'items'   => [
                    ['description' => 'Diseño de tienda online', 'quantity' => 1, 'unit_price' => 380000],
                    ['description' => 'Catálogo de productos con filtros avanzados', 'quantity' => 1, 'unit_price' => 420000],
                    ['description' => 'Integración Webpay Plus', 'detail' => 'Pago con tarjetas débito y crédito', 'quantity' => 1, 'unit_price' => 350000],
                    ['description' => 'Panel de administración', 'quantity' => 1, 'unit_price' => 280000],
                    ['description' => 'Sistema de notificaciones de pedidos', 'quantity' => 1, 'unit_price' => 180000],
                ],
            ],
        ];

        foreach ($quotes as $data) {
            $quote = Quote::create([
                'client_id'    => $data['client']->id,
                'created_by'   => $user->id,
                'quote_number' => Quote::generateNumber(),
                'title'        => $data['title'],
                'status'       => $data['status'],
                'currency'     => 'CLP',
                'tax_rate'     => 19,
                'tax_included' => false,
                'valid_until'  => $data['valid_until'] ?? null,
                'notes'        => 'Cotización preparada con todos los detalles del proyecto. Cualquier consulta no dude en contactarnos.',
                'terms'        => 'Pago: 50% anticipo al inicio, 50% contra entrega. Vigencia: 30 días desde emisión.',
                'sent_at'      => in_array($data['status'], ['sent', 'approved']) ? now()->subDays(5) : null,
                'approved_at'  => $data['status'] === 'approved' ? now()->subDays(2) : null,
            ]);

            foreach ($data['items'] as $i => $itemData) {
                $quote->items()->create([
                    'description' => $itemData['description'],
                    'detail'      => $itemData['detail'] ?? null,
                    'quantity'    => $itemData['quantity'],
                    'unit_price'  => $itemData['unit_price'],
                    'order'       => $i,
                ]);
            }

            $quote->recalculateTotals();
        }
    }
}
