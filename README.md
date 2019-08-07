## Quick dev-env deployment guide
Copy `./env.dev` into './env'.
Asuming your env is compatible with the Laravel version mentioned in the composer.json:
```
composer install
npm install
```
If you encounter any problems, you're likely missing some PHP modules required by Laravel

### Start the API dev-server with
```
php artisan serve
```

### Start the PubSub server with
```
php artisan BankWire

```


## Checking the pub-sub
Asuming you have a non-protected running redis server on your dev machine (this could be adjusted 
to use other drivers):

####Subscribe with:
```
redis-cli subscribe BankWire:businessDates 
```

####Request information with:
```
redis-cli subscribe BankWire:businessDates '{"initialDate": "2018-12-12T10:10:10Z", "delay": 5}'
```

## HTTP API server
For this development setup, send a GET or POST request to:
```http://127.0.0.1:8000/api/v1/businessDates/```
with a body like this:
```
{
  "initialDate": "2018-11-29T10:10:10Z",
  "delay": 10
}
```
##  Testing
From the application root folder, run vendor/bin/phpunit
