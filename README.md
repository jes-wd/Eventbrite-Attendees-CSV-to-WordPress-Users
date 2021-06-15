# Eventbrite-Attendees-CSV-to-WordPress-Users

This is a plugin which was built rushed, with an impossible deadline to properly test. It is incomplete in many ways. I am not planning to finish it, but I or someone else may find it useful at some stage.


### Directions for use
Add a file called "new_users.csv" to the "csv" folder in this plugins directory and then, while logged in as an administrator, visit "https://SITE_URL/eventbrite_csv_to_users to trigger the function that will create all the users listed in the Eventbrite generated CSV file. The file gets renamed when the functions are complete to avoid any issues that could be caused with running the same CSV twice.

### Challenges faced
* There were significant issues with some mail servers. User account details need to be emailed to each user who had an account created. Only a high end mail server such as SendGrid was able to handle it, with strong throttling of speed. Don't expect this function to complete quickly. 
* 

### To do: 
* Admin page with a button that will trigger the function
* Ajax progress data when function is run
* CSV file upload ability on admin page instead of CSV upload through FTP
* Better logging of progress and errors