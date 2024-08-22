<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentControllers extends Controller
{
    //delete student
  public function deleteStudent(int $id)
  {
    $student = Student::find($id);
    $student->delete();
    return response()->json(['message' => ' Teacher deleted successfully'], 200);
  }


  //update student
  public function updateStudent(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'setial_no' => 'required|int|max:191',
      'medium' => 'required|string|max:190',
      'age'=>'required|int|max:190',
      'stream' => 'required|INT|max:190',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 500,
        'message' => "Something Went Wrong!",
        'errors' => $validator->errors(),
      ], 500);
    } else {
      $student = Student::find($id);
      $student->setial_no = $request->setial_no;
      $student->medium = $request->medium;
      $student->age = $request->age;
      $student->stream= $request->stream;
      $result = $student->save();
    }
    return response()->json($result, 201);
  }


  //get all student  details
  public function getAllStudents()
  {
    $students = Student::with(['marks'])->get();
    return response()->json($students, 200);
  }


  //save student
  public function saveStudent(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'setial_no' => 'required|int|max:191',
      'medium' => 'required|string|max:191',
      'age'=>'required|int|max:190',
      'stream' => 'required|string|max:191',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 500,
        'message' => "Something Went Wrong!",
        'errors' => $validator->errors(),
      ], 500);
    } else {
      $student = new Student();
      $student->setial_no = $request->setial_no;
      $student->medium = $request->medium;
      $student->age = $request->age;
      $student->stream= $request->stream;
      $student->save();
    }
    return response()->json([
      'status' => 200,
      'message' => "Teacher saved successfully",
      'request_data' => $request->all(),
    ], 200);
  }

  
}
