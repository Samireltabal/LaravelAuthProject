# Change Log

## V 0.1.1 

* Append role in user model
* Add Route to verify Authority
* Documentation Initializing
* Added Media Library / Avatars
* Added Admin Controller / Admin Routes File
* Telescope For Debugging 

## V 0.1.0 ( Initial Release ) 
- 1 nov 2021
* Authentication / Registration / Roles Management
* OTP CODE On Registration 

# About

## Steps to get up and running 
* git clone GIT_URL
* composer install
* setup database / mail vars in .env file 
* php artisan migrate --seed
* php artisan passport:install

## Usage 
* admin login to /api/auth/login using : admin@example.com / password
* user  login to /api/auth/login using :  user@example.com / password

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
