<div align="center">
  <h3>ViaRouter</h3>

  <p align="center">A complete, modern and efficient router.</p>

  [![Latest Stable Version](http://poser.pugx.org/ystrion/viarouter/v)](https://packagist.org/packages/ystrion/viarouter)
  [![Latest Unstable Version](http://poser.pugx.org/ystrion/viarouter/v/unstable)](https://packagist.org/packages/ystrion/viarouter)
  [![License](http://poser.pugx.org/ystrion/viarouter/license)](https://packagist.org/packages/ystrion/viarouter)
</div>

## Getting Started

### Prerequisites

- PHP (>= 8.2)
- Composer
- [PSR-7](https://www.php-fig.org/psr/psr-7/) implementation
- PSR-15 dispatcher (optional)

### Installation

This package can be installed with Composer using this command:

```sh
composer require ystrion/viarouter
```

## Usage

### Adding routes

#### Classic route

```php
use Ystrion\ViaRouter\Routes;

$routes = new Routes();

$routes->add('homepage', '/', function ($request, $response, $args) {
  $response->getBody()->write('Hello World!');

  return $response;
});
```

Each classic route has at least three required parameters:

- A name (here `homepage`).
- A path (here `/`).
- A handler (here an `anonymous function`).

#### Attribute route

```php
use Ystrion\ViaRouter\Attributes\Route;

class HomepageController
{
  #[Route('homepage', '/')]
  public static function homepage($request, $response, $args)
  {
    $response->getBody()->write('Hello World!');

    return $response;
  }
}
```

```php
use Ystrion\ViaRouter\Routes;

$routes = new Routes();

$routes->addController(HomepageController::class);
```

Each attribute route has at least two required parameters:

- A name (here `homepage`).
- A path (here `/`).

#### List of rules

##### Name

The `name` must be a unique string.

##### Path

The `path` can be static (as it is above) or dynamic. A dynamic path contains arguments (variable portions).

An argument is written by placing an identifier between two braces. Example: `/users/{id}/{username}`

##### Defaults

The `defaults` rule imposes default values when some arguments are missing.

<details>
 <summary>Example with classic route</summary>

```php
$routes->add('user', '/users/{id}/{username}', function ($request, $response, $args) {
  $response->getBody()->write('Hello World!');

  return $response;
})->defaults([
  'id' => '1',
  'username' => 'Ystrion'
]);
```
</details>

<details>
 <summary>Example with attribute route</summary>

```php
#[Route('user', '/users/{id}/{username}', defaults: [
  'id' => '1',
  'username' => 'Ystrion'
])]
public static function user($request, $response, $args)
{
  $response->getBody()->write('Hello World!');

  return $response;
}
```
</details>

##### Constraints

The `constraints` rule imposes regular expression on certain arguments.

<details>
 <summary>Example with classic route</summary>

```php
$routes->add('user', '/users/{id}/{username}', function ($request, $response, $args) {
  $response->getBody()->write('Hello World!');

  return $response;
})->constraints([
  'id' => '[0-9]+',
  'username' => '[a-zA-Z]+'
]);
```
</details>

<details>
 <summary>Example with attribute route</summary>

```php
#[Route('user', '/users/{id}/{username}', constraints: [
  'id' => '[0-9]+',
  'username' => '[a-zA-Z]+'
])]
public static function user($request, $response, $args)
{
  $response->getBody()->write('Hello World!');

  return $response;
}
```
</details>

##### Host

The `host` rule enforces access by certain domains.

<details>
 <summary>Example with classic route</summary>

```php
$routes->add('homepage', '/', function ($request, $response, $args) {
  $response->getBody()->write('Hello World!');

  return $response;
})->host('example.com');
```
</details>

<details>
 <summary>Example with attribute route</summary>

```php
#[Route('homepage', '/', host: 'example.com')]
public static function homepage($request, $response, $args)
{
  $response->getBody()->write('Hello World!');

  return $response;
}
```
</details>

### Matching a request to a route

```php
use Ystrion\ViaRouter\Routes;
use Ystrion\ViaRouter\ViaRouter;

$routes = new Routes();
$router = new ViaRouter($routes);

// All your routes are defined here.

$route = $router->match($request);

if ($route === false) {
  // Not Found.
} else {
  // Found.
  // You can now use the Route object.
}
```

### Using ViaRouter as middleware

Autowiring makes it possible to inject objects (listed in the container) directly into the method parameters.

To use autowiring, you must use [PHP-DI](https://php-di.org/).

#### Without autowiring

```php
use Ystrion\ViaRouter\Routes;
use Ystrion\ViaRouter\ViaRouter;

$routes = new Routes();

// All your routes are defined here.

$response = (new MiddlewareDispatcher([
  new ViaRouter($routes)
]))->handle($request);
```

#### With autowiring

```php
use DI\Container;
use Ystrion\ViaRouter\Routes;
use Ystrion\ViaRouter\ViaRouter;

$routes = new Routes();
$container = new Container();

// All your routes are defined here.

$response = (new MiddlewareDispatcher([
  new ViaRouter($routes, $container)
]))->handle($request);
```

## License

This package is distributed under the [MIT license](https://github.com/Ystrion/ViaRouter/blob/main/LICENSE).

## Contact

- To report a security or related issue: [GitHub Security](https://github.com/Ystrion/ViaRouter/security)
- To report a problem or post a feature idea: [GitHub Issues](https://github.com/Ystrion/ViaRouter/issues)
- If you encounter any problem while installing or using: [GitHub Discussions](https://github.com/Ystrion/ViaRouter/discussions)
- For any other reason: [ystrion@deville.dev](mailto:ystrion@deville.dev)
