## JustFans - Paid creator social media platform

#### About


Product description..

It is mainly based on following techs, plus couple of other dependencies and tools to ease up your development.

- Laravel 6
- Bootstrap 4.6.0
- jQuery

####  Main features

- Clean, easy to understand structure
- Easy debugging (F5 + DebugBar)
- CSS,JS,HTML Minification and merger
- Good SEO practices, Schema.org integration 
- Mobile compatible, Flex based layout 
- Cookie consent
- Login system included
- Easy libraries management via NPM & Composer
- Dark mode & RTL & Easy theme customization via SCSS vars
- Localization 
- [Next] Admin

#### Requirements

* PHP 7.3
* Mysql / MariaDB
* Apache & mod_rewrite / Nginx
* Node, Composer & at least 2GB of RAM for dev builds

#### Install

````
1) Create db
2) cp .env.sample .env # Edit values, add db
3) composer install
4) php artisan npm:install
6) npm run prod
7) php artisan key:generate
8) php artisan migrate
9) php artisan db:seed
10) php artisan voyager:admin your@email.com # To add new admin user
````
_Note*_ If having issues with composer install, try `php -d memory_limit=1G /usr/bin/composer install`


#### Saving admin state via seeds

_Saving admin panel state. This will remove all prior admin related seeds and reverse genererate new ones - so default admin state & settings will persist._

````
php artisan admin:save
````

_Publishing frontend libraries to public directory. Eg: You npm add a new lib and need to include it  into your views._
```
php artisan npm:publish
```


_Running Code quality checkers and fixers_

````
php artisan code:check type=php/js
php artisan code:fix type=php/js
````

_Setting up the crons_

````
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
````

_Setting up payments_

For stripe:
1. In admin, add stripe public and secret key
2. In stripe, create a webhook ( all events )
3. Get Stripe's webhook secret and add it back to admin

For PP:

1. Login to PP Dev dashboard, create new app
2. Get Paypal's ClientID & Paypal Secret key and add them into the admin


### Ionicons usage

Icons ( Ionicons )
Backend

````
 @ include('elements.icon',['icon'=>'chevron-heart'])
 @ include('elements.icon',['icon'=>'chevron-heart-outline'])
 @ include('elements.icon',['icon'=>'chevron-heart-outline','variant'=>'medium])
 @ include('elements.icon',['icon'=>'chevron-heart-outline','variant'=>'medium','centered'=>'true'])
````
            
Frontend
````
< ion-icon name="heart"> 
< ion-icon name="heart-outline"> 
< ion-icon name="heart-sharp"> 
< ion-icon size="small">
< ion-icon size="large">
````
            
### Translations

Backend
````
trans_choice('We got coconut.',2,['number'=>2])
We got coconut.

trans_choice('We got coconut.',1,['number'=>1])
We got coconut.

__('English is nice')
English is nice

__('Food is good',['food'=>'cacao cu lapte'])
Food is good
````

Frontend
````
trans_choice('We got 1 coconut.',2,{'number':2})
We got 1 coconut.

trans_choice('We got 1 coconut.',1,{'number':1})
We got 1 coconut.

trans('English is nice')
English is nice

trans('Food is good',{'food':'cacao cu lapte']})
Food is good
````
#### Benchmarks & Performance
 
Tested on a dual core, $10 Digital ocean droplet, running nginx wiht php-fpm and PHP74, which tends to throttle CPU usage we got the following results:

- Avg Max concurent request: ~240rps
- Avg Load time: ~0.5s
- Total bundle overhead (Gzipped): ~241KB

_Wrk Benchmark tool sample_
![alt text](https://i.imgur.com/gZ3o7eP.png)

_Google Lighthouse/Page Insights report sample_
![alt text](https://i.imgur.com/mFXY8Zb.png)

#### Questions?

Send us a message over http://qdev.tech .
