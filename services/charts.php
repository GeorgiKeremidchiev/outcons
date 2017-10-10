<?php
    include_once("db.php");
    $db_conn = open();

    $result = null; 
    if(isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'load' && isset($_REQUEST['source']))
    {
        $from_date = null;
        $to_date = null;
        if(isset($_REQUEST['from-date']) && isset($_REQUEST['to-date'])) 
        {
            if(validateDate($_REQUEST['from-date']) && validateDate($_REQUEST['to-date'])) 
            {
                $from_date = $_REQUEST['from-date'];
                $to_date = $_REQUEST['to-date'];
            }
        } 

		switch($_REQUEST['source']) {
			case 'users':
					$usersId = null;
					if(isset($_REQUEST['users-id'])) {
						$usersId = (int)$_REQUEST['users-id'];
					}
                    loadUsers($usersId, $from_date, $to_date); 
				break;
			case 'projects':
                    loadProjects($from_date, $to_date);
				break;
			default:
				print json_encode(['status' => 'error']);
		}
	}

    function loadUsers($usersId, $from_date, $to_date)
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

        $date_part = '';
        if($from_date !== null && $to_date !== null)
        {
            $date_part = " where date >= '".pg_escape_string($from_date)."' and date <= '".pg_escape_string($to_date)."'";
        }

        $result = @pg_query($db_conn, "select B.first_name, B.last_name, B.email, A.sum_hours
        from 
        (select sum(hours) as sum_hours, users_id from time_logs $date_part group by users_id order by sum_hours desc limit 10) A
        join users B on A.users_id = B.id
		order by A.sum_hours desc");
        while($row = pg_fetch_object($result))
        {
            array_push($response, [ $row->first_name." ".$row->last_name." ".$row->email, (real)$row->sum_hours, "blue" ]);
        }

		print json_encode(['status' => 'ok', 'title' => 'Users', 'payload' => $response]);
    }

	function loadProjects($from_date, $to_date)
    {
        global $db_conn;

       	$date_part = '';
        if($from_date !== null && $to_date !== null)
        {
            $date_part = " where date >= '".pg_escape_string($from_date)."' and date <= '".pg_escape_string($to_date)."'";
        } 

        $result = @pg_query($db_conn, "select B.name, A.sum_hours
		from
		(select sum(hours) as sum_hours, projects_id from time_logs $date_part group by projects_id order by sum_hours desc limit 10) A
		join projects B on A.projects_id = B.id 
		order by A.sum_hours desc");

        $response = array();
        while($row = pg_fetch_object($result))
        {
            array_push($response, [ $row->name, (real)$row->sum_hours, 'blue' ]);
        }

        print json_encode(['status' => 'ok', 'title' => 'Projects', 'payload' => $response]);
    }

    //mm/dd/yyyy
    function validateDate($inputDate) 
    {
        if(!isset($inputDate)) 
        {
            return false;    
        }

        if(strlen($inputDate) != 10)  
        {
            return false;
        }

        $parts = explode("/", $inputDate);
        if(count($parts) != 3) 
        {
            return false;
        }

        return checkdate($parts[0], $parts[1], $parts[2]);
    }
?>
