<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\{User, School, LogsModel,Program};
// use App\Models\{User, School, CitiesModel, StateModel, LogsModel, Program, Package};
use DataTables;
use Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authuser(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $username = $request->email;
        $password = $request->password;

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            Auth::attempt(['email' => $username, 'password' => $password]);
        } else {
            Auth::attempt(['username' => $username, 'password' => $password]);
        }

        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->usertype, ['teacher', 'admin'])) {
                $school_data = School::where(['id' => $user->school_id])->first();
                if ($school_data->status == 0) {
                    Auth::logout();
                    return back()->withErrors(['error' => 'Your school account is not yet active, plz contact on support@valuezschool.com']);
                }
                session(['usertype' => $user->usertype]);
                LogsModel::create(['userid' => $user->id, 'action' => 'login', 'logs_info' => json_encode(['info' => 'User Login', 'usertype' => $user->usertype])]);
            }

            if ($user->usertype == 'superadmin' || $user->usertype == 'contentadmin') {
                session(['usertype' => $user->usertype]);
                return redirect()->intended(route('admin-dashboard'))->withSuccess('Signed in');
            } else if ($user->usertype == 'teacher' && $user->status == 1) {
                return redirect()->intended(route('teacher.class.list'))->withSuccess('Signed in');
            } else if ($user->usertype == 'admin') {
                return redirect()->intended(route('school.teacher.list'))->withSuccess('Signed in');
            } else {
                Auth::logout();
                return back()->withErrors(['error' => 'Your account is not yet active.']);
            }
        }

        #return redirect("login")->withSuccess('Login details are not valid');
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function userlist(Request $request)
    {
        $schoolid = $request->input('school');
        $userlist = DB::table('users')->where(['school_id' => $schoolid, 'usertype' => 'teacher', 'is_deleted' => 0])->orderBy('id')->get();
        return view('users.teacher', compact('userlist', 'schoolid'));
    }

    public function addUser(Request $request)
    {
        $schoolid = $request->input('school');
        return view('users.teacher-add', compact('schoolid'));
    }

    public function updateUser(Request $request)
    {
        $userId = $request->input('userid');
        $user = Auth::user();
        $where_cond = ['usertype' => 'teacher', 'id' => $userId];
        if (session()->get('usertype') == 'admin') {
            $where_cond['school_id'] = $user->school_id;
        }
        $user = DB::table('users')->where($where_cond)->first();
        return view('users.teacher-edit', compact('user'));
    }

    public function updateAdminUser(Request $request)
    {
        $userId = $request->userid;
        $user = Auth::user();
        $where_cond = ['usertype' => 'admin', 'id' => $userId];
        if (session()->get('usertype') == 'admin') {
            $where_cond['school_id'] = $user->school_id;
        }
        $user = DB::table('users')->where($where_cond)->first();
        return view('users.schooladmin.admin-edit', compact('user'));
    }

    public function createuser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            // 'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);
        $redirect = (session()->get('usertype') == 'admin') ? route('school.teacher.list') : route('teacher.list', ['school' => $data['school']]);
        if ($check == "error") {
            return redirect($redirect)->with('error', 'Maximum licences limit reached.');
        } else {
            return redirect($redirect)->withSuccess('User added successfully!');
        }
    }

    public function create(array $data)
    {
        $check_school_user = School::with(['teacher' => function ($query) {
            $query->where('usertype', '=', 'teacher')->where(['is_deleted' => 0]);
        }])->where(['is_deleted' => 0, 'id' => $data['school']])->orderBy('id')->first();

        $total_teacher = $check_school_user->teacher->count();

        if ($check_school_user->licence > $total_teacher) {
            $passWord = isset($data['password']) ? $data['password'] : Str::random(10);
            $user_email = strtolower($data['email']);
            $username = explode("@", $user_email);
            $userId = trim($username[0]) . date('Yims');
            $add_user = [
                'name' => $data['name'],
                'email' => $data['email'],
                'school_id' => $data['school'],
                'usertype' => 'teacher',
                'status' => 1,
                'username' => $userId,
                'view_pass' => $passWord,
                'password' => Hash::make($passWord)
            ];
            #print_r($add_user); die;
            #$this->UserAccountMail(['username' => $data['email'], 'userid' => $userId, 'pass' => $passWord, 'school_name' => $check_school_user->school_name]);
            return User::create($add_user);
        } else {
            return "error";
        }
    }

    public function edituser(Request $request)
    {
        $data = $request->all();
        $school = $data['school'];
        $pagetype = !empty($data['pagetype']) ? $data['pagetype'] : '';
        $updateuser = ['name' => $data['name'], 'email' => $data['email']];
        $validate = ['name' => 'required', 'email' => 'required|email|unique:users,email,' . $data['id']];
        if (!empty($data['password'])) {
            $validate['password'] = ['required', Password::min(6)];
            $updateuser['password'] = Hash::make($data['password']);
            $updateuser['view_pass'] = $data['password'];
        }
        $request->validate($validate);
        User::where('id', $data['id'])->update($updateuser);
        if (!empty($data['password'])) {
            $details = [
                'view' => 'emails.reset_password',
                'subject' => $data['name'] . ' Your Account Password Reset by admin - Valuez',
                'title' => $data['name'],
                'email' => $data['email'],
                'pass' => $data['password']
            ];
            Mail::to($data['email'])->send(new \App\Mail\TestMail($details));
        }
        $redirect = (session()->get('usertype') == 'admin') ? route('school.teacher.list') : route('teacher.list', ['school' => $school]);

        $redirect_url = ($pagetype == 'schooladmin') ? route('school.admin') : $redirect;
        return redirect($redirect_url)->with('success', 'User Updated successfully');
    }

    public function resetPassword(Request $request)
    {
        $passWord = $this->getToken();
        $resetPass = User::where('id', $request->userid)->update(['view_pass' => $passWord, 'password' => Hash::make($passWord)]);
        if ($resetPass) {
            $user_email = User::where('id', $request->userid)->first();
            $details = [
                'view' => 'emails.reset_password',
                'subject' => 'User Account Password Reset - Valuez',
                'title' => $user_email->name,
                'email' => $user_email->email,
                'pass' => $passWord
            ];
            Mail::to($user_email->email)->send(new \App\Mail\TestMail($details));
        }
    }

    public function destroy(Request $request)
    {
        $userId = $request->input('userid');
        $userPass = $request->input('userpass');
        if (Auth::check()) {
            $user = Auth::user();
            if (Hash::check($userPass, $user->password)) {
                DB::table('users')->where('id', $userId)->update(['is_deleted' => 1]);
                return response()->json(['success' => true, 'msg' => 'User deleted successfully!']);
            } else {
                return response()->json(['success' => false, 'msg' => 'Entered Password Incorrect.']);
            }
        } else {
            return response()->json(['success' => false, 'msg' => 'Somenthing Went Wrong!']);
        }
    }

    public function AdminDash()
    {
        if (Auth::check()) {
            $school = $teacher = $program = $lessonplan = 0;

            $school = DB::table('school')->where('status', 1)->get()->count();
            $teacher = DB::table('users')->where('usertype', 'teacher')->get()->count();
            $course = DB::table('master_course')->where('status', 1)->get()->count();
            $program = DB::table('master_class')->where('status', 1)->get()->count();
            $lessonplan = DB::table('lesson_plan')->where('status', 1)->get()->count();

            return view('dashboard-admin', compact('school', 'teacher', 'program', 'lessonplan', 'course'));
        } else {
            return redirect("login")->withSuccess('You are not allowed to access');
        }
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $schoolid = $user->school_id;
            if (session()->get('usertype') == 'admin') {
                $school = School::with(['teacher' => function ($query) {
                    $query->where('usertype', '=', 'teacher');
                }])->where('id', $schoolid)->orderBy('id')->first();

                $package_start = new \DateTime(date("Y-m-d h:i:s"));
                $package_end = new \DateTime($school->package_end);
                $interval = $package_start->diff($package_end);
                $time_left = $interval->format('%a');

                return view('dashboard', compact('school', 'time_left'));
            } else {
                return view('dashboard-teacher');
            }
        } else {
            return redirect("login")->withSuccess('You are not allowed to access');
        }
    }

    public function teacherList()
    {
        $user = Auth::user();
        $schoolid = $user->school_id;
        $userlist = DB::table('users')->where(['school_id' => $user->school_id, 'usertype' => 'teacher', 'is_deleted' => 0])->orderBy('id')->get();
        return view('users.teacher', compact('userlist', 'schoolid'));
    }

    public function studentuserlist(Request $request)
    {
        $schoolid = $request->input('school');
        $userlist = DB::table('users')->where(['school_id' => $schoolid, 'usertype' => 'student', 'is_deleted' => 0])->orderBy('id')->get();
        return view('users.student', compact('userlist', 'schoolid'));
    }

    public function addUserStudent(Request $request)
    {
        $grades = Program::where("status", 1)->get(["class_name", "id"]);
        $schoolid = $request->input('school');
        return view('users.student-add', compact('schoolid','grades'));
    }


    public function createuserstudent(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'grade_id' => 'required',
            'phone' => 'required',
        ]);

        $data = $request->all();
        $check = $this->createstudent($data);
        $redirect = (session()->get('usertype') == 'admin') ? route('school.student.list') : route('student.list', ['school' => $data['school']]);
        if ($check == "error") {
            return redirect($redirect)->with('error', 'Maximum licences limit reached.');
        } else {
            return redirect($redirect)->withSuccess('User added successfully!');
        }
        
    }
    public function createstudent(array $data)
    {
        $check_school_user = School::with(['student' => function ($query) {
            $query->where('usertype', '=', 'student')->where(['is_deleted' => 0]);
        }])->where(['is_deleted' => 0, 'id' => $data['school']])->orderBy('id')->first();
        $total_student = $check_school_user->student->count();
    
        if ($check_school_user->licence > $total_student) {
            $passWord = isset($data['password']) ? $data['password'] : Str::random(10);
            $user_email = strtolower($data['email']);
            $username = explode("@", $user_email);
            $userId = trim($username[0]) . date('Yims');
            $add_user = [
                'name' => $data['name'],
                'email' => $data['email'],
                'grade_id' => $data['grade_id'],
                'phone' => $data['phone'],
                'school_id' => $data['school'],
                'usertype' => 'student',
                'status' => 1,
                'username' => $userId,
                'view_pass' => $passWord,
                'password' => Hash::make($passWord)
            ];
            #print_r($add_user); die;
            #$this->UserAccountMail(['username' => $data['email'], 'userid' => $userId, 'pass' => $passWord, 'school_name' => $check_school_user->school_name]);
            return User::create($add_user);
        } else {
            return "error";
        }
    }
    public function studentxls(Request $request){
        // dd($request->school);
        try {
            $excel = $request->file("xlsxFile")->path();
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel);
$sheet = $spreadsheet->getSheet(0);
$data = $sheet->toArray(true,true,true,true); 
// dd($data);
$msg="";

if(count($data)>1){ 
    $data = array_values($data);   
    $fields = array_values($data[0]);
    unset($data[0]);
    $dataSetList = array();
    foreach ($data as $key => $value) {
        $dataSet = array(
            'school_id' => $request->school,
            'usertype' => 'student',
            'status' => 1,
            'username' => array_values($value)[array_search('name', $fields)].Str::random(3),
            'view_pass' => Str::random(6),
            'password' => Hash::make(Str::random(6)), 
        );
        $dataSet[$fields[0]]=array_values($value)[0];
        $dataSet[$fields[1]]=array_values($value)[1];
        $dataSet[$fields[2]]=array_values($value)[2];
        $dataSet[$fields[3]]=array_values($value)[3];
        User::create($dataSet);
        $dataSetList[]=$dataSet;
    }
    // dd(json_encode($dataSetList));
    
    //  return $msg='Excel imported successfully';
     return redirect()->back();
     
}else{
   return $msg='Sorry! No user found in excel file';
}

        } catch (\Throwable $th) {
            return $msg='Something went wrong';
        }
    }

    public function SchoolAdmin(Request $request)
    {
        $schoolid = $request->school;
        $userlist = User::where(['school_id' => $schoolid, 'usertype' => 'admin', 'users.is_deleted' => 0])->orderBy('id')->get();
        return view('users.schooladmin.admin', compact('userlist', 'schoolid'));
    }

    public function signOut(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
            LogsModel::create(['userid' => $userId, 'action' => 'logout', 'logs_info' => json_encode(['info' => 'User logout', 'usertype' => $user->usertype])]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    // Generate token
    public function getToken($length = 8)
    {
        return Str::random($length);
    }

    public function UserAccountMail($data)
    {
        $details = [
            'view' => 'emails.account',
            'subject' => 'User Account creation Mail from Valuez',
            'title' => $data['username'],
            'userid' => $data['userid'],
            'pass' => $data['pass'],
            'school_name' => $data['school_name'],
        ];
        #Mail::to($data['username'])->send(new \App\Mail\TestMail($details));
    }
}
