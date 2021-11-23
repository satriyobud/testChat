# MINI PROJECT 

Simple chat API with Lumen 8.

### Included Packages

- [flipbox/lumen-generator@^8.0](https://github.com/flipboxstudio/lumen-generator)
- [fruitcake/laravel-cors@^2.0](https://github.com/fruitcake/laravel-cors)
- [spatie/laravel-fractal@^5.8](https://github.com/spatie/laravel-fractal)
- [spatie/laravel-query-builder@^3.6](https://github.com/spatie/laravel-query-builder)
- [tymon/jwt-auth@^1.0](https://github.com/tymondesigns/jwt-auth)

### Installation

- Clone the Repo:
    - `git clone git@github.com:satriyobud/testChat.git`
    - `git clone https://github.com/satriyobud/testChat.git`
- `cd testChat`
- Install Packages.
    - `composer create-project`
    - `php artisan key:generate`
    - `php artisan jwt:secret`
    - `php artisan migrate`


#### Create new user


- `php artisan ti`
- `App\Models\User::factory()->create(['email' => 'admin@localtest.me', 'password' => 'password'])`

### Configuration

- Edit `.env` file for environment variables.


#### Authentication

- Create Bearer Token

```
curl --request POST 'http://127.0.0.1:8000/auth' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "email": "admin@localtest.me",
        "password": "password"
    }'
```

Example Bearer Token -

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXV0aCIsImlhdCI6MTYzNDI2MTQzNSwiZXhwIjoxNjM0MjY1MDM1LCJuYmYiOjE2MzQyNjE0MzUsImp0aSI6IlVzVm1PZk52dTBrOTZFYk4iLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.xjvzoFCkxlB_k2z0R0zkeatDDRU0hAbRFMETAEZBsss
```

Bearer Token need to passed in the request header as 

```
Authorization: Bearer <token>
```

- Get Current User

```
curl --request GET 'http://127.0.0.1:8000/auth' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXV0aCIsImlhdCI6MTYzNDI2MTQzNSwiZXhwIjoxNjM0MjY1MDM1LCJuYmYiOjE2MzQyNjE0MzUsImp0aSI6IlVzVm1PZk52dTBrOTZFYk4iLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.xjvzoFCkxlB_k2z0R0zkeatDDRU0hAbRFMETAEZBsss'
```

### Using CORS

Please check [fruitcake/laravel-cors](https://github.com/fruitcake/laravel-cors) in Github for the usage details.

### Postman
- collection `https://www.getpostman.com/collections/750e09d9a1cf66ac18fa`
- env `https://go.postman.co/workspace/mini-project~30aad92d-25fb-4f05-b20f-93d1f55d3014/environment/706467-d6d8d616-ef23-4993-a58b-da499207e01f`

### Issues

Please create an issue if you find any bug or error.

### Contribution

Feel free to make a pull request if you want to add anything.

### License

MIT
