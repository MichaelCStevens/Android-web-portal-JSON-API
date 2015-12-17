I. Setting Up Web Portal Environment

   Upload all the files onto the FTP and then create a database. On the newly created database, run the import.sql file located in the project root.
   
   Open \classes\db.class.php and change the login parameters for the database (line 41) 

   Open the Following files and enter the absolute paths for the project on the server:
   ($this->fileRoot and $this->uri need to be updated to the correct paths)
        - \classes\ajax.requests.php
        - \classes\device.sort.php
        - \classes\router.class.php 
   
   

II. Changing API url in Android Source

    Open the following files and change the string to the url where the portal is hosted:
    ( public static final String API_URL )
        - asyncInspection.java
        - asyncLogin.java
        - asyncPhone.java
        - asyncRegister.java
        - asyncScan.java
        - asyncSMS .java

