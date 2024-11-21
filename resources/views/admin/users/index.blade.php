@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Is verified</th>
                            <th scope="col">Status</th>
                            <th scope="col">Name</th>
                            <th scope="col">Surname</th>
                            <th scope="col">Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr class="{{ $user->status ? 'table-success' : 'table-danger' }}">
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->phone ? $user->phone : 'Phone number missing' }}</td>
                                <td>
                                    {{ $user->is_verified ? 'Yes' : 'No' }}
                                </td>
                                <td class="d-flex align-items-center justify-content-between">
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->status ? 'btn-danger' : 'btn-success' }}">
                                            {{ $user->status ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $user->name ? $user->name : 'Name missing' }}</td>
                                <td>{{ $user->surname ? $user->surname : 'Surname missing' }}</td>
                                <td>{{ $user->email ? $user->email : 'Email missing' }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
