<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Marks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use function Laravel\Prompts\search;
use Illuminate\Support\Facades\Log;

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

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }
        if ($request->has('stream')) {
            $query->where('stream', $request->stream);
        }
        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        $mark = Marks::find($request->teacherId);
        if ($mark != null) {
            $query->where('marking_status', '<', 3);
        }

        $students = $query->get();
        return response()->json($students, 200);
    }


    public function getAllStudentsWithMark()
    {
        $query = Student::with('marks')->get();;
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

    public function uploadExcel(Request $request)
    {
        $districtList = [
            'Colombo' => 1,
            'Galle' => 2,
            'Gampaha' => 3,
            'Kalutara' => 4,
            'Kandy' => 5,
            'Matale' => 6,
            'Nuwara Eliya' => 7,
            'Kegalle' => 8,
            'Hambantota' => 9,
            'Matara' => 10,
            'Jaffna' => 11,
            'Kilinochchi' => 12,
            'Mannar' => 13,
            'Mullaitivu' => 14,
            'Vavuniya' => 15,
            'Ampara' => 16,
            'Batticaloa' => 17,
            'Trincomalee' => 18,
            'Anuradhapura' => 19,
            'Polonnaruwa' => 20,
            'Kurunegala' => 21,
            'Puttalam' => 22,
            'Badulla' => 23,
            'Monaragala' => 24,
            'Ratnapura' => 25
        ];

        // Validate the file input
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        // Store the uploaded file temporarily
        $file = $request->file('excel_file');
        $filePath = $file->getRealPath();

        // Load the Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Sinhala');

        // Iterate through rows in the sheet
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            // Skip the header row
            if ($rowIndex == 1) {
                continue;
            }

            $districtName = $rowData[3];
            $districtId = $districtList[$districtName] ?? 0;

            if ($districtId === null) {
                Log::error("District not found for district: " . $districtName);
                continue;
            }

            $defaultValue = '000000000000'; // Default for string fields
            $serialNo = !empty($rowData[1]) ? $rowData[1] : $defaultValue; // Serial Number
            $district = $districtId; // District
            $ageGroup = !empty($rowData[7]) ? $rowData[7] : $defaultValue;; // age
            $school = !empty($rowData[6]) ? $rowData[6] : $defaultValue; // School
            $studentName = !empty($rowData[9]) ? $rowData[9] : $defaultValue; // Student Name
            $stream = !empty($rowData[8]) ? $rowData[8] : $defaultValue; // Competition (stream)
            $language = !empty($rowData[11]) ? $rowData[11] : $defaultValue;

            if (empty($rowData[1]) || empty($rowData[3]) || empty($rowData[6]) || empty($rowData[7]) || empty($rowData[9]) || empty($rowData[8]) || empty($rowData[11])) {
                Log::alert('Missing required fields  - ' . $rowIndex . ' - ' . json_encode($rowData) . '\n');
            }
            Student::create([
                'serial_no' => $serialNo,
                'district' => $districtId,
                'school' => $school,
                'age' => $ageGroup,
                'studentName' => $studentName,
                'stream' => $stream,
                'language' => $language,
                'marking_status' => 0, // Default value
            ]);
        }

        $sheet = $spreadsheet->getSheetByName('Tamil');

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            // Skip the header row
            if ($rowIndex == 1) {
                continue;
            }

            $districtName = $rowData[3];
            $districtId = $districtList[$districtName] ?? 0;
            if ($districtId === null) {
                Log::error("District not found for district: " . $districtName);
                continue;
            }

            $defaultValue = '000000000000'; // Default for string fields
            $serialNo = !empty($rowData[1]) ? $rowData[1] : $defaultValue; // Serial Number
            $district = $districtId; // District
            $ageGroup = !empty($rowData[7]) ? $rowData[7] : $defaultValue;; // age
            $school = !empty($rowData[6]) ? $rowData[6] : $defaultValue; // School
            $studentName = !empty($rowData[9]) ? $rowData[9] : $defaultValue; // Student Name
            $stream = !empty($rowData[8]) ? $rowData[8] : $defaultValue; // Competition (stream)
            $language = !empty($rowData[11]) ? $rowData[11] : $defaultValue;

            if (empty($rowData[1]) || empty($rowData[3]) || empty($rowData[6]) || empty($rowData[7]) || empty($rowData[9]) || empty($rowData[8]) || empty($rowData[11])) {
                Log::alert('Missing required fields ' . $rowIndex . ' - ' . json_encode($rowData) . '\n');
            }

            Student::create([
                'serial_no' => $serialNo,
                'district' => $district,
                'school' => $school,
                'age' => $ageGroup,
                'studentName' => $studentName,
                'stream' => $stream,
                'language' => $language,
                'marking_status' => 0, // Default value
            ]);
        }

        return response()->json(['message' => 'File processed successfully'], 201);
    }
}
