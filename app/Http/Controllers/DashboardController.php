<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Slide;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $role = $user->role;
        $dashboardType = $this->getDashboardType($role);

        $stats = $this->getStatsByRole($role);
        $recentTransactions = $this->getRecentTransactions($role);

        return Inertia::render('Dashboard', [
            'userRole' => $role,
            'dashboardType' => $dashboardType,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            // Additional data specific to roles can be pushed here
        ]);
    }

    private function getDashboardType($role)
    {
        return match ($role) {
            'super_admin' => 'admin',
            'ketua' => 'executive',
            'bendahara' => 'finance',
            'marbot' => 'operations',
            default => 'default',
        };
    }

    private function getStatsByRole($role)
    {
        // Common Financial Stats
        $totalIncome = Transaction::income()->approved()->sum('amount');
        $totalExpense = Transaction::expense()->approved()->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $baseStats = [
            'formattedBalance' => 'Rp ' . number_format($balance, 0, ',', '.'),
            'balance' => $balance,
        ];

        switch ($role) {
            case 'super_admin':
                return array_merge($baseStats, [
                    'totalUsers' => User::count(),
                    'totalTransactions' => Transaction::count(),
                    'pendingApprovals' => Transaction::pending()->count(),
                    'systemHealth' => 'Online',
                    'monthlyIncome' => $this->getMonthlySum('income'),
                    'monthlyExpense' => $this->getMonthlySum('expense'),
                ]);

            case 'ketua':
                return array_merge($baseStats, [
                    'pendingApprovals' => Transaction::pending()->count(),
                    'totalAssets' => Asset::count(),
                    'monthlyIncome' => $this->getMonthlySum('income'),
                    'monthlyExpense' => $this->getMonthlySum('expense'),
                ]);

            case 'bendahara':
                return array_merge($baseStats, [
                    'formattedMonthlyIncome' => 'Rp ' . number_format($this->getMonthlySum('income'), 0, ',', '.'),
                    'formattedMonthlyExpense' => 'Rp ' . number_format($this->getMonthlySum('expense'), 0, ',', '.'),
                ]);

            case 'marbot':
                $activeSlides = Slide::where('is_active', true)->count();
                $brokenAssets = Asset::where('condition', '!=', 'Baik')->count();
                return [
                    'activeSlides' => $activeSlides,
                    'brokenAssets' => $brokenAssets,
                    'message' => 'Sistem TV Online',
                    'description' => "Saat ini ada $activeSlides slide aktif yang ditampilkan.",
                ];

            default:
                return [];
        }
    }

    private function getRecentTransactions($role)
    {
        if ($role === 'marbot') return [];

        $query = Transaction::with('user');
        
        if ($role === 'ketua') {
            // For Chairperson, prioritize Pending items
            $query->orderByRaw("CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                WHEN 'rejected' THEN 3 
                ELSE 4 END");
        }
        
        return $query->latest()
            ->take(10)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'category' => $t->category,
                'amount' => $t->amount,
                'formatted_amount' => $t->formatted_amount,
                'description' => $t->description,
                'status' => $t->status,
                'date' => $t->date->format('d M Y'),
                'user_name' => $t->user->name ?? 'System',
            ]);
    }

    private function getMonthlySum($type)
    {
        return Transaction::where('type', $type)
            ->approved()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
    }
}
