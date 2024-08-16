<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TeacherControllers extends Controller
{
    //delete teacher
   public function deleteTeacher(int $id){
    $teacher=Teacher::find($id);
    $teacher->delete();
    return response()->json(['message' => ' Teacher deleted successfully'], 200);
   }


   //update teacher
   public function updateTeacher(Request $request, $id){
    $validator = Validator::make($request->all(), [
        'admin_id' => 'required|int|max:191',
        'user_name'=>'required|string|max:191',
        'language' => 'required|string|max:191'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status' => 500,
          'message' => "Something Went Wrong!",
          'errors' => $validator->errors(),
        ], 500);
      } else {
        $teacher=Teacher::find($id);
        $teacher->admin_id = $request->admin_id;
        $teacher->user_name = $request->user_name;
        $teacher->language=$request->language;
        $result = $teacher->save();
      }
      return response()->json($result,201);
   }

   //get all teacher  details
   public function getAllTeacher(){
    $teachers = DB::table('teachers')
        ->get();
      return response()->json($teachers,200);
   }

   //save teacher
   public function saveTeacher(Request $request){
    $validator = Validator::make($request->all(), [
        'admin_id' => 'required|int|max:191',
        'user_name'=>'required|string|max:191',
        'password' => 'required|string|max:191',
        'language' => 'required|string|max:191'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status' => 500,
          'message' => "Something Went Wrong!",
          'errors' => $validator->errors(),
        ], 500);
      } else {
        $teacher = new Teacher();
        $teacher->admin_id = $request->admin_id;
        $teacher->user_name = $request->user_name;
        $teacher->password = bcrypt($request->password);
        $teacher->language=$request->language;
        $result = $teacher->save();
      }
      return response()->json($result,201);
   }

   //get teacher by id
   public function searchTeacher($input){
    $teachers = DB::table('teachers')
    ->where('teacher_id', 'LIKE', '%' . $input . '%')
    ->orWhere('user_name', 'LIKE', '%' . $input . '%')
    ->orWhere('language', 'LIKE', '%' . $input . '%')
    ->get();
  return response()->json($teachers, 201);
   }

}
