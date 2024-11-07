<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            ['district_id' => 1, 'name' => 'Colombo'],
            ['district_id' => 2, 'name' => 'Galle'],
            ['district_id' => 3, 'name' => 'Gampaha'],
            ['district_id' => 4, 'name' => 'Kalutara'],
            ['district_id' => 5, 'name' => 'Kandy'],
            ['district_id' => 6, 'name' => 'Matale'],
            ['district_id' => 7, 'name' => 'Nuwara Eliya'],
            ['district_id' => 8, 'name' => 'Kegalle'],
            ['district_id' => 9, 'name' => 'Hambantota'],
            ['district_id' => 10, 'name' => 'Matara'],
            ['district_id' => 11, 'name' => 'Jaffna'],
            ['district_id' => 12, 'name' => 'Kilinochchi'],
            ['district_id' => 13, 'name' => 'Mannar'],
            ['district_id' => 14, 'name' => 'Mullaitivu'],
            ['district_id' => 15, 'name' => 'Vavuniya'],
            ['district_id' => 16, 'name' => 'Ampara'],
            ['district_id' => 17, 'name' => 'Batticaloa'],
            ['district_id' => 18, 'name' => 'Trincomalee'],
            ['district_id' => 19, 'name' => 'Anuradhapura'],
            ['district_id' => 20, 'name' => 'Polonnaruwa'],
            ['district_id' => 21, 'name' => 'Kurunegala'],
            ['district_id' => 22, 'name' => 'Puttalam'],
            ['district_id' => 23, 'name' => 'Badulla'],
            ['district_id' => 24, 'name' => 'Monaragala'],
            ['district_id' => 25, 'name' => 'Ratnapura'],
        ];

        foreach ($districts as $district) {
            $district['name'] = strtolower($district['name']);
            District::create($district);
        }
    }
}
