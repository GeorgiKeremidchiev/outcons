<?php
    include_once("consts.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Outcons test task</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css">
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <link href="libs/jquery-loader/jquery.loader.css" rel="stylesheet">
    <link rel="stylesheet" href="libs/jquery-ui/jquery-ui.min.css">

    <script src="libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="libs/jquery-loader/jquery.loader.js"></script>
    <script src="libs/jquery-ui/jquery-ui.min.js"></script>

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

			//charts
			$('input[type=radio][name=chart-data-source]').change(function() {
				showHideCompareBtn();
        		loadData(this.value);
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
                                            $('#users-table tbody').append("<tr><td>" + user.first_name + "</td><td>" + user.last_name + "</td><td>" + user.email + "</td>" 
																				+ '<td><a href="#" class="btn btn-primary btn-sm compare-hours-users" onclick="compareHoursUsers(' + user.id +')">Compare</a></td></tr>');
                                        });
										showHideCompareBtn();
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

		var showHideCompareBtn = function() {
			switch($('input[type=radio][name=chart-data-source]:checked').val()) {
                    case 'users':
                            $('.compare-hours-users').show();
                        break;
                    case 'projects':
                            $('.compare-hours-users').hide();
                        break;
                }
		}

    	// Load the Visualization API and the corechart package.
      	google.charts.load('current', {'packages':['corechart']});

      	// Set a callback to run when the Google Visualization API is loaded.
      	google.charts.setOnLoadCallback(initCharsData);
	
		var source = 'users';
		function initCharsData() {
			loadData(source);
		}

		function compareHoursUsers(usersId) {
			loadData('users', usersId);
		}

		function loadData(source, usersId, fromDate, toDate) {
			$.ajax( 
                    {   
                        url: "<?php echo(WEB_ROOT); ?>charts.php",
                        dataType: "json",
                        type: "POST",
                        async: true,
                        data: 
                        {
                            "cmd": "load",
							"source": source,
							"users-id": usersId,
							"from-date": fromDate,
							"to-date": toDate
                        },
                        complete: function(result)
                        {   
                            if(result.status == 200 && typeof result.responseJSON != 'undefined' && typeof result.responseJSON.status != 'undefined')
                            {   
                                if(result.responseJSON.status === 'ok')
                                {
									drawChart(result.responseJSON.title, result.responseJSON.payload)
                                }
                            }
                        }
                    });

		}

      	function drawChart(title, payload) {
			if(payload.length === 0) {
				$("#chart-div").hide();
				$("#data-source-char").hide();
				return;
			} else {
				$("#chart-div").show();
                $("#data-source-char").show();
			}	
			// Create the data table.
			payload.unshift(['Element', 'Hours', { role: 'style' }]);
			var data = new google.visualization.arrayToDataTable(payload);
    	    // Set chart options
 	    	var options = {'title':title,
            	'width':500,
                'height':300};

        	// Instantiate and draw our chart, passing in some options.
	        var chart = new google.visualization.BarChart(document.getElementById('chart-div'));
    	    chart.draw(data, options);
		}

    //Datapicker
	$( document ).ready(function() {
		$( "#from" ).val('');
		$( "#to" ).val('');
		var dateFormat = "mm/dd/yy",
      	from = $( "#from" )
        	.datepicker({
          		defaultDate: "+1w",
          		changeMonth: true,
          		numberOfMonths: 1
        	})
        .on( "change", function() {
        	to.datepicker( "option", "minDate", getDate( this ) );
        }),
      	to = $( "#to" ).datepicker({
        	defaultDate: "+1w",
        	changeMonth: true,
        	numberOfMonths: 1
      	})
      	.on( "change", function() {
        	from.datepicker( "option", "maxDate", getDate( this ) );
      	});
 
    	function getDate( element ) {
		    var date;
      		try {
		        date = $.datepicker.parseDate( dateFormat, element.value );
		      } catch( error ) {
		        date = null;
      		}

      		return date;
    	}

		$('#apply-date').click(function() {
			loadData($('input[type=radio][name=chart-data-source]:checked').val(), null, 
						$( "#from" ).val(), $( "#to" ).val())		
		});	
	});
   </script>
</head>
<body>
	<div class="col-sm-12">
		<div class="col-xs-6"></div>
		<div class="col-xs-6">
			<label for="from">From</label>
			<input type="text" id="from" name="from">
			<label for="to">to</label>
			<input type="text" id="to" name="to">
			<a href="#" id="apply-date" class="btn btn-success btn btn-sm">Apply</a>
		</div>
	</div>
	<div class="col-sm-12">
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
    	    <div id="chart-div"></div>
			<div id="data-source-char" class="form-group">
	      		<label class="col-lg-2 control-label">Data source</label>
    	  		<div class="col-lg-10">
        			<div class="radio">
		    	      <label>
		        	    <input type="radio" name="chart-data-source" value="users" checked="">
						Users
			          </label>
    	    		</div>
			        <div class="radio">
		    	      <label>
		        	    <input type="radio" name="chart-data-source" value="projects">
		            	Projects
			          </label>
			        </div>
	    	  	</div>
    		</div>
    	</div>
	</div>
</body>
</html>
