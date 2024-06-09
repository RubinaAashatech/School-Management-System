<?php

namespace App\Http\Controllers\Shared;



use App\Models\Staff;
use App\Models\Classg;
use App\Models\School;

use App\Models\Student;
use App\Models\User;
use App\Models\District;
use App\Models\Department;
use App\Models\SchoolUser;

use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\AcademicSession;

use App\imports\CombinedImport;
use Yajra\Datatables\Datatables;
use App\Http\Services\FormService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use App\Http\Services\StaffUserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    protected $formService;
    protected $staffUserService;
    protected $imageSavePath = '/uploads/staffs/';
    public function __construct(FormService $formService, StaffUserService $staffUserService)
    {
        $this->formService = $formService;
        $this->staffUserService = $staffUserService;
    }

    public function getDistrict($province_id)
    {

        $districts = $this->formService->getDistricts($province_id);
        return response()->json($districts);
    }
    public function index()
    {

        $staffs = Staff::latest()->get();
        $page_title = 'Staff List';
        return view('backend.shared.staffs.index', compact('staffs', 'page_title'));
    }


    public function create()
    {
        $page_title = 'Staff Create Form';
        $departments = Department::all();
        // $roles = Role::all();
        $roles = Role::whereIn('name', ['Teacher', 'Accountant', 'Librarian', 'Principal', 'Receptionist'])->get();
        $states = $this->formService->getProvinces();
        $adminStateId = Auth::user()->state_id;
        $adminDistrictId = Auth::user()->district_id;
        $adminMunicipalityId = Auth::user()->municipality_id;


        return view('backend.shared.staffs.create', compact('page_title', 'states', 'roles', 'departments', 'adminStateId', 'adminMunicipalityId', 'adminDistrictId'));
    }

    // CREATE STAFF
    protected function saveStaff(array $staffInput)
    {
        try {
            $newStaff = Staff::create($staffInput);
        } catch (\Exception $e) {
            // Handle any specific exception related to staff creation
            throw $e;
        }
    }

    // VALIDATION FOR USERS and STAFFS
    protected function validateUserData(Request $request, $isStaff = false)
    {
        $rules = [

            'state_id' => 'required',
            'district_id' => 'required',
            'municipality_id' => 'required',
            'ward_id' => 'required',
            'local_address' => 'required',
            'permanent_address' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required',
            'religion' => 'nullable',
            'mobile_number' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'blood_group' => 'nullable',
            // 'image' => 'required',
            // 'father_name' => 'required',
            // 'father_phone' => 'required',
            // 'father_occupation' => 'required',
            // 'mother_name' => 'required',
            // 'mother_phone' => 'required',
            // 'mother_occupation' => 'required',
            'emergency_contact_person' => 'nullable',
            'emergency_contact_phone' => 'nullable',
            'username' => 'nullable',
            'employee_id' => 'nullable',
            // 'password' => 'required',
            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'linkedin' => 'nullable',
            'bank_name' => 'nullable',
            'bank_account_no' => 'nullable',
            'bank_branch' => 'nullable',
            'note' => 'nullable',
            'role' => 'nullable',
            // 'marital_status' => 'required',
            'is_active' => 'boolean',

        ];

        // Add conditional validation for Staff
        if ($isStaff) {
            $rules += [
                // 'user_id' => 'required|exists:users,id',
                // 'school_id' => 'required|exists:schools,id',
                // 'employee_id' => 'required|string|unique:staffs,employee_id',
                // 'department_id' => 'required|exists:departments,id',
                // 'qualification' => 'required|string',
                // 'work_experience' => 'nullable|string',
                'marital_status' => 'nullable',

                // 'date_of_joining' => 'required|date',
                // 'date_of_leaving' => 'required|date',
                // 'payscale' => 'required|string',
                // 'basic_salary' => 'required|string',
                // 'contract_type' => 'required|string',
                // 'shift' => 'required|string',
                // 'location' => 'required|string',
                // 'resume' => 'nullable|string',
                // 'joining_letter' => 'nullable|string',
                // 'resignation_letter' => 'nullable|string',
                // 'medical_leave' => 'nullable|string',
                // 'casual_leave' => 'nullable|string',
                // 'maternity_leave' => 'nullable|string',
                // 'other_document' => 'nullable|string',

            ];

        // If employee_id is not provided, generate it

        if (!$request->filled('employee_id')) {
            $request->merge(['employee_id'  => $this->generateEmployeeId()]);
        }
    }

    return $request->validate($rules);
  }

        // Function to generate a random employee ID

        private function generateEmployeeId($length = 3)
        {
   
            $chars = '0123456789';
            $employeeId = '';
            for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($chars) - 1);
            $employeeId .= $chars[$randomIndex];
    }
       return $employeeId;
    }

    protected function saveUserImage($croppedImage)
    {
        // if (!File::exists($this->imageSavePath)) {
        //     File::makeDirectory($this->imageSavePath, 0775, true, true);
        // }

        // $destinationPath = $this->imageSavePath . $this->getDateFormatFileName('jpg');
        // Image::make($croppedImage)
        //     ->encode('jpg')
        //     ->save(public_path($destinationPath));

        // return $destinationPath;
        try {
            $savePath = public_path($this->imageSavePath);

            if (!File::exists($savePath)) {
                // Create the directory if it doesn't exist
                File::makeDirectory($savePath, 0775, true, true);
            }

            // Generate a unique filename
            $filename = time() . '.' . 'jpg';

            // Save the image with a unique filename
            $image = Image::make($croppedImage);
            $image->encode('jpg')->save($savePath . $filename);

            return $this->imageSavePath . $filename;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    protected function createUser(array $userData)
    {
        $userData['user_type_id'] = 6;
        $userData['school_id'] = 1;

        $result = User::create($userData);
        $result->assignRole((int) $userData['role_id']);
        return $result;
    }

    protected function saveResume(Request $request)
    {
        if ($request->hasFile('resume')) {
            $postPath = time() . '.' . $request->file('resume')->getClientOriginalExtension();
            $destinationPath = public_path('uploads/staffs/resume');
            $request->file('resume')->move($destinationPath, $postPath);
            ;

            return $destinationPath . '/' . $postPath;
        }

        return null;
    }

    // SAVING ENTRY FOR SCHOOL_USER PIVOT TABLE
    protected function createSchoolUserEntry($schoolId, $userId)
    {
        try {
            SchoolUser::create([
                'school_id' => $schoolId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            // Handle any specific exception related to SchoolUser creation
            throw $e;
        }
    }
    public function store(Request $request)
    {
        try {
            // Debugging to check the role value
            // dd($request->input('role'));

            // CREATING USER WITH ITS RESPECTIVE VALIDATION FUNCTION
            $userInput = $this->validateUserData($request);

            if ($request->has('inputCroppedPic') && !is_null($request->inputCroppedPic)) {
                $userInput['image'] = $this->saveUserImage($request->input('inputCroppedPic'));
            }
            if ($request->has('inputCroppedPic') && !is_null($request->inputCroppedPic)) {
                if (!File::exists($this->imageSavePath)) {
                    File::makeDirectory($this->imageSavePath, 0775, true, true);
                }
                $destinationPath = $this->imageSavePath . $this->getDateFormatFileName('jpg');
                Image::make($request->input('inputCroppedPic'))
                    ->encode('jpg')
                    ->save(public_path($destinationPath));
                $input['student_photo'] = $destinationPath;
            }

            // Include 'role' in user data
            $userInput['role_id'] = $request->input('role');
            // dd($userInput);

            $staffNumber = $request->input('employee_id') ?? '';
            // Generate username based on first letter of f_name, first letter of l_name, and employee number
            $username = strtolower(substr($userInput['f_name'], 0, 1) . substr($userInput['l_name'], 0, 1) . '-' . $staffNumber);
            // Assign the generated username to the user input
            $userInput['username'] = $username;

            $userInput['password'] = Hash::make('password');

            // STAFF USER CREATED AND ASSIGNED
            $staffUser = $this->createUser($userInput);
            // dd($staffUser);
            // $staffUser->assignRole($userInput['role_id']);

            // CREATING STAFF WITH ITS RESPECTIVE VALIDATION
            $staffInput = $this->validateUserData($request, true);
            $staffInput['user_id'] = $staffUser->id;
            $staffInput['school_id'] = session('school_id');


            // $staffInput['employee_id'] = 1;
            $staffInput['department_id'] = $request->input('department_id');
            $staffInput['class_id'] = $request->input('class_id');
            $staffInput['qualification'] = $request->input('qualification');
            $staffInput['work_experience'] = $request->input('work_experience');
            $staffInput['date_of_joining'] = $request->input('date_of_joining');
            $staffInput['date_of_leaving'] = $request->input('date_of_leaving');
            $staffInput['payscale'] = $request->input('payscale');
            $staffInput['basic_salary'] = $request->input('basic_salary');
            $staffInput['contract_type'] = $request->input('contract_type');
            $staffInput['shift'] = $request->input('shift');
            $staffInput['resignation_letter'] = $request->input('resignation_letter');
            $staffInput['joining_letter'] = $request->input('joining_letter');
            $staffInput['medical_leave'] = $request->input('medical_leave');
            $staffInput['casual_leave'] = $request->input('casual_leave');
            $staffInput['maternity_leave'] = $request->input('maternity_leave');
            $staffInput['role'] = $request->input('role');
            // $staffInput['location'] = $request->input('location');
            $staffInput['other_document'] = $request->input('other_document');

            $resumePath = $this->saveResume($request);

            if (!is_null($resumePath)) {
                $staffInput['resume'] = $resumePath;
            }

            $staffInput['staff_photo'] = $staffUser->image;

            // Include 'role' in staff data
            $staffInput['role'] = $request->input('role');

            // Ensure 'role' is set to 'role_id'
            $staffInput['role_id'] = $request->input('role');
            $this->saveStaff($staffInput);

            // Create entry in the SchoolUser pivot table
            $this->createSchoolUserEntry($staffInput['school_id'], $staffUser->id);

            return redirect()->route('admin.staffs.index')->withToastSuccess('Staff successfully created');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage())->withInput();
        }
    }

    public function edit(string $id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $states = $this->formService->getProvinces();
            $roles = Role::whereIn('name', ['Teacher', 'Accountant', 'Librarian', 'Principal', 'Receptionist'])->get();
            $selectedRole = $staff->role_id;
            $departments = Department::all();
            $page_title = 'Staff Edit Form';
            // FETCHING DISTRICT FOR SELECTED STATE
            $districts = $staff->user->district_bystate($staff->user->state_id);
            // FETCHING MUNICIPALITY FOR SELECTED DISTRICT
            $municipalities = $staff->user->municipalities_bydistrict($staff->user->district_id);

            // FETCHING WARDS BY MUNICIAITY
            $wards = User::getWards($staff->user->municipality_id);


            return view('backend.shared.staffs.update', compact('staff', 'page_title', 'states', 'roles', 'selectedRole', 'districts', 'municipalities', 'wards', 'departments'));
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         // CREATING USER WITH ITS RESPECTIVE VALIDATION FUNCTION
    //         $userInput = $this->validateUserData($request);

    //         // Handling Image Update
    //         if ($request->has('inputCroppedPic') && !is_null($request->inputCroppedPic)) {
    //             $userInput['image'] = $this->saveUserImage($request->input('inputCroppedPic'));
    //         }

    //         // Fetching the existing staff user
    //         $staff = Staff::findOrFail($id);
    //         $staffUser = User::findOrFail($staff->user_id);

    //         // Updating User
    //         $staffUser->update($userInput);

    //         // Check if a new file is provided
    //         // if ($request->hasFile('resume')) {
    //         //     $postPath = time() . '.' . $request->file('resume')->getClientOriginalExtension();
    //         //     $request->file('resume')->move(public_path('uploads/staffs/resume'), $postPath);
    //         //     $input['resume'] = $postPath;
    //         // } else {
    //         //     // If no new file provided, retain the existing value
    //         //     $input['resume'] = $staff->resume;
    //         // }

    //         // // Handling Resume Update
    //         // $this->saveResume($request, $staffInput);

    //         // Updating Staff
    //         $staff->update($staffInput);

    //         return redirect()->route('admin.staffs.index')->withToastSuccess('Staff successfully updated');
    //     } catch (\Exception $e) {
    //         return back()->withToastError($e->getMessage())->withInput();
    //     }
    // }

    public function update(Request $request, $id)
    {
        try {
            // Validate user and staff data
            $validatedUserData = $this->validateUserData($request);
            $validatedStaffData = $this->validateUserData($request, true);

            // Retrieve the student by ID
            $staff = Staff::findOrFail($id);

            // Check if the user already exists for the student
            if ($staff->user) {
                // Update existing user data
                $userInput = $validatedUserData;

                // Check if a new photo is selected
                if ($request->has('inputCroppedPic') && !is_null($request->inputCroppedPic)) {
                    $userInput['image'] = $this->saveUserImage($request->input('inputCroppedPic'));
                }

                // Update the existing user
                $staff->user->update($userInput);

                // Assign role if role_id is provided in the request
                if ($request->has('role_id')) {
                    $staff->user->assignRole((int) $request->role_id);
                }
            }

            // Update existing student data
            $staff->update($validatedStaffData);

            return redirect()->route('admin.staffs.index')->withToastSuccess('Student successfully Updated');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {

        // $staff = Staff::find($id);
        $staff = Staff::with('user')->findOrFail($id);

        if ($staff->delete()) {
            return redirect()->back()->withToastSuccess('Staff Successfully Deleted!');
        } else {
            return back()->withToastError('An error occurred while performing the operation.');
        }
    }


    public function getAllStaff(Request $request)
    {
        $staff = $this->staffUserService->getStaffsForDataTable($request->all());
        // dd($staff);
        return Datatables::of($staff)
            ->editColumn('f_name', function ($row) {
                return $row->f_name;
            })
            ->editColumn('l_name', function ($row) {
                return $row->l_name;
            })
            ->editColumn('marital_status', function ($row) {
                return $row->marital_status == 1 ? '<span class="">Married</span>' : '<span class="">Unmarried</span>';
            })
            ->editColumn('date_of_joining', function ($row) {
                return $row->date_of_joining;
            })
            ->editColumn('date_of_leaving', function ($row) {
                return $row->date_of_leaving;
            })
            // ->editColumn('payscale', function ($row) {
            //     return $row->payscale;
            // })
            // ->editColumn('basic_salary', function ($row) {
            //     return $row->basic_salary;
            // })
            ->editColumn('contract_type', function ($row) {
                return $row->contract_type;
            })
            ->editColumn('shift', function ($row) {
                return $row->shift;
            })
            // ->editColumn('location', function ($row) {
            //     return $row->location;
            // })
            // ->editColumn('resume', function ($row) {
            //     return $row->resume;
            // })
            // ->editColumn('joining_letter', function ($row) {
            //     return $row->joining_letter;
            // })
            // ->editColumn('resignation_letter', function ($row) {
            //     return $row->resignation_letter;
            // })
            // ->editColumn('medical_leave', function ($row) {
            //     return $row->medical_leave;
            // })
            // ->editColumn('casual_leave', function ($row) {
            //     return $row->casual_leave;
            // })
            ->editColumn('maternity_leave', function ($row) {
                return $row->maternity_leave;
            })
            ->editColumn('other_document', function ($row) {
                return $row->other_document;
            })

            ->escapeColumns([])
            ->addColumn('created_at', function ($user) {
                return $user->created_at->diffForHumans();
            })

            ->addColumn('status', function ($staff) {
                return $staff->is_active == 1 ? '<span class="btn-sm btn-success">Active</span>' : '<span class="btn-sm btn-danger">Inactive</span>';
            })

            ->addColumn('actions', function ($staff) {
                return view('backend.shared.staffs.partials.controller_action', [
                    'staff' => $staff
                ])->render();
            })

            ->make(true);
    }

    public function importStaffs()
    {
        $page_title = "Import Staff";
        $schoolId = session('school_id');
        return view('backend.shared.staffs.importindex', compact('page_title'));
    }

    public function addLeaveDetails()
    {
    $page_title = 'Add Leave Details';
    $schoolId = session('school_id');
    return view('backend.shared.staffs.leavedetails', compact('page_title'));
    }

    public function addResignationDetails()
    {
        $page_title = "Resignation Details";
        $schoolId = session('school_id');
        return view('backend.shared.staffs.resignationdetailsindex', compact('page_title'));
    }

    public function import(Request $request)
    {
        // $roles = Department::pluck('name');
        // dd($roles);
        try {
            // Begin a database transaction
            DB::beginTransaction();
            $array1 = Excel::toCollection(new CombinedImport, $request->file('file'));
            // Access the outer collection
            foreach ($array1 as $outerCollection) {
                // Iterate through the inner collections
                // dd($outerCollection);
                foreach ($outerCollection as $row) {

                    $validator = Validator::make($row->toArray(), [

                        'state_id' => 'required',
                        'district_id' => 'required',
                        'municipality_id' => 'required',
                        'ward_id' => 'required',
                        'f_name' => 'required',
                        'l_name' => 'required',
                        'mobile_number' => 'required',
                        'email' => 'required|unique:users,email',
                        'employee_id' => 'required|unique:staffs,employee_id',
                        'gender' => 'required',
                        'role' => 'required',
                        'dob' => 'required',
                        'department' => 'required',
                        'contract_type' => 'required',
                        'emergency_contact_person' => 'required',
                        'emergency_contact_phone' => 'required',

                    ]);

                    if ($validator->fails()) {
                        // Redirect back with validation errors
                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    //extracting role id from name
                    $role_id = $this->roleIdentification($row['role']);
                    if ($role_id == null) {
                        return redirect()->back()->withErrors('Invalid Role name of : ' . $row['f_name'])->withInput();
                    }
                    //extracting department id from name
                    $department_id = $this->departmentIdentification($row['department']);
                    if ($department_id == null) {
                        return redirect()->back()->withErrors('Invalid Department name of : ' . $row['f_name'])->withInput();
                    }
                    $marriedId = $this->maritialStatus($row['marital_status']);
                    if ($marriedId == null && $marriedId != 0) {
                        return redirect()->back()->withErrors('Invalid Marital Status of : ' . $row['f_name'])->withInput();
                    }

                    $staffUser = User::create([
                        'user_type_id' => 6,
                        'role_id' => $role_id,
                        'school_id' => session('school_id'),
                        'state_id' => $row['state_id'],
                        'district_id' => $row['district_id'],
                        'municipality_id' => $row['municipality_id'],
                        'ward_id' => $row['ward_id'],
                        'f_name' => $row['f_name'],
                        'm_name' => $row['m_name'],
                        'l_name' => $row['l_name'],
                        'email' => $row['email'],
                        'local_address' => $row['local_address'] ?? null,
                        'permanent_address' => $row['permanent_address'] ?? null,
                        'password' => bcrypt('password'),
                        'gender' => $row['gender'],
                        'religion' => $row['religion'] ?? null,
                        'dob' => $row['dob'] ?? null,
                        'blood_group' => $row['blood_group'] ?? null,
                        'father_name' => $row['father_name'] ?? null,
                        'father_phone' => $row['father_phone'] ?? null,
                        'mother_name' => $row['mother_name'] ?? null,
                        'mother_phone' => $row['mother_phone'] ?? null,
                        'emergency_contact_person' => $row['emergency_contact_person'] ?? null,
                        'emergency_contact_phone' => $row['emergency_contact_phone'] ?? null,
                    ]);
                    // CREATE staff

                    $studentCreate = Staff::create([
                        'user_id' => $staffUser->id,
                        'school_id' => session('school_id'),
                        'employee_id' => $row['employee_id'] ?? null,
                        'department_id' => $department_id,
                        'qualification' => $row['qualification'] ?? null,
                        'work_experience' => $row['work_experience'] ?? null,
                        'marital_status' => $marriedId,
                        'date_of_joining' => $row['date_of_joining'] ?? null,
                        'payscale' => $row['payscale'] ?? null,
                        'basic_salary' => $row['basic_salary'] ?? null,
                        'contract_type' => $row['contract_type'] ?? null,
                        'shift' => $row['shift'],
                        'medical_leave' => $row['medical_leave'] ?? null,
                        'casual_leave' => $row['casual_leave'] ?? null,
                        'maternity_leave' => $row['maternity_leave'] ?? null,
                        'role' => $role_id,
                    ]);
                }
                // dd("he");
            }
            DB::commit();
            return back()->with('success', 'Data has been uploaded');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function roleIdentification($role_name)
    {
        $roleId = null;
        switch ($role_name) {
            case "Teacher":
                $roleId = 6;
                break;
            case "Accountant":
                $roleId = 7;
                break;
            case "Librarian":
                $roleId = 8;
                break;
            case "Principal":
                $roleId = 9;
                break;
            case "Receptionist":
                $roleId = 10;
                break;
            default:
                $roleId = null;
        }

        return $roleId;
    }
    public function departmentIdentification($department_name)
    {
        $departmentId = null;
        switch ($department_name) {
            case "Academic":
                $departmentId = 1;
                break;
            case "Library":
                $departmentId = 2;
                break;
            case "Sports":
                $departmentId = 3;
                break;
            case "Science":
                $departmentId = 4;
                break;
            case "Commerce":
                $departmentId = 5;
                break;
            case "Arts":
                $departmentId = 6;
                break;
            case "Exam":
                $departmentId = 7;
                break;
            case "Admin":
                $departmentId = 8;
                break;
            case "Finance":
                $departmentId = 9;
                break;
            default:
                $departmentId = null;
        }

        return $departmentId;
    }
    public function maritialStatus($status)
    {
        echo $status;
        $maritalStatus = null;
        switch ($status) {
            case "Married":
                $maritalStatus = 1;
                break;
            case "Unmarried":
                $maritalStatus = 0;
                break;
            case "Devorced":
                $maritalStatus = 2;
                break;
            case "Widow":
                $maritalStatus = 3;
                break;
            case "Separeted":
                $maritalStatus = 4;
                break;
            default:
                $maritalStatus = null;
        }
        echo ($maritalStatus);
        return $maritalStatus;
    }


}