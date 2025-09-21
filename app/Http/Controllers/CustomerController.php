<?php

namespace App\Http\Controllers;

use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use App\Models\Transaction;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = CustomerDebt::with(['transaction', 'payments']);

        // Filter berdasarkan status
        if ($status !== 'all') {
            if ($status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $status);
            }
        }

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $debts = $query->orderBy('debt_date', 'desc')->paginate(20);

        // Summary statistics
        $stats = [
            'total_debts' => CustomerDebt::sum('remaining_amount'),
            'active_customers' => CustomerDebt::where('status', '!=', 'paid')
                                              ->distinct('customer_name')
                                              ->count(),
            'overdue_debts' => CustomerDebt::overdue()->sum('remaining_amount'),
            'total_paid' => CustomerDebt::sum('paid_amount'),
        ];

        return view('debts.index', compact('debts', 'stats', 'status', 'search'));
    }

    public function create()
    {
        // Ambil transaksi hari ini untuk opsi
        $todayTransactions = Transaction::with(['details.product'])
            ->whereDate('transaction_date', today())
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('debts.create', compact('todayTransactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'debt_amount' => 'required|numeric|min:1',
            'transaction_id' => 'nullable|exists:transactions,id',
            'items_purchased' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Jika ada transaction_id, ambil data dari transaksi
                $itemsData = null;
                if ($request->transaction_id) {
                    $transaction = Transaction::with(['details.product', 'details.unit'])->find($request->transaction_id);
                    if ($transaction) {
                        $itemsData = $transaction->details->map(function ($detail) {
                            return [
                                'name' => $detail->product->name,
                                'quantity' => $detail->quantity,
                                'unit' => $detail->unit->symbol ?? 'pcs',
                                'price' => $detail->unit_price,
                                'subtotal' => $detail->subtotal,
                            ];
                        })->toArray();
                    }
                } else {
                    // Manual input items
                    if ($request->items_purchased) {
                        $itemsData = $request->items_purchased;
                    }
                }

                $debt = CustomerDebt::create([
                    'customer_name' => $request->customer_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'debt_amount' => $request->debt_amount,
                    'remaining_amount' => $request->debt_amount,
                    'transaction_id' => $request->transaction_id,
                    'items_purchased' => $itemsData,
                    'notes' => $request->notes,
                    'debt_date' => $request->debt_date,
                    'due_date' => $request->due_date,
                    'status' => 'active',
                ]);

                // Catat cash flow sebagai piutang
                CashFlow::create([
                    'type' => 'expense', // Piutang dianggap sebagai pengeluaran kas sementara
                    'category' => 'Piutang Pelanggan',
                    'amount' => $request->debt_amount,
                    'description' => 'Piutang dari ' . $request->customer_name,
                    'reference_type' => 'customer_debt',
                    'reference_id' => $debt->id,
                    'transaction_date' => $request->debt_date,
                ]);
            });

            return redirect()->route('debts.index')
                ->with('success', 'Hutang pelanggan berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan hutang: ' . $e->getMessage()]);
        }
    }

    public function show(CustomerDebt $debt)
    {
        $debt->load(['transaction.details.product', 'payments']);
        return view('debts.show', compact('debt'));
    }

    public function edit(CustomerDebt $debt)
    {
        return view('debts.edit', compact('debt'));
    }

    public function update(Request $request, CustomerDebt $debt)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'debt_amount' => 'required|numeric|min:1',
            'items_purchased' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
        ]);

        try {
            $oldAmount = $debt->debt_amount;
            
            $debt->update([
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'debt_amount' => $request->debt_amount,
                'remaining_amount' => $request->debt_amount - $debt->paid_amount,
                'items_purchased' => $request->items_purchased,
                'notes' => $request->notes,
                'debt_date' => $request->debt_date,
                'due_date' => $request->due_date,
            ]);

            // Update status
            $debt->updateStatus();

            // Update cash flow jika ada perubahan amount
            if ($oldAmount != $request->debt_amount) {
                $cashFlow = CashFlow::where('reference_type', 'customer_debt')
                    ->where('reference_id', $debt->id)
                    ->first();

                if ($cashFlow) {
                    $cashFlow->update([
                        'amount' => $request->debt_amount,
                        'description' => 'Piutang dari ' . $request->customer_name . ' (Diperbarui)',
                    ]);
                }
            }

            return redirect()->route('debts.index')
                ->with('success', 'Hutang pelanggan berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui hutang: ' . $e->getMessage()]);
        }
    }

    public function destroy(CustomerDebt $debt)
    {
        if ($debt->paid_amount > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus hutang yang sudah ada pembayaran']);
        }

        try {
            DB::transaction(function () use ($debt) {
                // Hapus cash flow terkait
                CashFlow::where('reference_type', 'customer_debt')
                    ->where('reference_id', $debt->id)
                    ->delete();

                // Hapus payments (seharusnya tidak ada karena sudah dicek di atas)
                $debt->payments()->delete();

                // Hapus debt
                $debt->delete();
            });

            return redirect()->route('debts.index')
                ->with('success', 'Hutang pelanggan berhasil dihapus');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus hutang: ' . $e->getMessage()]);
        }
    }

    public function addPayment(Request $request, CustomerDebt $debt)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $debt) {
                // Tambah pembayaran
                DebtPayment::create([
                    'customer_debt_id' => $debt->id,
                    'payment_amount' => $request->payment_amount,
                    'payment_date' => $request->payment_date,
                    'notes' => $request->notes,
                ]);

                // Update debt
                $debt->paid_amount += $request->payment_amount;
                $debt->remaining_amount -= $request->payment_amount;
                $debt->save();

                // Update status
                $debt->updateStatus();

                // Catat cash flow untuk pembayaran
                CashFlow::create([
                    'type' => 'income',
                    'category' => 'Pembayaran Piutang',
                    'amount' => $request->payment_amount,
                    'description' => 'Pembayaran piutang dari ' . $debt->customer_name,
                    'reference_type' => 'debt_payment',
                    'reference_id' => $debt->id,
                    'transaction_date' => $request->payment_date,
                ]);
            });

            return redirect()->route('debts.show', $debt)
                ->with('success', 'Pembayaran berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan pembayaran: ' . $e->getMessage()]);
        }
    }

    public function getTransactionItems(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if (!$transactionId) {
            return response()->json(['items' => '']);
        }

        $transaction = Transaction::with(['details.product', 'details.unit'])
            ->find($transactionId);

        if (!$transaction) {
            return response()->json(['items' => '']);
        }

        $items = $transaction->details->map(function ($detail) {
            return $detail->product->name . ' (' . $detail->quantity . ' ' . ($detail->unit->symbol ?? 'pcs') . ')';
        })->implode(', ');

        return response()->json([
            'items' => $items,
            'total_amount' => $transaction->total_amount
        ]);
    }
}