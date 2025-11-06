<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Client::factory(5)
            ->has(Quotation::factory(2)
                ->has(QuotationItem::factory(3)))
            ->has(Project::factory(1)
                ->has(ProjectTask::factory(4)))
            ->create();
    }
}
