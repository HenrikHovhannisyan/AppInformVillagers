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
                            <th scope="col">Account</th>
                            <th scope="col">Account approve</th>
                            <th scope="col">Statistic</th>
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
                                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm {{ $user->status ? 'btn-danger' : 'btn-success' }}">
                                            {{ $user->status ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $user->name ? $user->name : 'Name missing' }}</td>
                                <td>{{ $user->surname ? $user->surname : 'Surname missing' }}</td>
                                <td>{{ $user->email ? $user->email : 'Email missing' }}</td>
                                <td>
                                    @if($user->account)
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editAccountModal-{{ $user->account->id }}">
                                            Edit
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if($user->account)
                                        @if($user->account->admin_approval)
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.accounts.approve', $user->account->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.accounts.approve', $user->account->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="reject" value="true">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($user->statistics->count())
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editStatisticsModal-{{ $user->id }}">
                                        Edit Statistics
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            @if($user->account)
                                <!-- Modal -->
                                <div class="modal fade" id="editAccountModal-{{ $user->account->id }}" tabindex="-1"
                                     aria-labelledby="editAccountLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('account.update', $user->account->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAccountLabel">
                                                        Edit Account {{ $user->name ? "for ". $user->name : '' }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="field_size" class="form-label">Field Size</label>
                                                        <input type="number" class="form-control" name="field_size"
                                                               value="{{ $user->account->field_size }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="tree_count" class="form-label">Tree Count</label>
                                                        <input type="number" class="form-control" name="tree_count"
                                                               value="{{ $user->account->tree_count }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="olive_type" class="form-label">Olive Type</label>
                                                        <input type="text" class="form-control" name="olive_type"
                                                               value="{{ $user->account->olive_type }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="age_of_trees" class="form-label">Age of
                                                            Trees</label>
                                                        <input type="number" class="form-control" name="age_of_trees"
                                                               value="{{ $user->account->age_of_trees }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="location_of_field" class="form-label">Location of
                                                            Field</label>
                                                        <input type="text" class="form-control" name="location_of_field"
                                                               value="{{ $user->account->location_of_field }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="continuous_season_count" class="form-label">Continuous
                                                            Season Count</label>
                                                        <input type="number" class="form-control"
                                                               name="continuous_season_count"
                                                               value="{{ $user->account->continuous_season_count }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="total_harvested_olives" class="form-label">Total
                                                            Harvested Olives</label>
                                                        <input type="number" class="form-control"
                                                               name="total_harvested_olives"
                                                               value="{{ $user->account->total_harvested_olives }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="total_gained_oil" class="form-label">Total Gained
                                                            Oil</label>
                                                        <input type="number" class="form-control"
                                                               name="total_gained_oil"
                                                               value="{{ $user->account->total_gained_oil }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($user->statistics->count())
                                <div class="modal fade" id="editStatisticsModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editStatisticsLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.users.updateStatistic', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editStatisticsLabel">Edit Statistics for User {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @foreach($user->statistics as $statistic)
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                <b>
                                                                    Year: {{ $statistic->year }}
                                                                </b>
                                                            </label>
                                                            <input type="hidden" name="statistics[{{ $loop->index }}][year]" value="{{ $statistic->year }}">
                                                            <div class="mb-3">
                                                                <label for="olive_amount_{{ $loop->index }}" class="form-label">Olive Amount</label>
                                                                <input type="number" name="statistics[{{ $loop->index }}][olive_amount]" id="olive_amount_{{ $loop->index }}" class="form-control" value="{{ $statistic->olive_amount }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="oil_amount_{{ $loop->index }}" class="form-label">Oil Amount</label>
                                                                <input type="number" name="statistics[{{ $loop->index }}][oil_amount]" id="oil_amount_{{ $loop->index }}" class="form-control" value="{{ $statistic->oil_amount }}">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
