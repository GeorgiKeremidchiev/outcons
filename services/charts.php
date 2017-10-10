<?php
    include_once("db.php");
    $db_conn = open();

    $result = null; 
    if(isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'load' && isset($_REQUEST['source']))
    {
		switch($_REQUEST['source']) {
			case 'users':
					$usersId = null;
					if(isset($_REQUEST['users-id'])) {
						$usersId = (int)$_REQUEST['users-id'];
					}
                    loadUsers($usersId); 
				break;
			case 'projects':
                    loadProjects();
				break;
			default:
				print json_encode(['status' => 'error']);
		}
	}

    function loadUsers($usersId)
    {
        global $db_conn;

		$response = array();
		if($usersId) {
			$compare_result = @pg_query($db_conn, "select B.first_name, B.last_name, B.email, A.sum_hours
			from 
			(select sum(hours) as sum_hours, users_id from time_logs A where A.users_id = ".pg_escape_string($usersId)." group by A.users_id) A
			join users B on A.users_id = B.id");
			$row = pg_fetch_object($compare_result);
			array_push($response, [ $row->first_name." ".$row->last_name." ".$row->email, (real)$row->sum_hours, "red" ]);
		}

        $result = @pg_query($db_conn, "select B.first_name, B.last_name, B.email, A.sum_hours
        from 
        (select sum(hours) as sum_hours, users_id from time_logs A group by A.users_id order by sum_hours desc limit 10) A
        join users B on A.users_id = B.id
		order by A.sum_hours desc");

        while($row = pg_fetch_object($result))
        {
            array_push($response, [ $row->first_name." ".$row->last_name." ".$row->email, (real)$row->sum_hours, "blue" ]);
        }

		print json_encode(['status' => 'ok', 'title' => 'Users', 'payload' => $response]);
    }

	function loadProjects()
    {
        global $db_conn;
        $result = @pg_query($db_conn, "select B.name, A.sum_hours
		from
		(select sum(hours) as sum_hours, projects_id from time_logs group by projects_id order by sum_hours desc limit 10) A
		join projects B on A.projects_id = B.id 
		order by A.sum_hours desc");

        $response = array();
        while($row = pg_fetch_object($result))
        {
            array_push($response, [ $row->name, (real)$row->sum_hours, 'blue' ]);
        }

        print json_encode(['status' => 'ok', 'title' => 'Projects', 'payload' => $response]);
    }
?>
