@extends('layouts.app')

@section('content')
    <div class="content-wrapper">

        <!-- Page Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Edit Staff</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('app.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('app.staff.index') }}">Staff</a></li>
                            <li class="breadcrumb-item active">Edit Staff</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Update Staff</h3>
                    </div>

                    <form action="{{ route('app.staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ $staff->name }}"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Staff No</label>
                                <input type="text"
                                       name="staff_no"
                                       class="form-control"
                                       value="{{ $staff->staff_no }}"
                                       required>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </section>

    </div>
@endsection
