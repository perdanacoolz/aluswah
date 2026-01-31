<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display public transparency page.
     */
    public function publicIndex(): Response
    {
        // Calculate overall balance
        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;
        
        // Monthly stats
        $monthlyIncome = Transaction::income()->thisMonth()->sum('amount');
        $monthlyExpense = Transaction::expense()->thisMonth()->sum('amount');
        
        // Chart data: Last 6 months
        $chartData = $this->getChartData();
        
        // Recent transactions (last 20)
        $recentTransactions = TransactionResource::collection(
            Transaction::with('verifiedBy')
                ->latest()
                ->take(20)
                ->get()
        );
        
        return Inertia::render('Keuangan/Index', [
            'stats' => [
                'currentBalance' => $currentBalance,
                'formattedBalance' => 'Rp ' . number_format($currentBalance, 0, ',', '.'),
                'monthlyIncome' => $monthlyIncome,
                'formattedMonthlyIncome' => 'Rp ' . number_format($monthlyIncome, 0, ',', '.'),
                'monthlyExpense' => $monthlyExpense,
                'formattedMonthlyExpense' => 'Rp ' . number_format($monthlyExpense, 0, ',', '.'),
            ],
            'chartData' => $chartData,
            'transactions' => $recentTransactions,
        ]);
    }
    
    /**
     * Store a new transaction.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        
        // Handle file upload for expense proof
        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('transactions', 'public');
            $data['proof_image_path'] = $path;
        }
        
        // Add verified_by (current user)
        $data['verified_by'] = auth()->id();
        
        Transaction::create($data);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }
    
    /**
     * Remove the specified transaction.
     */
    public function destroy(int $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Delete proof image if exists
        if ($transaction->proof_image_path) {
            Storage::disk('public')->delete($transaction->proof_image_path);
        }
        
        $transaction->delete();
        
        return redirect()->back()
            ->with('success', 'Transaksi berhasil dihapus!');
    }
    
    /**
     * Get chart data for last 6 months.
     */
    private function getChartData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');
            
            $income = Transaction::income()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
                
            $expense = Transaction::expense()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
            
            $months[] = $monthName;
            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'backgroundColor' => '#ef4444',
                ],
            ],
        ];
    }
}
