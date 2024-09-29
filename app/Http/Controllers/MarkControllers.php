<?php

namespace App\Http\Controllers;

use App\Models\Marks;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarkControllers extends Controller
{
    public function deleteMark(int $id)
  {
    $mark = Marks::find($id);
    $mark->delete();
    return response()->json(['message' => ' Mark deleted successfully'], 200);
  }


  //update student
  public function updateMark(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'mark_01' => 'required',
      'mark_02' => 'required',
      'mark_03' => 'required',
      'mark_04' => 'required',
      'mark_05' => 'required',
      'total' => 'required',
      'teacher_id'=> 'required',
      'student_id'=> 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 500,
        'message' => "Something Went Wrong!",
        'errors' => $validator->errors(),
      ], 500);
    } else {
      $mark = Marks::find($id);
      $mark->mark_01 = $request->mark_01;
      $mark->mark_02 = $request->mark_02;
      $mark->mark_03 = $request->mark_03;
      $mark->mark_04 = $request->mark_04;
      $mark->mark_05 = $request->mark_05;
      $mark->total= $request->total;
      $mark->teacher_id= $request->teacher_id;
      $mark->student_id= $request->student_id;
      $result = $mark->save();
    }
    return response()->json($result, 201);
  }


  //get all student  details
  public function getAllMarks()
  {
    $marks = Marks::with(['students'])->get();
    return response()->json($marks, 200);
  }


  //save student
  public function saveMark(Request $request)
  {
    $validator = Validator::make($request->all(), [
        'mark_01' => 'required',
        'mark_02' => 'required',
        'mark_03' => 'required',
        'mark_04' => 'required',
        'mark_05' => 'required',
        'total' => 'required',
        'teacher_id'=> 'required|int|max:190',
        'student_id'=> 'required|int|max:190',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 500,
        'message' => "Something Went Wrong!",
        'errors' => $validator->errors(),
      ], 500);
    } else {
      $mark = new Marks();
      $mark->mark_01 = $request->mark_01;
      $mark->mark_02 = $request->mark_02;
      $mark->mark_03 = $request->mark_03;
      $mark->mark_04 = $request->mark_04;
      $mark->mark_05 = $request->mark_05;
      $mark->total= $request->total;
      $mark->teacher_id= $request->teacher_id;
      $mark->student_id= $request->student_id;
      $mark->save();
      $student = Student::find($request->student_id);
      $student->marking_status=($student->marking_status+1);
      $student->save();
    }
    return response()->json([
      'status' => 200,
      'message' => "Marks saved successfully",
      'request_data' => $request->all(),
    ], 200);
  }
}
