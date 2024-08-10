# PHP Coding Exercise for VT Electronic Research Administration (ERA) Web Application Developer position

This project serves as a template for a LAMP stack Web Application from which
Web Developer candidates can start.

This project template was created by following part of this tutorial:
[Docker's PHP Language Guide](https://docs.docker.com/language/php/).

### Getting started

To get started in development, make sure
[Docker Desktop](https://docs.docker.com/desktop/) is installed.

Then open a terminal in the root of this project and run:
```
docker compose up --build
# or `docker compose up` if you don't want to rebuild the docker image
```
And/or
```
docker compose watch
```

The latter, as mentioned from 
[the tutorial](https://docs.docker.com/language/php/develop/) should 
automatically update the docker services when you save your code changes. 
Documentation for the `watch` command can be found here:
https://docs.docker.com/compose/file-watch/.

When the stack starts, it should build and download all the necessary images
and automatically start containers for an Apache PHP server, MariaDB database, 
and phpMyAdmin. The Apache PHP server image is built using the 
[Dockerfile](./Dockerfile). 

Upon starting the stack, you should then be able to open a browser and go
to localhost on port 9000 to see a basic `Hello, world` message. (Note, there 
might be a short delay from when it seems like the stack is done spinning up
to when the server is accessible. So you might consider doing a few 
refreshes). 

- http://localhost:9000

As you make code changes, you may need to update your services to see the 
changes, particularly if you don't use the `watch` command. You can do this by
stopping the containers (`control-c`), and starting them again using one of 
the above commands. Also see the "Tear down" section below if you prefer to
stop or remove your Docker containers.

### phpMyAdmin

To view your database, you may take advantage of phpMyAdmin, which is running
as one of the docker containers. You can use the following url and
username/password to access it:
- http://localhost:8080  
- Username: root
- Password: admin

### Connecting to the database in the PHP backend

There are a few instructions you could consider in  
[src/database.php](src%2Fdatabase.php) for connecting to the DB using PHP 
code.

### Connecting to the National Weather Service (NWS) API

The [National Weather Service API](https://www.weather.gov/documentation/services-web-api) 
can be used to get weather data for your user-specified locations.  

The endpoint you can use to get weather information based on X,Y location 
coordinates is the 
[gridpoint forecast](https://www.weather.gov/documentation/services-web-api#/default/gridpoint_forecast).
The X,Y coordinates do not necessarily correspond to latitude, longitude, and
could be relative to the particular weather station used.

For example, Blacksburg is located at Lat: 37.22°N Lon: 80.42°W, Elev: 2133ft.  
The following endpoint shows more about this lat/lon point, 
https://api.weather.gov/points/37.22,-80.42. In the response of the point 
request, there's a forecast link for the X,Y gridpoint. For example:  
https://api.weather.gov/gridpoints/RNK/58,65/forecast

If you wish to use `curl` as your API client, the project should already be
configured such that you can use the [curl related functions](https://www.php.net/manual/en/book.curl.php).
Otherwise, you could install your favorite http client (see below "Installing
additional libraries" section).

**Note, according to the [API Web Service overview](https://www.weather.gov/documentation/services-web-api):**
> A User Agent is required to identify your application. This string can be anything, ... (the more unique the better)

**Therefore, make sure to set the `User-Agent` header in your request, otherwise 
you will get 403 responses.** E.g. `'User-Agent: era-php-coding-exercise`.

If you run out of time integrating the NWS API, or if the API is down for 
whatever reason (e.g. getting 502 responses), feel free to use the json data
provided in the [data](./data) directory as mock data for your application. 
The [blacksburg_va_weather_example.json](data%2Fblacksburg_va_weather_example.json)
contains example forecast data for Blacksburg, VA, which was retrieved from 
the following endpoint: https://api.weather.gov/gridpoints/RNK/58,65/forecast.

The `forecast` endpoint responds with a JSON object. At the top level of the 
returned JSON object, you can get the 'properties' object. From the 
'properties' object, get the array of 'periods'. The first element in the 
periods array (at index 0) is an object describing the current period. In the
current period, the text of the forecast can be found as 'detailedForecast'.

### Installing additional libraries

This is not required, but if you wish to install additional libraries for use 
in your PHP project, consider installing them via `apt-get` inside the 
[Dockerfile](Dockerfile). There are some example there. Make sure to rebuild
your docker image by stopping or removing the stack (see below) and starting
it again via `docker compose up --build`.

Alternatively, you may be able to use `composer` for dependency management. 
There's a [composer.json](./composer.json) in the project that gets mounted 
into the [Dockerfile](./Dockerfile) and used for install when building the 
image.

### Tear down

When you are done developing, you can tear down the stack by doing a 
"control-c" in the terminal followed by  `docker compose down` in the top-level 
directory of this project. Alternatively, you can use `docker ps -a` and 
perform `docker stop <##hash##>` and `docker rm <##hash##>` commands.  
