<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        $user     = User::first();
        $projects = Project::all();

        // ── Proyecto 1: Sistema de gestión de obras ────────────
        Server::create([
            'project_id'         => $projects[0]->id,
            'created_by'         => $user->id,
            'name'               => 'Servidor de producción',
            'type'               => 'vps',
            'provider'           => 'DigitalOcean',
            'ip_address'         => '167.99.120.45',
            'hostname'           => 'server1.delvalle.cl',
            'os'                 => 'Ubuntu 22.04 LTS',
            'php_version'        => '8.3',
            'db_type'            => 'mysql',
            'db_version'         => '8.0',
            'panel'              => 'forge',
            'web_server'         => 'nginx',
            'ssh_user'           => 'forge',
            'ssh_port'           => 22,
            'domain'             => 'delvalle.cl',
            'url'                => 'https://delvalle.cl',
            'status'             => 'active',
            'hosting_expires_at' => now()->addDays(45),
            'domain_expires_at'  => now()->addDays(18), // alerta: critical
            'ssl_expires_at'     => now()->addDays(60),
            'credentials'        => [
                'ssh_password'   => 'S3cur3P@ss!2024',
                'db_user'        => 'delvalle_user',
                'db_password'    => 'DbP@ss#2024',
                'db_name'        => 'delvalle_prod',
                'panel_url'      => 'https://forge.laravel.com',
                'panel_user'     => 'admin@delvalle.cl',
                'panel_password' => 'ForgeP@ss2024!',
            ],
            'notes'              => "Droplet 2 vCPU / 4 GB RAM / 80 GB SSD\nForge configurado con queue worker y scheduler\nBackup diario a las 3am (Spaces S3)",
        ]);

        // ── Proyecto 2: Portal de pacientes ────────────────────
        Server::create([
            'project_id'         => $projects[1]->id,
            'created_by'         => $user->id,
            'name'               => 'Servidor de desarrollo',
            'type'               => 'vps',
            'provider'           => 'Hetzner',
            'ip_address'         => '49.13.88.201',
            'os'                 => 'Ubuntu 22.04 LTS',
            'php_version'        => '8.3',
            'db_type'            => 'mysql',
            'db_version'         => '8.0',
            'panel'              => 'ploi',
            'web_server'         => 'nginx',
            'ssh_user'           => 'root',
            'ssh_port'           => 22,
            'domain'             => 'dev.saludnorte.cl',
            'status'             => 'active',
            'hosting_expires_at' => now()->addDays(90),
            'domain_expires_at'  => now()->addDays(120),
            'credentials'        => [
                'ssh_password'   => 'H3tzn3r@Prod!',
                'db_user'        => 'salud_dev',
                'db_password'    => 'DevDB#2024',
                'db_name'        => 'salud_norte_dev',
                'panel_url'      => 'https://ploi.io',
            ],
            'notes'              => "CX22 — 2 vCPU / 4 GB RAM\nAmbiente de desarrollo y testing",
        ]);

        // ── Proyecto 3: Landing (completado, servidor activo) ───
        Server::create([
            'project_id'         => $projects[2]->id,
            'created_by'         => $user->id,
            'name'               => 'Hosting compartido',
            'type'               => 'shared',
            'provider'           => 'Hostinger',
            'ip_address'         => '31.220.55.192',
            'os'                 => 'cPanel / CloudLinux',
            'php_version'        => '8.1',
            'db_type'            => 'mysql',
            'db_version'         => '5.7',
            'panel'              => 'cpanel',
            'web_server'         => 'apache',
            'ssh_user'           => 'u123456789',
            'ssh_port'           => 65002,
            'domain'             => 'robertosoto.cl',
            'url'                => 'https://robertosoto.cl',
            'status'             => 'active',
            'hosting_expires_at' => now()->addDays(-5), // VENCIDO para demo
            'domain_expires_at'  => now()->addDays(200),
            'credentials'        => [
                'panel_url'      => 'https://cpanel.hostinger.com',
                'panel_user'     => 'u123456789',
                'panel_password' => 'Hosting@2024!',
                'db_user'        => 'u123_rsoto',
                'db_password'    => 'MySqlDb#123',
                'db_name'        => 'u123_robertosoto',
            ],
            'notes'              => "Plan Business Hostinger\nSolo HTML/CSS + PHP básico\nRenovar hosting URGENTE",
        ]);
    }
}
