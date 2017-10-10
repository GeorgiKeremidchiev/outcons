<?php
    include_once("db.php");
    $db_conn = open();

    $result = null;
    if(isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'reset')
    {
        @pg_query($db_conn, "select init()");
		//In real world we must get result and check for errors.
        print json_encode([ 'status' => 'ok' ]);
    }
?>
