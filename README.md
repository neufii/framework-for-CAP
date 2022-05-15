# Framework for Computerized Adaptive Practice with Question Generator Assessment
### Installation
1. Clone the repository
2. Create a database and set the `.env` file in the project folder
3. Run the following command in cmd/terminal 
``` 
composer install
php artisan key:generate
php artisan cache:clear
php artisan migrate:fresh
```
### Demo
To run the example client application
1. Run `php artisan db:seed --class=DemoSeeder` in the project folder
2. Run `php artisan serve --port=8080` in the project folder
3. Clone the repository from https://github.com/neufii/framework-for-CAP-Demo
4. Run `npm install` in the client project folder
5. Run `npm start` in the client project folder
6. Visit `localhost:3000` for the example client application

### Making Change
To change the code of each module (such as a question instance generator) simply edit the files under `App\Modules\Repositories`.
