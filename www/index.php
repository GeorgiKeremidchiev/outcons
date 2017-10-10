<?php
    include_once("consts.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Outcons test task</title>

    <!-- css -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css">
    <link href="libs/jquery-loader/jquery.loader.css" rel="stylesheet">

    <script src="libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="libs/jquery-loader/jquery.loader.js"></script>

	<script type="text/javascript">
		var currUserPage = 0;
		var maxUserPage;
        var orderByEmailUsers = 'asc';
		$( document ).ready(function() {
			loadUsers();

			$('#prev-users-page').click(function() {
				if(currUserPage > 0) { currUserPage--; }
				loadUsers();
			});

            $('#next-users-page').click(function() {
				if(typeof maxUserPage !== 'undefined') {
					if(currUserPage < maxUserPage) { currUserPage++; }
                	loadUsers();
				}
            });

            $('#order-by-email-users').click(function() {
                if(orderByEmailUsers === 'asc') { 
                    orderByEmailUsers = 'desc';
                    $("#order-by-email-arrow-users").html('&#8593;'); 
                } else { 
                    orderByEmailUsers = 'asc';
                    $("#order-by-email-arrow-users").html('&#8595;');
                }                

                loadUsers();
            });

            $('#init-db-reload-page').click(function() {
                initDbReloadPage();
            });
		});

		var loadUsers = function() {
			$('#users-table').loader('show', { delay: 0 });
			$.ajax( 
                    {
                        url: "<?php echo(WEB_ROOT); ?>users.php",
                        dataType: "json",
                        type: "POST",
                        async: true,
                        data: 
                        {
                            "cmd": "load",
                            "page": currUserPage,
                            "order-by-email": orderByEmailUsers
                        },
                        complete: function(result)
                        {   
                            if(result.status == 200 && typeof result.responseJSON != 'undefined' && typeof result.responseJSON.status != 'undefined')
                            {   
                                if(result.responseJSON.status === 'ok')
                                {
                                        $('#users-table tbody').html('');
                                        result.responseJSON.users.forEach(function(user) { 
                                            $('#users-table tbody').append("<tr><td>" + user.first_name + "</td><td>" + user.last_name + "</td><td>" + user.email + "</td></tr>");
                                        });

										maxUserPage = result.responseJSON.pages;
                                        $('#users-table').loader('hide');
                                }
                            }
                        }
                    });
		}

        var initDbReloadPage = function() {
            $('body').loader('show', { delay: 0 });
			$.ajax( 
                    {   
                        url: "<?php echo(WEB_ROOT); ?>system.php",
                        dataType: "json",
                        type: "POST",
                        async: true,
                        data: 
                        {
                            "cmd": "reset",
                        },
                        complete: function(result)
                        {   
                            if(result.status == 200 && typeof result.responseJSON != 'undefined' && typeof result.responseJSON.status != 'undefined')
                            {   
                                if(result.responseJSON.status === 'ok')
                                {
            						location.reload();            
                                }
                            }
                        }
                    });
        }
	</script>
</head>
<body>
    <div class="col-xs-6">
    	<table id="users-table" class="table table-hover">
			<thead>
      			<tr>
			        <th>First Name</th>
        			<th>Last Name</th>
        			<th><a id="order-by-email-users" href="#" style="text-decoration: none;">Email <span id="order-by-email-arrow-users">&#8595;</span></a></th>
      			</tr>
    		</thead>
		    <tbody>
			</tbody>
  		</table>
        <div>
		    <div class="col-xs-6"><a id="prev-users-page" href="#" class="btn btn-primary">&#8612; Назад</a></div>
		    <div class="col-xs-6"><a id="next-users-page" href="#" class="btn btn-primary">Напред &#x21A6;</a></div>
        </div>
        <div class="jumbotron" style="background-color: #FFF">
		        <a href="#" id="init-db-reload-page" class="btn btn-danger btn-block">Инициализация на базата и презарежда страницата</a> 
        </div>
    </div>
    <div class="col-xs-6">
        Right side
    </div>
</body>
</html>
