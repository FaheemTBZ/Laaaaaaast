@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <strong>Users List to Approve</strong>
                </div>

                <div class="card-body">

                    @if (session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                    @endif

                    <table class="table">
                        <tr>
                            <th>User name</th>
                            <th>Email</th>
                            <th>Registered at</th>
                            <th>Allow Permissions</th>
                            <th></th>
                        </tr>
                        @forelse ($users as $user)
                        <tr>
                            <form action="{{ route('admin.users.approve') }}" method="POST">
                                @csrf
                                <input type="hidden" name="userId" value="{{ $user->id }}" />
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <label for="addRight">Add Item</label>
                                    <input type="checkbox" id="addRight" name="addRight" />
                                    <label for="searchRight" id="searchRight">Search Items</label>
                                    <input type="checkbox" name="searchRight" id="searchRight" />
                                </td>
                                <td><button type="submit" class="btn btn-primary btn-sm">Approve</button></td>
                            </form>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection