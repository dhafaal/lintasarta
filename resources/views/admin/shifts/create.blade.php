@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
<x-section-content title="Tambah Shift" subtitle="Buat shift baru untuk jadwal kerja">
    <form method="POST" action="{{ route('admin.shifts.store') }}" class="space-y-6">
        @csrf

        <x-select 
            name="name" 
            label="Nama Shift" 
            :options="['Pagi' => 'Pagi', 'Siang' => 'Siang', 'Malam' => 'Malam']" 
            required 
        />

        <div class="flex items-center space-x-2">
        <x-input 
            type="time" 
            name="start_time" 
            label="Jam Mulai" 
            required 
        />

        <x-input 
            type="time" 
            name="end_time" 
            label="Jam Selesai" 
            required 
        />
        </div>

        <div class="pt-4 flex items-center space-x-2 justify-between">
            <a href="{{ route('admin.shifts.index') }}" class="btn btn-outline">← Cancel</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</x-section-content>
@endsection
