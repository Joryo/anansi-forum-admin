A web panel for [Anansi Forum API](https://github.com/Joryo/anansi-forum-api) administrator, run on [Lumen](https://lumen.laravel.com/).

## Installation

### Needs
This panel work with [Anansi Forum API](https://github.com/Joryo/anansi-forum-api).

### Configuration
Edit the '.env.template' file with your personnal configuration before running the app and rename it '.env'.

**Parameters**

* API_URI: The api endpoint to call. Write here the URL of your [Anansi Forum API](https://github.com/Joryo/anansi-forum-api) server.
* API_TIMEOUT: Max waiting time for an API request.

### Serve the admin panel

Make sure you have [PHP](https://nodejs.org/) and a http server like [Apache](https://httpd.apache.org/) or [Nginx](https://www.nginx.com/) installed on your server.

1. Copy the source code on your root http server directory

  ```
  $ git clone https://github.com/Joryo/anansi-forum-adminpanel.git
  ```

2. Edit configuration file ```.env.template``` with your config (see [Configuration](#configuration)) and rename the file to ```.env```