#Deployment Instructions

1. Download the github created zip file and extract into the web root of the deployment location (most likely `www`).  Verify that .htaccess and .gitignore exist.

2. `git init` a new repo and `git add .` to add all files. If the `.gitignore` file is in place this should exclude the `config.php` file from the repo (you want this).  Commit.

3. `chmod` the `cache` and `files` directories for holding local cache data and user uploads, respectively, so that they are writable by Apache.

4. Update config.php with the correct local configuration settings for the database server and environment vars.

5. Finish whatever apache configuration is needed and test in your browser.  You should get a confirmation message that the site is running properly.

6. SUCCESS! Time for brandy.
