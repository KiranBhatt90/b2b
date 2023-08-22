@extends('layout.main')
@section('content')
{{-- <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head> --}}
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Students</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Manage Students</li>
                            <li class="breadcrumb-item active" aria-current="page">Update Student</li>
                        </ol>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-8 col-12">
                @if ($getStudent)
                    <!-- Basic Forms -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Update Student</h4>
                        </div>
                        <!-- /.box-header -->
                        <form action="{{ route('student.update' , ['userid' => $getStudent->id] )}}" method="POST" enctype="multipart/form-data">

                            @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ $getStudent->name}}" class="form-control"
                                        placeholder="Enter Name" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ $getStudent->email}}" class="form-control"
                                        placeholder="Enter Email" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                    <div class="form-group">
                                    <label for="grade" class="form-label">Grade:<span class="text-danger">*</span></label>
                                    <select name="grade" class="form-control" required>
                                        <option value="" disabled>Select Grade</option>
                                        @foreach ($programs as $id => $program)
                                            <option value="{{ $id }}" {{ $getStudent->grade == $id ? 'selected' : '' }}>
                                                {{ $program }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('grade')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="{{ $getStudent->phone}}" class="form-control"
                                        placeholder="Enter Phone">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <input type="hidden" name="id" value="{{ $getStudent->id }}">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box -->
                @else
                    <h1>Something went wrong.</h1>
                @endif
            </div>

        </div>
    </section>
    <!-- /.content -->
@endsection
