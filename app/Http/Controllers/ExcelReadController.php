<?php

namespace App\Http\Controllers;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ExcelReadController extends Controller
{
    public function uploadExcel(Request $request)
    {
        $districtList = [
            'Colombo' => 1,
            'Galle' => 2,
            'Gampaha' => 3,
            'Kaluthara' => 4,
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
            'Rathnapura' => 25
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

            $districtName = trim($rowData[3]);
            $districtId = $districtList[$districtName] ?? 0;

            if ($districtId === 0) {
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
                Log::alert('Missing required fields  - ' . $rowIndex . ' - ' . $rowData[1].' - '. $rowData[3].' - '. $rowData[6].' - '. $rowData[7].' - '. $rowData[9].' - '. $rowData[8].' - '. $rowData[11]. '\n');
                continue;
            }
            Student::create([
                'serial_no' => $serialNo,
                'district' => $districtId,
                'school' => $school,
                'age' => $ageGroup,
                'studentName' => $studentName,
                'stream' => $stream,
                'language' => $stream === 'Art' ? NULL : $language,
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

            $districtName = trim($rowData[3]);
            $districtId = $districtList[$districtName] ?? 0;
            if ($districtId === 0) {
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
                Log::alert('Missing required fields ' . $rowIndex . ' - ' . $rowData[1].' - '. $rowData[3].' - '. $rowData[6].' - '. $rowData[7].' - '. $rowData[9].' - '. $rowData[8].' - '. $rowData[11]. '\n');
                continue;
            }

            Student::create([
                'serial_no' => $serialNo,
                'district' => $district,
                'school' => $school,
                'age' => $ageGroup,
                'studentName' => $studentName,
                'stream' => $stream,
                'language' => $stream === 'Art' ? NULL : $language,
                'marking_status' => 0, // Default value
            ]);
        }

        return response()->json(['message' => 'File processed successfully'], 201);
    }
}
