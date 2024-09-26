# User-Activity-Log
  The User Activity Log plugin allows administrators to track user activities on a WordPress site. The plugin includes features for logging user actions,export(csv,excel), backing up and restoring logs, and controlling access to the log data based on user roles. 
# Installation: 
  1. __Upload the Plugin:__
      * Download the plugin ZIP file. 
      * Go to your WordPress admin dashboard. 
      * Navigate to __Plugins > Add New__. 
      * Click on __Upload Plugin__. 
      * Choose the downloaded __ZIP file__ and click __Install Now__.
  2. __Activate the Plugin:__
      * After installation, click __Activate Plugin__.  
# Setup and Configuration: 
   1. __Configure Logging Settings:__
       * Go to __Settings__. 
          * You will see options to configure notification preferences and access control. 
       * __Notification Preferences:__ Check the types of notifications you want to receive (e.g., Content Change, Failed Login). 
       * __Access Control:__ Select the user roles that should have access to view the logs.
             * __Access Control has two type:__
                  * Role-Based Access Control
                  * User-Specific Access Control 
    2. __Backup and Restore Logs:__ 
        * __Create a Backup:__ 
              * Go to __User Activity Log page__. 
              * Click the __Create Backup__ button to generate a backup of the logs.
        * __Restore from Backup:__
              * Go to __User Activity Log page__. 
              * Choose a backup file that save in plugin folder (include) to restore and click the __Restore Backup__ button.
    3. __Viewing Logs:__
         * Go to __User Activity Log page__. 
         * Use the filter options to search for specific user, actions, date ranges. 
         * The logs will be displayed in a table format.
     # Using the Plugin: 
    1. __Viewing Activity Logs:__ 
         * Navigate to __User Activity Log page__. 
         * Use the search and filter options to find specific log entries. 
         * You can filter by user, action type and date range. 
    2. __Export Logs:__
         * On the User Activity Log page,(at the end of the page) use the Export CSV or Export Excel buttons to download the logs in the desired format.
    3. __Setting Notifications:__
         * Go to __Settings__. 
         * Configure the notification preferences to receive alerts for specific types of activities.   
    # Troubleshooting: 
       1. __Backup or Restore Fails:__
         * Ensure that the plugin has the necessary file permissions to write and read files in the backups directory. 
         * Check for errors in the file upload process. Make sure the backup file format is correct and not corrupted.    
       2. __Logs Not Showing:__ 
         * Ensure that the log table in the database is correctly created and populated. 
         * Verify that the filtering criteria are set correctly. 
       3. __Access Control Issues:__ 
         * Check that the selected roles in the settings match the roles assigned to users. 
         * Ensure that the logging_settings option is properly saved and retrieved. 
     # FAQs: 
       1.  __How do I restore logs from a backup file?__ <br>
            * Navigate to User Activity Log page. Upload the backup file and click the Restore Backup button. Ensure the backup file is in the correct format(SQL format) and not corrupted. 
       2.  __Can I export logs to formats other than CSV and Excel?__ <br>
            * Currently, the plugin supports exporting logs only in CSV and Excel formats. Future updates may include additional formats.
       3.  __How can I enable or disable specific notifications?__ <br>
            * Go to __Settings__ and adjust the notification preferences according to your needs 
       4. __What should I do if I can't access the log page?__ <br>
            * Verify that your user role is included in the access control settings. If you are an admin and still cannot access the logs, check the settings for any misconfigurations.
    # Screenshot:
     [user activity log page(part-1)](https://drive.google.com/file/d/12jI8VHw9VoT1xBxvGrcsKFM2PFtU-FOS/view?usp=drive_link)<br>
     [user activity log page (part-2)](https://drive.google.com/file/d/1eCV-Q5QCosO1Jqnie52EdEDi3u5H6dkI/view?usp=drive_link)<br>
     [Setting page](https://drive.google.com/file/d/1nQM1axU6Ro2y5exrO356Evs-g0dEv19N/view?usp=drive_link)<br>
     [Dashboard Overview](https://drive.google.com/file/d/1voIXvvYQLZQlWLhOKsNQpApUpXUlpFgH/view?usp=drive_link)  
