@extends('layouts.admin')
@section('content')
<div class="container-fluid">
      
   <h1>Update User</h1>
   <form action="{{ route('users.update', $user) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label>Password (isi jika ingin mengganti)</label>
        <input type="password" name="password" class="form-control">
        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection