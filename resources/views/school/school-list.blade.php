@extends('layout.main')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Manage School</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage School</li>
                        </ol>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xl-12">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        {{ $message }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-success">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">School</h5>
                        <div class="card-actions float-end">
                            <div class="dropdown show">
                                <a href="{{ route('school.add') }}"
                                    class="waves-effect waves-light btn btn-sm btn-outline btn-info mb-5">Add School</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example2" class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>School</th>
                                        {{-- <th>Name</th> --}}
                                        {{-- <th>Email</th> --}}
                                        {{-- <th>Contact</th> --}}
                                        <th>Licence</th>
                                        <th>Status</th>
                                        <th>Demo</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @foreach ($school as $sdata)
                                        <tr>
                                            <td><img src="{{ url('uploads/school') }}/{{ !empty($sdata->school_logo != '') ? $sdata->school_logo : 'no_image.png' }}"
                                                    width="32" height="32" class="bg-light my-n1"
                                                    alt="{{ $sdata->school_name }}">
                                            </td>
                                            <td> <a href="javascript:void(0)" data-bs-toggle="modal"
                                                    data-bs-target="#bs-school-modal" class="fw-bold preview_school_data"
                                                    data-school="{{ $sdata->id }}"
                                                    title="Preview School">{{ $sdata->school_name }}</a></td>
                                            {{-- <td>{{ $sdata->primary_person }}</td>
                                            <td>{{ $sdata->primary_email }}</td> --}}
                                            {{-- <td>{{ $sdata->primary_mobile }}</td> --}}
                                            <td><span class="badge badge-pill badge-primary">{{ $sdata->teacher->count() }}
                                                    / {{ $sdata->licence }}</span></td>
                                            <td><a href="javascript:void(0);"
                                                    class="change_status text-white badge bg-{{ $sdata->status == 1 ? 'success' : 'danger' }}"
                                                    id="status_{{ $sdata->id }}" data-id="{{ $sdata->id }}"
                                                    data-status="{{ $sdata->status }}">{{ $sdata->status == 1 ? 'Active' : 'Inactive' }}</a>
                                            </td>
                                            <td><a href="javascript:void(0);"
                                                    class="change_school_demo_status text-white badge bg-{{ $sdata->is_demo == 1 ? 'success' : 'danger' }}"
                                                    id="demo_status_{{ $sdata->id }}" data-id="{{ $sdata->id }}"
                                                    data-status="{{ $sdata->is_demo }}">{{ $sdata->is_demo == 1 ? 'Yes' : 'No' }}</a>
                                            </td>
                                            <td> {{-- <a href="#"
                                                        class="waves-effect waves-light btn btn-sm btn-outline btn-danger mb-5" title=""><i
                                                            class="fa fa-lock"></i></a> --}}
                                                <a href="{{ route('school.edit', ['school' => $sdata->id]) }}"
                                                    class="waves-effect waves-light btn btn-sm btn-outline btn-info mb-5"
                                                    title="Edit School"><i class="fa fa-pencil-square-o"></i></a>


                                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                                    data-bs-target="#bs-password-modal" title="Delete School"
                                                    class="remove_school_data waves-effect waves-light btn btn-sm btn-outline btn-danger mb-5"
                                                    data-schoolid="{{ $sdata->id }}"><i class="fa fa-trash-o"></i></a>
                                                <a href="{{ route('report.school.view', ['school' => $sdata->id]) }}"
                                                    class="waves-effect waves-light btn btn-sm btn-outline btn-warning mb-5"
                                                    title="View Analytics"><i class="fa fa-bar-chart"></i></a>

                                                <a href="{{ route('school.admin', ['school' => $sdata->id]) }}"
                                                    class="waves-effect waves-light btn btn-sm btn-outline btn-info mb-5"
                                                    title="Manage Admin"><i class="fa fa-user-o"></i> Admin</a>

                                                <a href="{{ route('teacher.list', ['school' => $sdata->id]) }}"
                                                        class="waves-effect waves-light btn btn-sm btn-outline btn-primary mb-5"
                                                        title="Manage Teacher"><i class="fa fa-user-o"></i> Teacher</a>

                                                 <a href="{{ route('student.list') }}"
                                                    class="waves-effect waves-light btn btn-sm btn-outline btn-primary mb-5"
                                                            title="Manage Student"><i class="fa fa-user-o"></i> Student</a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- /.content -->

    <!-- Info modal -->
    <div class="modal fade" id="bs-password-modal" tabindex="-1" role="dialog" aria-labelledby="modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-label-pass">
                        Verify your Account </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti-lock"></i></span>
                            <input type="text" name="userpass" class="form-control" id="userpass">
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <input type="hidden" id="remSchool" value="0" />
                        <button class="btn btn-primary" type="submit" id="remove_school_user">Submit</button>
                    </div>
                    <div class="mb-3 text-center">
                        <p class="text-center" id="error-list"></p>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Info modal -->
    <div class="modal fade" id="bs-school-modal" tabindex="-1" role="dialog" aria-labelledby="modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h4 class="modal-title" id="modal-label-school">
                        School Detail </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div id="viewSchool"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script-section')
    <script>
        $(document).on('click', '.remove_school_data', function() {
            var schoolId = $(this).attr("data-schoolid");
            $('#remSchool').val(schoolId);
            $("#error-list").html('');
        });

        $(document).on('click', '.preview_school_data', function() {
            var schoolId = $(this).attr("data-school");
            $.ajax({
                url: "{{ route('school.preview') }}",
                type: "POST",
                data: {
                    school: schoolId,
                },
                success: function(res) {
                    $("#viewSchool").html(res);
                }
            });
        });

        $(document).on('click', '#remove_school_user', function() {
            $("#error-list").html('').removeClass('text-danger text-success');
            $.ajax({
                url: "{{ route('school.remove') }}",
                type: "POST",
                data: {
                    school: $("#remSchool").val(),
                    userpass: $("#userpass").val(),
                },
                success: function(res) {
                    if (res.success === true) {
                        $("#error-list").addClass('text-success').html(res.msg);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else if (res.success === false) {
                        $("#error-list").addClass('text-danger').html(res.msg);
                    } else {
                        alert(res);
                    }
                }
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.change_status', function() {
                var id = $(this).attr('data-id');
                var status = $(this).attr('data-status');
                $.ajax({
                    url: "{{ route('school.status') }}",
                    type: "POST",
                    data: {
                        school: id,
                        status: status
                    },
                    success: function(data) {
                        var csts = (status == 1) ? 0 : 1;
                        $('#status_' + id).text(data).attr('data-status', csts);
                        if (csts == 1) {
                            $('#status_' + id).addClass('bg-success').removeClass('bg-danger');
                        } else {
                            $('#status_' + id).addClass('bg-danger').removeClass('bg-success');
                        }
                    }
                });
            });

            $(document).on('click', '.change_school_demo_status', function() {
                var id = $(this).attr('data-id');
                var status = $(this).attr('data-status');
                console.log(id);
                $.ajax({
                    url: "{{ route('school.demo.status') }}",
                    type: "POST",
                    data: {
                        school: id,
                        status: status
                    },
                    success: function(data) {
                        var csts = (status == 1) ? 0 : 1;
                        $('#demo_status_' + id).text(data).attr('data-status', csts);
                        if (csts == 1) {
                            $('#demo_status_' + id).addClass('bg-success').removeClass(
                                'bg-danger');
                        } else {
                            $('#demo_status_' + id).addClass('bg-danger').removeClass(
                                'bg-success');
                        }
                    }
                });
            });
        });
    </script>
@endsection
