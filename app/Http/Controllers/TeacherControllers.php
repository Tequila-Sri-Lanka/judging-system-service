<?php

namespace App\Http\Controllers;

use App\Models\District_detail;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TeacherControllers extends Controller
{
    //delete teacher
    public function deleteTeacher(int $id)
    {
        $teacher = Teacher::find($id);
        $teacher->delete();
        return response()->json(['message' => ' Teacher deleted successfully'], 200);
    }


    //update teacher
    public function updateTeacher(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adminId' => 'required|int|max:191',
            'userName' => 'required|string|max:191',
            'stream' => 'required|string|max:191',
            'availableDistricts' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => "Something Went Wrong!",
                'errors' => $validator->errors(),
            ], 500);
        } else {
            if ($request->availableDistricts != null) {
                $teacher = Teacher::find($id);
                $teacher->admin_id = $request->adminId;
                $teacher->user_name = $request->userName;
                $teacher->stream = $request->stream;
                $teacher->contact = $request->contact ?? NULL;
                if (!is_null($request->language)) {
                    $teacher->language = $request->language;
                } else
                    $teacher->language = null;
                $result = $teacher->save();
                DB::table('district_details')
                    ->where('teacher_id', $id)
                    ->delete();

                foreach ($request->availableDistricts as $district) {
                    $districtDetail = new District_detail();
                    $districtDetail->teacher_id = $teacher->teacher_id;
                    $districtDetail->district_id = $district;
                    $districtDetail->save();
                }
                return response()->json($result, 201);
            } else {
                $teacher = Teacher::find($id);
                $teacher->admin_id = $request->adminId;
                $teacher->user_name = $request->userName;
                $teacher->password = bcrypt($request->password);
                $teacher->stream = $request->stream;
                $teacher->contact = $request->contact;
                if (!is_null($request->language)) {
                    $teacher->language = $request->language;
                } else
                    $teacher->language = '';
                $result = $teacher->save();
                return response()->json($result, 201);
            }
        }
    }

    //get all teacher  details
    public function getAllTeacher()
    {
        $teachers = Teacher::with(['DistrictDetails', 'DistrictDetails.Districts'])->get();
        return response()->json($teachers, 200);
    }

    //save teacher
    public function saveTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adminId' => 'required|int|max:191',
            'availableDistricts' => 'required|array|max:191',
            'userName' => 'required|string|max:191',
            'password' => 'required|string|max:191',
            'stream' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('contact')) {
                return response()->json([
                    'status' => 422,
                    'message' => "The contact field validation failed: " . $validator->errors()->first('contact'),
                ], 422);
            }

            return response()->json([
                'status' => 400,
                'message' => "Validations faild",
                'errors' => $validator->errors(),
            ], 400);
        } else {

            $teacher = new Teacher();
            $teacher->admin_id = $request->adminId;
            $teacher->user_name = $request->userName;
            $teacher->password = bcrypt($request->password);
            $teacher->stream = $request->stream;
            if (!is_null($request->language)) {
                $teacher->language = $request->language;
            } else
                $teacher->language = null;
            $teacher->save();

            foreach ($request->availableDistricts as $value) {
                $districtDetails = new District_detail();
                $districtDetails->teacher_id = $teacher->teacher_id;
                $districtDetails->district_id = $value;
                $teacher->DistrictDetails()->save($districtDetails);
            }
        }
        return response()->json([
            'status' => 200,
            'message' => "Teacher saved successfully",
            'request_data' => $request->all(),
        ], 200);
    }

    //get teacher by id
    public function searchTeacher($input)
    {
        $teachers = Teacher::with(['DistrictDetails', 'DistrictDetails.Districts'])
            ->where('teacher_id', 'LIKE', '%' . $input . '%')
            ->orWhere('user_name', 'LIKE', '%' . $input . '%')
            ->orWhere('language', 'LIKE', '%' . $input . '%')
            ->get();
        return response()->json($teachers, 201);
    }


     //get student by teacher id
     public function searchStudentTeacherWise($input)
     {
         $teachers = Teacher::with(['Marks.students', 'Marks'])
             ->where('teacher_id', 'LIKE', '%' . $input . '%')
             ->get();
         return response()->json($teachers, 201);
     }

     

    public function login(Request $request)
    {
        $fields = $request->validate([
            'userName' => 'required',
            'password' => 'required'
        ]);

        $user = Teacher::where('user_name', $fields['userName'])
            ->with(['districtDetails' => function ($query) {
                $query->select('district_id', 'teacher_id');
            }])
            ->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad credentials'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
