
## Setting Up Your PHP Website on XAMPP in Windows

  

### 1. Installation of XAMPP

  

1. Download XAMPP from the [official website](https://www.apachefriends.org/index.html).

2. Run the installer and follow the installation wizard.

3. Choose the components you want to install (Apache, MySQL, PHP, phpMyAdmin, etc.).

4. Select the installation directory (by default, it's `C:\xampp`).

5. Complete the installation process.

  

### 2. Cloning Your PHP Website to htdocs Folder

  

1. Open File Explorer and navigate to the XAMPP installation directory (by default, it's `C:\xampp`).

2. Inside the XAMPP directory, locate the `htdocs` folder.

3. Clone your PHP website repository into the `htdocs` folder.

  

```bash

git  clone  https://github.com/christanplaza/design-1c-CMS.git

```

  

### 3. Creating the Database

  

1. Launch XAMPP Control Panel.

2. Start the Apache and MySQL services by clicking on the "Start" buttons next to them.

3. Open your web browser and go to `http://localhost/phpmyadmin`.

4. Log in to phpMyAdmin (the default credentials are usually `root` for username and no password).

5. Click on the "Databases" tab.

6. Enter a name for your database and click on the "Create" button.

7. Now, select the newly created database from the left sidebar.

8. Click on the "Import" tab.

9. Click on the "Choose File" button and select the SQL file from the root folder of your PHP website repository. `xampp/htdocs/design-1c-cms/design-1c-cms.sql`

10. Click on the "Go" button to import the SQL file and create the database tables.

  

### 4. Configuring Your PHP Website

  

1. Create a file `config/config.php`

2. Check the config.php file sent in Messenger

  

### 5. Accessing Your PHP Website

  

1. Open your web browser.

2. Enter `http://localhost/design-1c-CMS/login.php` in the address bar.

3. Your PHP website should now be up and running locally on XAMPP.