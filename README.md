# Contact_system

Contact System Project
Welcome to the Contact System project! This web-based application allows users to manage their contacts, send and receive friend requests, and maintain a list of shared contacts. This README provides essential information to get you started with the project.

Installation
Follow these steps to set up the Contact System project:

Clone the repository to your local machine:

bash
Copy code
git clone https://github.com/KewOfficial/Contact_system.git
Import the database schema and initial data using the provided SQL dump in schema.sql. You can use the following command:

bash
Copy code
mysqldump -u root -p --no-data kihungwe > schema.sql
Enter your MySQL root password when prompted.

Configure the database connection in the PHP files where necessary (e.g., config.php). Update the database connection parameters accordingly:

php
Copy code
// config.php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'kihungwe';
Set up a web server (e.g., Apache, Nginx) to host the project. Make sure PHP is installed and configured.

Access the project through a web browser, and you should be ready to go.
