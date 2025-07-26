<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Services\CashFlowService;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    protected CashFlowService $cashFlowService;

    public function __construct(CashFlowService $cashFlowService)
    {
        $this->cashFlowService = $cashFlowService;
    }

    public function index()
    {
        $cashFlows = CashFlow::orderBy('transaction_date', 'desc')->paginate(20);
        $todayFlow = $this->cashFlowService->getDailyCashFlow();
        $monthlyFlow = $this->cashFlowService->getMonthlyCashFlow();

        return view('cashflow.index', compact('cashFlows', 'todayFlow', 'monthlyFlow'));
    }

    public function create()
    {
        return view('cashflow.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        try {
            CashFlow::create([
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,
            ]);

            return redirect()->route('cashflow.index')
                ->with('success', 'Catatan keuangan berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan catatan: ' . $e->getMessage()]);
        }
    }

    public function edit(CashFlow $cashFlow)
    {
        return view('cashflow.edit', compact('cashFlow'));
    }

    public function update(Request $request, CashFlow $cashFlow)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        try {
            $cashFlow->update([
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,
            ]);

            return redirect()->route('cashflow.index')
                ->with('success', 'Catatan keuangan berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui catatan: ' . $e->getMessage()]);
        }
    }

    public function destroy(CashFlow $cashFlow)
    {
        // Tidak bisa hapus catatan dari transaksi otomatis
        if ($cashFlow->reference_type === 'transaction') {
            return back()->withErrors(['error' => 'Catatan dari transaksi tidak dapat dihapus']);
        }

        $cashFlow->delete();

        return redirect()->route('cashflow.index')
            ->with('success', 'Catatan keuangan berhasil dihapus');
    }
}