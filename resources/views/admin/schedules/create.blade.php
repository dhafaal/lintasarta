@extends('layouts.app')

@section('title', 'Create Schedule')

@section('content')
<x-section-content title="Create Schedule" subtitle="Create new schedule here">
    <form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}"class="space-y-6">
        @csrf
        @if(isset($schedule)) @method('PUT') @endif

        <x-select 
            name="user_id"
            label="User"
            :options="$users->pluck('name', 'id')"
            :selected="$schedule->user_id ?? null"
            required
        />

        <x-select 
            name="shift_id"
            label="Shift"
            :options="$shifts->pluck('name', 'id')"
            :selected="$schedule->shift_id ?? null"
            required
        />

        <x-input 
            type="date"
            name="schedule_date"
            label="Date"
            :value="$schedule->schedule_date ?? old('schedule_date')"
            required
        />

        <div class="pt-4 flex justify-between">
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline">← Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</x-content-section>
@endsection
