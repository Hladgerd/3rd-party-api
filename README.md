# 3rd Party API Integration via Laravel

## Getting started
### Installation
Please check the official Laravel installation guide for server requirements before you start: [Official Documentation](https://laravel.com/docs/10.x)

Clone the repository, switch to the repo folder and install all the dependencies using composer:
```
git clone git@github.com:Hladgerd/maileon-api.git
cd maileon-api
composer install
```

Copy the .env.example file:
```
cp .env.example .env
```

Add your API key to the .env file:
```
API_KEY="<your-API-key>"
```
No need for database connection and migration.
Start the local development server
```
php artisan serve
```
You can now access the server at http://localhost:8000

**TL;DR command list**
```
git clone git@github.com:Hladgerd/maileon-api.git
cd maileon-api
composer install
cp .env.example .env
API_KEY="<your-API-key>"
php artisan serve
```

### Folders
* `app/Exceptions` - Contains the custom exceptions' rendering
* `app/Http/Controllers/APIControllers` - Contains the API controller
* `app/Http/Requests/APIRequests` - Contains the form request validation
* `app/Providers` - Contains the route parameter validation in RouteServiceProvider.php
* `config` - Contains all the application configuration files
* `routes` - Contains all the api routes defined in api.php file
* `tests/Feature/API` - Contains all the api tests


## API Documentation
### Overview
This project's JSON-based API is organised around REST and integrate [Maileon Rest API v1.0](https://support.maileon.com/support/rest-api-1-0/)  
All requests are made to the developer endpoint beginning:  
http://localhost
### Authentication
An API-Key is used to authenticate the application for authorized access to the Maileon API.  
All launched API requests are made over HTTPS
### Resources
**Contacts**

 | Name                      | Method | Endpoint                      |
|---------------------------|--------|-------------------------------|
| Get Contact By Email      | `GET`  | [/get-contact/:email](https:) |  
| Create Contact With Email | `POST` | [/create-contact](https:)     |

Requests and responses are in JSON format.

### Errors
This application uses conventional HTTP response codes to indicate the success or failure of an API request.  
Specifically:
* Codes **200** and **201** indicate success.  
* Code **404** indicates incorrectly typed API route, route parameter or non-existent contact.  
* Code **422** indicates an error that failed given the information provided (e.g., a required parameter was omitted, incorrect, etc.).  
* Code **500** indicates an error with Maileon's or this application's servers.

## Testing instructions
Open terminal in project's folder and run below command:
```
php artisan test
```
**Run tests with coverage**  
In order to run the tests with coverage, make sure that Xdebug's coverage mode has been set.
(Find instructions [here](https://dev.to/arielmejiadev/set-xdebug-coverage-mode-2d9g) how to do it)

Open the project in a terminal and run:
```
php artisan test --coverage
```


## Future Improvements
* Fix the error that contact is successfully created when payload was sent with duplicated required field (email).
* Fix mock tests so that the test absorbs the mocked response (instead of the real one) in order 
to test scenarios with Maileon API down
* Add authentication tests



