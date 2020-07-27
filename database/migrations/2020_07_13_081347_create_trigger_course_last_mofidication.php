<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerCourseLastMofidication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE TRIGGER course_last_update
            AFTER INSERT ON `mdl_logstore_standard_log` FOR EACH ROW
            BEGIN
                IF (NEW.courseid <> 0 AND NEW.action <> 'viewed')
                THEN
                        UPDATE mdl_course
                        SET last_modify_user = NEW.userid, last_modify_time = NEW.timecreated, last_modify_action = CONCAT(NEW.action, ' ', NEW.target)
                        WHERE id = NEW.courseid;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `course_last_update`');
    }
}
