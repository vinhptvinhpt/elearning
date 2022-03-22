<?php

use App\TmsConfigs;
use Illuminate\Database\Seeder;

class GeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $today = date('Y-m-d H:i:s', time());
        //insert value guideline into table Tms_Configs
        //check for create target guideline
        $guide_line = TmsConfigs::where('target', '=', 'guideline')->get()->first();
        if (is_null($guide_line)) {
            $guide_line_insert = array(
                'target' => 'guideline',
                'content' => '',
                'editor' => TmsConfigs::EDITOR_TEXTAREA,
                'created_at' => $today,
                'updated_at' => $today
            );
            TmsConfigs::insert($guide_line_insert);
        }
    }
}
