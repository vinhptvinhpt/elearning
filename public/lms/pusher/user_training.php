<?php

require_once('../config.php');


$sql = 'select
ttp.id,
ttp.name
from tms_traninning_users ttu
inner join tms_traninning_programs ttp on ttp.id = ttu.trainning_id
where ttp.deleted = 0
and ttu.user_id = ' . $USER->id;

$list = array_values($DB->get_records_sql($sql)); //Auto group by moodle

$response = json_encode(['list' => $list]);

echo $response;
