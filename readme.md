# LVotS
LineBot Vote System.

# Required Service.
* PHP 7.0
* MySQL 5.6 
* Redis

# install Guide.
1. git clone
```
$ git clone https://github.com/clom/LVotS.git
```

2. install Environment
```
$ php composer.phar install 

or

$ composer install
```

```
$ cp .env.example .env
$ php artisan key:generate
```

Please Fix Your Environment `.env`

3. migration
```
$ php artisan migrate
```

4. Launch Standalone
```
$ php artisan serve
```

# License
This System is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
