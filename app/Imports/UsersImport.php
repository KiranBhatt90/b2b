<?php
namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $email = $row['email'];

        $existingStudent = Student::where('email', $email)->first();

        if ($existingStudent) {
            // Student with the same email already exists
            return null; // Skipping this record
        }
        $lowerGradeName = strtolower($row['grade']);

        // Map class_name values to corresponding IDs
        $masterClassNames = [
            'grade 1' => 1,
            'grade 2' => 2,
            'grade 3' => 3,
            'grade 4' => 4,
            'grade 5' => 5,
            'pre school' => 6,
            'grade 6-10' => 7,
        ];

        if (isset($masterClassNames[$lowerGradeName])) {
            $masterClassId = $masterClassNames[$lowerGradeName];

            $student = new Student([
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
            ]);

            // Set the 'grade' field with the mapped master class ID
            $student->grade = $masterClassId;

            return $student;
        } else {
            // If gradeName is not found in the mapping array, skip the row
            return null;
        }
    }
}

