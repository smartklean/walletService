# Service Docker Scaffold

### **Description**

This will create a dockerized stack for our service, made up of one container that houses the images (Nginx, PHP7.4 PHP7.4-fpm, Composer, NPM, Node.js v10.x) needed to run our Lumen application.

### **Directory Structure**
```
+-- .git
+-- resources
|   +-- default
|   +-- nginx.conf
|   +-- supervisord.conf
|   +-- www.conf
+-- src <project root>
+-- .dockerignore
+-- .gitignore
+-- .bitbucket-pipelines.yml
+-- docker-compose.yml
+-- docker-ssh-setup.sh
+-- Dockerfile
+-- Dockerfile.staging
+-- migrate-db.sh
+-- readme.md <this file>
```

### **Prerequisites**

Depending on your OS, the appropriate version of Docker Community Edition has to be installed on your machine.  ([Download Docker Community Edition](https://hub.docker.com/search/?type=edition&offering=community))

### **How To Install**

1. Create a new directory in which your OS user has full read/write access and clone this repository inside. Clone using your [bitbucket app password](https://support.atlassian.com/bitbucket-cloud/docs/app-passwords/) i.e `git clone https://username:apppassword@bitbucket.org/cashenvoy-engineering/service-scaffold.git`. Replace **username** with your bitbucket username and **apppassword** with your bitbucket app password.

2. Open a new terminal/CMD, navigate to this repository root (where `docker-compose.yml` exists).

3. Update the remote upstream to point to your repository using: `git remote set-url origin [repo_url]`. Replace `[repo_url]` with your repository url.

4. In your `docker-compose.yml` update the container name by editing the value of services.app.container_name. By default this is set to **ServiceScaffold**. Also, by default, port 80 in your docker container is mapped to port 8000 on your local machine in the `docker-compose.yml`; if you already have some service running on port 8000 you should map port 80 to a different port before you proceed.

5. Execute the following command:

    ```
    $ docker-compose up -d
    ```

    This will download/build all the required images and start the stack containers. It usually takes a bit of time, so grab a cup of coffee.

6. After the whole stack is up, ssh into the app container by running the following command:

    ```
    $ docker exec -it [app_container_name] bash
    ```
    Replace `[app_container_name]` with the name of your app container. If successful, the command would open up an interactive shell in the /var/www/html folder.

7. In that shell, run `$ composer update` to install all your Lumen dependencies.

8. Copy .env.example to .env:

    ```
    $ cp .env.example .env
    ```

9. In the `.env` file, you need to assign a value to **LUMENWS_APP_KEY**. Laravel allows you to easily generate an app key with `php artisan key:generate` command but Lumen being extremely light weight doesn't come with this and many other artisan commands. Make a GET request to [http://localhost:8000](http://localhost:8000) either on your web browser or Postman to get a valid app key. Assign this key to the **APP_KEY** variable in the `.env` file.

    **N.B:** This assumes port 80 is mapped to port 8000 in the `docker-compose.yml` file. If you have mapped port 80 to a different port say 6000, your app would run on [http://localhost:6000](http://localhost:6000).

    You can edit your `.env` file from the terminal using commands like `vim` or `nano` or from a text editor.

10. Ensure that you have a MySQL server instance running on your local machine. Enter the MySQL connection parameters into your `.env` file. The **LUMENWS_DB_HOST** variable should be set to **host.docker.internal**.

    **N.B:** The easiest way to do install and run a MYSQL server on your macOS is using Homebrew. To learn how to install Homebrew on your macOS, [click here](https://brew.sh/). To learn how to install and run a MySQL server on your macOS using Homebrew [click here](https://flaviocopes.com/mysql-how-to-install/). After successfully installing your MySQL server you may choose to download an administration tool such as [MySQL Workbench](https://www.mysql.com/products/workbench/) or [phpMyAdmin](https://www.phpmyadmin.net/).

11. In your terminal, run `php artisan migrate --seed` to migrate existing tables to your database.

12. Update the **LUMENWS_** prefix in the `.env.example`, `.env` and `phpunit.xml` files to one unique to your app (it should follow the naming convention **SERVICEWS_**). Be sure to also update this in the `app.php`, `database.php` and `queue.php` files in your config folder and to add this prefix to any new environment variables you create in your app.

13. That's it, you're all setup. Now build an awesome service!

### **How To Use Migrations**

**TL/DR:** Once a migration is created and run, it is immutable. Any modifications to an existing Schema should be made via a new migration.

Migrations should be used in an iterative manner, in order words once a migration for a new Schema is created, any modifications to the Schema **should be made via a new migration**. Consider the following example:

We create a new migration that holds the Schema for a users table:

   ```
   public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->dateTime('password_updated_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('users');
    }
   ```

 After running the above migration, we want to modify the Schema by adding two new columns **birthday** and **active**, so we create and run a new migration while the above remains untouched:

    ```
    public function up()
    {
       Schema::table('users', function (Blueprint $table) {
           $table->date('birthday')->nullable();
           $table->boolean('active')->default(0);
       });
    }
    ```

### **Naming Convention For Routes**

Routes should be named following this convention: **api/vX/service_name/[routes]**. The only exception is the **health check route**.

### **Naming Convention For Branches**

Branches should be named following either one of these conventions: **feature/branch_name**, **bugfix/branch_name** or **hotfix/branch_name**.
