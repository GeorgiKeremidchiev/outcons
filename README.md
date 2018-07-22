# outcons
Test task 

In real world should have at least error handling. 

Demo: http://5.189.138.212:8109/

Task description:

1) SQL Server:

- Tables for users, projects and time log
- The information in the table for:
    - users to be first name, last name
    - projects to be id and project name
    - time log to be user id, project id, date, hours
- Each user can have n records

2) Stored procedure to initialize the database:

- Еach time it starts to delete the content of users, projects, time log tables
- Generate 100 records with random first, last names and e-mail address based on:
    - First name: John, Gringo, Mark, Lisa, Maria, Sonya, Philip, Jose, Lorenzo, George, Justin
    - Last name: Johnson, Lamas, Jackson, Brown, Mason, Rodriguez, Roberts, Thomas, Rose, McDonalds
    - Domain: hotmail.com, gmail.com, live.com
- Each record should be random first name and last name. The e-mail should be: <first name>.<last name>@<random domain>
- For each record should generate three projects: My own, Outcouns, Free Time
- For each record in users should generate random number of records (1-20) in time log table, with random project id and random number of hours (0.25-8.00). Records for each day should not exceed 8 hours
    
 
