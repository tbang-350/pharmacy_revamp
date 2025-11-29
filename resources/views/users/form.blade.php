@extends('layouts.app')

@section('title', $user ? 'Edit User' : 'Add User')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $user ? 'Edit' : 'Add' }} User</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $user ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if($user)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" 
                           value="{{ old('name', $user->name ?? '') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="{{ old('email', $user->email ?? '') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" 
                           value="{{ old('phone', $user->phone ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" required>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" 
                                {{ old('role', $user?->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Password {{ $user ? '(leave blank to keep current)' : '' }}</label>
                    <input type="password" class="form-control" name="password" {{ !$user ? 'required' : '' }}>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $user ? 'Update' : 'Create' }} User
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
