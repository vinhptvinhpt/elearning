<?php

use Illuminate\Database\Seeder;
use App\TmsOrganizationHistaffMapping;

class TmsOrganizationHistaffMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            ['tms_code'=>'BOM', 'histaff_code'=> 'BOM'],
            ['tms_code'=>'LNC', 'histaff_code'=> 'LC'],
            ['tms_code'=>'ACC', 'histaff_code'=> 'AC'],
            ['tms_code'=>'SLS', 'histaff_code'=> 'SA'],
            ['tms_code'=>'OPS', 'histaff_code'=> 'OP'],
            ['tms_code'=>'IT', 'histaff_code'=> 'IT'],
            ['tms_code'=>'MKT', 'histaff_code'=> 'MB'],
            ['tms_code'=>'CON', 'histaff_code'=> 'CT'],
            ['tms_code'=>'DTB', 'histaff_code'=> 'DB'],
            ['tms_code'=>'PRJ', 'histaff_code'=> 'PJ'],
            ['tms_code'=>'HRA', 'histaff_code'=> 'HR'],
            ['tms_code'=>'PDN', 'histaff_code'=> 'PD'],
            ['tms_code'=>'TND', 'histaff_code'=> 'TD'],
        ];
        TmsOrganizationHistaffMapping::insert($data); // Eloquent approach
    }
}
