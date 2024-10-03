<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Marks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
            'serialNo' => 'required',
            'district' => 'required',
            'ageGroup' => 'required',
            'stream' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => "Invalid input!",
                'errors' => $validator->errors(),
            ], 500);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $student->image = $path;
        }

        if ($request->hasFile('student_detail')) {
            $studentDetailPath = $request->file('student_detail')->store('student_detail', 'public');
            $student->student_detail = $studentDetailPath;
        }
        // $student->fill($request->all());
        $student->language = $request->language;
        $student->age = $request->ageGroup;
        $student->stream = $request->stream;
        $student->school=$request->school;
        $student->studentName=$request->studentName;
        $student->district = $request->district;
        if ($student->save()) {
            return response()->json([
                'status' => 200,
                'message' => "Student updated successfully",
                'request_data' => $request->all(),
            ], 200);
        }
        return response()->json([
            'status' => 500,
            'message' => "Something went wrong!",
        ], 500);
    }

    //get all student  details
    public function getAllStudents(Request $request)
    {
        $query = Student::query();

        if ($request->has('district') && is_array($request->district)) {
            $query->whereIn('district', $request->district);
        }

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }
        if ($request->has('stream')) {
            $query->where('stream', $request->stream);
        }

        $students = $query->with('marks')->get();
        return response()->json($students, 200);
    }


    public function getAllStudentsWithMark()
    {
        $query = Student::with('marks')->get();
        return response()->json($query, 200);
    }

    //get all student  details
    public function getStudentById(int $id)
    {
        $student = Student::find($id);
        return response()->json($student, 200);
    }

    //save student
    public function saveStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serialNo' => 'required',
            'district' => 'required',
            'ageGroup' => 'required',
            'stream' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => "Invalied input!",
                'errors' => $validator->errors(),
            ], 500);
        } else {
            $student = Student::where('serial_no', $request->serialNo)->first();
            if ($student != null) {
                return response()->json([
                    'status' => 500,
                    'message' => "Student Is Exits!",
                ], 500);
            } else {
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $path = $image->store('images', 'public');
                    $request->merge(['image' => $path]);
                } else {
                    $request->image = null;
                }

                if ($request->hasFile('student_detail')) {
                    $student_detail = $request->file('student_detail');
                    $studentDetailPath = $student_detail->store('student_detail', 'public');
                    $request->merge(['student_detail' => $studentDetailPath]);
                } else {
                    $request->student_detail = null;
                }

                $student = new Student();
                $student->serial_no = $request->input('serialNo');
                $student->district = $request->input('district');
                $student->language = $request->input('language');
                $student->age = $request->input('ageGroup');
                $student->stream = $request->input('stream');
                $student->school=$request->school;
                $student->studentName=$request->studentName;
                $student->image = $request->image;
                $student->student_detail = $request->student_detail;

                $isSave = $student->save();


                // Check if student save successfully or not and return appropriate response.
                if ($isSave) {
                    return response()->json([
                        'status' => 200,
                        'message' => "Student saved successfully",
                        'request_data' => $request->all(),
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => "Something went wrong!",
                    ], 500);
                }
            }
        }
    }
}
