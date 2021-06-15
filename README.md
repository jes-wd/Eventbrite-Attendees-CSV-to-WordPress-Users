# Eventbrite-Attendees-CSV-to-WordPress-Users

Add a file called "new_users.csv" to the "csv" folder in this plugins directory and then, while logged in as an administrator, visit "https://SITE_URL/eventbrite_csv_to_users to trigger the function that will create all the users listed in the Eventbrite generated CSV file. The file gets renamed when the functions are complete to avoid any issues that could be caused with running the same CSV twice.

To do: 
* admin page with a button that will trigger the function
* ajax progress data when function is run