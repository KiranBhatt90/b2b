<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Mail;


class StudentController extends Controller
{
    public function index()
    {

        $student = Student::with([
            'student' => function ($query) {
                $query->where('usertype', '=', 'student')->where(['is_deleted' => 0]);
            }
        ])->where(['is_deleted' => 0])->orderBy('id')->get();

        return view('students.student', compact('student'));
    }

    public function editStudent(Request $request)
    {
        $userid = $request->input('userid');
        $getStudent = DB::table('students')->where('id', $userid)->first();
        $programs = Program::pluck('class_name', 'id');

        return view('students.student-edit', compact('getStudent', 'programs'));
    }

    public function updateStudent(Request $request)
    {
        $student = Student::where(['id' => $request->userid])->first();

        $request->validate([
            'name' => 'required',
            'grade' => 'required',
            'phone' => 'required',
        ]);


        $studentData = [
            'name' => $request->name,
            'grade' => $request->grade,
            'phone' => $request->phone,
        ];

        if ($request->input('email') != $student->email) {
            //dd('hi');
            $request->validate([
                'email' => 'required|email|unique:students',
            ]);

            $studentData['email'] = $request->email;
        }
        $student->update($studentData);
        return redirect()->route('student.list')->with('success', 'Student has been Updated');

    }

    public function createStudent()
    {
        $programs = Program::pluck('class_name', 'id');
        return view('students.student-add', compact('programs'));

    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'grade' => 'required',
            'phone' => 'required',

        ]);

        $stuData = new Student;
        $stuData->name = $request->name;
        $stuData->email = $request->email;
        $stuData->grade = $request->grade;
        $stuData->phone = $request->phone;

        $randomPassword = Str::random(12);

        $encryptedPassword = Hash::make($randomPassword);

        // Store encrypted password in 'password'
        $stuData->password = $encryptedPassword;

        // Store plaintext password in 'view_pass'
        $stuData->view_pass = $randomPassword;
        $stuData->save();

        //dd('data saved');
        return redirect()->route('student.list')
            ->with('success', 'Student has been Created');
    }

    public function destroy(Request $request)
    {
        $userId = $request->input('userid');
        $userPass = $request->input('userpass');
        if (Auth::check()) {
            $user = Auth::user();
            if (Hash::check($userPass, $user->password)) {
                DB::table('students')->where('id', $userId)->update(['is_deleted' => 1]);
                return response()->json(['success' => true, 'msg' => 'Student deleted successfully!']);
            } else {
                return response()->json(['success' => false, 'msg' => 'Entered Password Incorrect.']);
            }
        } else {
            return response()->json(['success' => false, 'msg' => 'Somenthing Went Wrong!']);
        }
    }

    public function resetPassword(Request $request)
    {
        dd($request->all());
        $passWord = $this->getToken();
        $resetPass = Student::where('id', $request->userid)->update(['view_pass' => $passWord, 'password' => Hash::make($passWord)]);

        if ($resetPass) {
            $user_email = Student::where('id', $request->userid)->first();
            $details = [
                'view' => 'emails.reset_password',
                'subject' => 'User Account Password Reset - Valuez',
                'title' => $user_email->name,
                'email' => $user_email->email,
                'pass' => $passWord
            ];

           // Mail::to($user_email->email)->send(new \App\Mail\TestMail($details));
    }
}


    public function change_user_status(Request $request)
    {
        $userId = $request->userid;
        $status = ($request->status == 1) ? 0 : 1;
        DB::table('students')->where('id', $userId)->update(['status' => $status]);
        echo ($status == 1) ? 'Active' : 'Inactive';
    }

    public function uploadStudent()
    {
        return view('students.bulkUploadForm');
    }


    public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|mimes:csv',
    ]);

    try {
        $filePath = $request->file('csv_file')->store('csv_files');
        Excel::import(new UsersImport, storage_path('app/' . $filePath));

        session()->flash('success', 'Bulk upload completed.');
    } catch (\Exception $e) {
        session()->flash('error', 'Error during bulk upload: ' . $e->getMessage());
    }

    return redirect()->route('student.list');
}





}
