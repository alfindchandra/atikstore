@extends('layouts.app')

@section('title', 'Manajemen Unit')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Tambah Unit</h1>
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Unit:</label>
                <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="symbol" class="block text-gray-700 font-bold mb-2">Symbol:</label>
                <input type="text" name="symbol" id="symbol" class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Simpan</button>
        </form>
    </div>
@endsection