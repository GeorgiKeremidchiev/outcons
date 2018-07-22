# outcons
Test task 

In real world should have at least error handling, OOP, logs etc. 

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
- Each record should be random first name and last name. The e-mail should be: (first name).(last name)@(random domain)
- For each record should generate three projects: My own, Outcouns, Free Time
- For each record in users should generate random number of records (1-20) in time log table, with random project id and random number of hours (0.25-8.00). Records for each day should not exceed 8 hours
    
3) User interface should be one page divided into two columns at 50 percent:

- The left column should contain grid with users divided by 10 per page using SQL Pagination
- Sort the table
- Date filter "From" ... "To"
- Right column should contain bar chart (Google Charts). 1 bar for TOP 10 users with the highest number of hours for the selected period. The size of the bar is the amount of hours per user or project. Radio button to select user or project
- Add button Compare for each user from users. With AJAX get data for hours for selected user. Set this data to chart in red. This information should only be loaded when you press the Compare button

4) Add button to start procedure to initialize the database and reload the page
