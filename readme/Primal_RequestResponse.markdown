#Primal Request/Response

Created and Copyright 2012 by Jarvis Badgley, chiper at chipersoft dot com.

Request and Response are two helper classes for PHP 5.3 that wrap other various elements of PHP to provide a centralized and organized access point for tasks related to incoming request data and outgoing response data.  This repo also includes the HTTPStatus class, a collection of constants representing all of the HTTP status codes.

[Primal](http://www.primalphp.com) is a collection of independent micro-libraries that collectively form a [LAMP](http://en.wikipedia.org/wiki/LAMP_\(software_bundle\)) development framework.


##Request

The `Request` object's primary task is providing a single unified collection of data received in the request.  It adds support to PHP for JSON request body data, and access to PUT request data.  Request extends the ArrayObject class, allowing you to access this data directly on the Request object as an array:

```php
$req = new Request();
$username = $req['username'];
$password = $req['password'];
```

Additional member properties are provided on the object for getting other request information.

- `data`: Contains the entire contents of the request body, as an array.
- `contenttype`: Request body content type.
- `method`: HTTP request method (GET, POST, PUT, DELETE)
- `ip`: IPv4 address of the requesting device
- `uri` or `url`: The original request URL.
- `domain`: Domain name portion of the request URL.
- `path`: File path portion of the request URL
- `query`: GET request query
- `referer` or `referrer`: The referring page URL
- `secure`: Boolean indicating if the request is being handled over SSL.
- `browser`: Short string indicating the name of the web browser being used.  Possible values are:
    - chrome
    - firefox
    - ie6
    - ie7
    - ie8
    - ie9
    - ie10
    - safari
    - opera	
- `mobile`: Boolean identifying if the request was made from a mobile device.
- `useragent` or `agent`: Requesting HTTP User-Agent.

Member functions:

- `header($name, [$default])`: Returns the header matching the name supplied, or returns the default value if the header does not exist.
- `cookie($name, [$default])`: Returns the browser cookie matching the name supplied, or the supplied default if the cookie does not exist.

##Response

The `Response` object provides the following member functions:

- `secure()` : If the request was not performed over SSL, redirects the page to the same URL on HTTPS.
- `header($name, $value)`: Sets the defined HTTP header.
- `cookie($key, $value)`: Sets the defined browser cookie. Defaults to one year expiry on the root domain.
- `unsetCookie($key)`: Removes the defined browser cookie.
- `noCache()`: Tells the browser and any proxies to never cache the response data.
- `statusCode($code)`: Sets the HTTP response code. See the HTTPStatus class for all available codes.
- `json($object, [$callback])`: Sends the passed data as a JSON encoded object with the `application/json` content type.  If `$callback` is defined, the data will be wrapped in a JSONp call back function name.
- `redirect([$url,][$code])`: Tells the browser to redirect the page to a different URL.  Default is a 302 redirect to the current page.


##Legal

All Primal libraries are released under an MIT license.  No attribution is required and you are expected to make alterations.  For details see the enclosed LICENSE file.

