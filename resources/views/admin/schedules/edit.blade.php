@extends('layouts.app')

@section('title', isset($schedule) ? 'Edit Schedule' : 'Add Schedule')

@section('content')
<x-section-content
    title="{{ isset($schedule) ? 'Edit Schedule' : 'Add Schedule' }}"
    subtitle="{{ isset($schedule) ? 'Edit and update an existing schedule' : 'Create a new schedule for a user' }}">

    <form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}"
        class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @if(isset($schedule)) @method('PUT') @endif

        {{-- User --}}
        <x-select 
            name="user_id" 
            label="User" 
            :options="$users->pluck('name', 'id')" 
            :selected="isset($schedule) ? $schedule->user_id : null" 
            required 
        />

        {{-- Shift --}}
        <x-select 
            name="shift_id" 
            label="Shift" 
            :options="$shifts->pluck('name', 'id')" 
            :selected="isset($schedule) ? $schedule->shift_id : null" 
            required 
        />

        {{-- Date --}}
        <x-input 
            type="date" 
            name="schedule_date" 
            label="Date" 
            :value="isset($schedule) ? $schedule->schedule_date : null" 
            required 
            class="md:col-span-2"
        />

        {{-- Submit --}}
        <div class="md:col-span-2 flex justify-between items-center">
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline px-6">← Back</a>

            <button type="submit" class="btn btn-primary">
                {{ isset($schedule) ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</x-section-content>
@endsection
