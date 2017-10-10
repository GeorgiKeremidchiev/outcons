<?php
    include_once("db.php");
    $db_conn = open();

    $result = null;
    if($_REQUEST['cmd'] == 'load' && isset($_REQUEST['page']) && isset($_REQUEST['order-by-email']))
    {
        $order_by_email = 'asc';
        if($_REQUEST['order-by-email'] === 'desc') 
        {
            $order_by_email = 'desc';
        }

        $result = @pg_query($db_conn, "select * from users order by email ".$order_by_email." offset ".pg_escape_string($_REQUEST['page'])." limit 10");

        $response = array();
        while($row = pg_fetch_object($result))
        {
            array_push($response, $row);
        }

        $result_pages = @pg_query($db_conn, "select ceil(count(*)/10.0) as users_pages from users;");
        $row_pages = pg_fetch_object($result_pages);
        print json_encode([ 'status' => 'ok', 'users' => $response, 'pages' => $row_pages->users_pages ]);
    }
?>
