<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function __invoke()
    {
        $currentYear  = now()->year;
        $currentMonth = now()->month;

        // ── Resumen anual ────────────────────────────────────────
        $annualIncome   = Payment::whereYear('payment_date', $currentYear)->sum('amount');
        $annualExpenses = Expense::whereYear('expense_date', $currentYear)->sum('amount');

        // ── Resumen del mes actual ───────────────────────────────
        $monthIncome = Payment::whereYear('payment_date', $currentYear)
            ->whereMonth('payment_date', $currentMonth)
            ->sum('amount');

        $monthExpenses = Expense::whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->sum('amount');

        // ── Por cobrar (proyectos activos) ───────────────────────
        $pendingProjects = Project::with('client')
            ->whereIn('status', ['active', 'paused', 'draft'])
            ->where('budget_amount', '>', 0)
            ->get()
            ->map(function ($p) {
                $paid    = $p->payments()->sum('amount');
                $pending = max(0, $p->budget_amount - $paid);
                return [
                    'id'              => $p->id,
                    'code'            => $p->code,
                    'name'            => $p->name,
                    'client_name'     => $p->client->name,
                    'status'          => $p->status,
                    'status_name'     => $p->status_name,
                    'status_color'    => $p->status_color,
                    'budget'          => $p->budget_amount,
                    'paid'            => $paid,
                    'pending'         => $pending,
                    'formatted_budget'  => $p->formatted_budget,
                    'formatted_paid'    => '$' . number_format($paid, 0, ',', '.'),
                    'formatted_pending' => '$' . number_format($pending, 0, ',', '.'),
                    'payment_pct'     => $p->budget_amount > 0
                        ? (int) min(100, round(($paid / $p->budget_amount) * 100))
                        : 0,
                ];
            })
            ->filter(fn ($p) => $p['pending'] > 0)
            ->values();

        // ── Ingresos mensuales (últimos 6 meses) ─────────────────
        $monthlyIncome = Payment::selectRaw(
                'EXTRACT(YEAR FROM payment_date)::int  AS year,
                 EXTRACT(MONTH FROM payment_date)::int AS month,
                 SUM(amount) AS total'
            )
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupByRaw('EXTRACT(YEAR FROM payment_date), EXTRACT(MONTH FROM payment_date)')
            ->orderByRaw('EXTRACT(YEAR FROM payment_date), EXTRACT(MONTH FROM payment_date)')
            ->get()
            ->map(fn ($r) => [
                'label' => now()->setYear($r->year)->setMonth($r->month)->locale('es')->isoFormat('MMM'),
                'total' => $r->total,
                'formatted' => '$' . number_format($r->total, 0, ',', '.'),
            ]);

        // ── Gastos por categoría (año actual) ────────────────────
        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereYear('expense_date', $currentYear)
            ->groupBy('category')
            ->get()
            ->map(fn ($r) => [
                'category'   => $r->category,
                'label'      => (new \App\Models\Expense(['category' => $r->category]))->category_name,
                'color'      => (new \App\Models\Expense(['category' => $r->category]))->category_color,
                'total'      => $r->total,
                'formatted'  => '$' . number_format($r->total, 0, ',', '.'),
            ]);

        // ── Últimos movimientos ──────────────────────────────────
        $recentPayments = Payment::with('project.client')
            ->latest('payment_date')
            ->take(8)
            ->get()
            ->map(fn ($p) => [
                'id'               => $p->id,
                'project_id'       => $p->project_id,
                'project_name'     => $p->project->name,
                'client_name'      => $p->project->client->name,
                'formatted_amount' => $p->formatted_amount,
                'method_name'      => $p->method_name,
                'method_color'     => $p->method_color,
                'payment_date'     => $p->payment_date->format('d/m/Y'),
                'reference'        => $p->reference,
            ]);

        $recentExpenses = Expense::with('project')
            ->latest('expense_date')
            ->take(8)
            ->get()
            ->map(fn ($e) => [
                'id'               => $e->id,
                'project_id'       => $e->project_id,
                'project_name'     => $e->project->name,
                'description'      => $e->description,
                'category_name'    => $e->category_name,
                'category_color'   => $e->category_color,
                'formatted_amount' => $e->formatted_amount,
                'expense_date'     => $e->expense_date->format('d/m/Y'),
            ]);

        return Inertia::render('Finances/Index', [
            'stats' => [
                'annual_income'    => $annualIncome,
                'annual_expenses'  => $annualExpenses,
                'annual_profit'    => $annualIncome - $annualExpenses,
                'month_income'     => $monthIncome,
                'month_expenses'   => $monthExpenses,
                'month_profit'     => $monthIncome - $monthExpenses,
                'total_pending'    => $pendingProjects->sum('pending'),
                'formatted_annual_income'   => '$' . number_format($annualIncome, 0, ',', '.'),
                'formatted_annual_expenses' => '$' . number_format($annualExpenses, 0, ',', '.'),
                'formatted_annual_profit'   => '$' . number_format($annualIncome - $annualExpenses, 0, ',', '.'),
                'formatted_month_income'    => '$' . number_format($monthIncome, 0, ',', '.'),
                'formatted_month_expenses'  => '$' . number_format($monthExpenses, 0, ',', '.'),
                'formatted_total_pending'   => '$' . number_format($pendingProjects->sum('pending'), 0, ',', '.'),
            ],
            'pendingProjects'    => $pendingProjects,
            'monthlyIncome'      => $monthlyIncome,
            'expensesByCategory' => $expensesByCategory,
            'recentPayments'     => $recentPayments,
            'recentExpenses'     => $recentExpenses,
            'currentYear'        => $currentYear,
        ]);
    }
}
