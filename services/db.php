<?php
    function open()
    {
        $db_conn = @pg_connect("host=localhost port=5432 dbname=outcons user=xxxxxxxxx password='xxxxxxx'") or die("Could not connect to db.");
        return $db_conn;
    }
?>

