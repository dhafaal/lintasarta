@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<x-section-content title="Create User" subtitle="Insert to create users account">
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        <div class="flex items-center space-x-4">
            <x-input 
                name="name" 
                label="Name" 
                placeholder="Insert user name" 
                required 
            />

            <x-input 
                type="email" 
                name="email" 
                label="Email" 
                placeholder="user@email.com" 
                required 
            />
        </div>

        <x-input 
            type="password" 
            name="password" 
            label="Password" 
            placeholder="Insert password" 
            required 
        />

        <x-select 
            name="role" 
            label="Role" 
            :options="['admin' => 'Admin', 'operator' => 'Operator', 'user' => 'User']" 
            required 
        />

        <div class="pt-4 flex justify-between">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</x-section-content>
@endsection
