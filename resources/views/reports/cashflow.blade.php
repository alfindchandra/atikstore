@extends('layouts.app')

@section('title', 'Cash Flow Report')

@section('content_header')
    <h1>Cash Flow Report</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Cash Flow</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.cashFlow') }}" method="GET">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="start_date">Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="end_date">End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                    </div>
                    <div class="form-group col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Cash Flow Data</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashFlows as $cashFlow)
                        <tr>
                            <td>{{ $cashFlow->date->format('d M Y') }}</td>
                            <td>{{ ucfirst($cashFlow->type) }}</td>
                            <td>Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}</td>
                            <td>{{ $cashFlow->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No cash flow records found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h4>Total Cash In: Rp {{ number_format($cashFlows->where('type', 'in')->sum('amount'), 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-6">
                    <h4>Total Cash Out: Rp {{ number_format($cashFlows->where('type', 'out')->sum('amount'), 0, ',', '.') }}</h4>
                </div>
                <div class="col-md-12">
                    <h4>Net Cash Flow: Rp {{ number_format($cashFlows->where('type', 'in')->sum('amount') - $cashFlows->where('type', 'out')->sum('amount'), 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
@stop