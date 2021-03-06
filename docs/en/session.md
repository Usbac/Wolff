`Wolff\Core\Session`

Instead of managing the `$_SESSION` variable directly, you can use the Wolff Session class.

It's safer since it's tied to the client's IP address and its user agent, meaning that if the client has a different IP or a different user agent, the session will be destroyed.

## General methods

_All the session methods works with the time expressed in minutes (unless it’s specified other way)._

### Start session

`start(): void`

Starts the session.

```php
Session::start();
```

### Add session time

`addTime(int $time): void`

Adds time to the session.

```php
Session::addTime(10);
```

### Set session time

`setTime(int $time): void`

Sets the session global time.

```php
Session::setTime(10);
```

### Get remaining time

`getRemainingTime([bool $gmdate]): mixed`

Returns the session remaining time (in seconds).

```php
Session::getRemainingTime();
```

If a `string` is given as parameter, it will return the remaining time in the given date format.

```php
Session::getRemainingTime('H:m:s');
```

### Empty

`empty(): void`

Removes the session data.

```php
Session::empty();
```

### Destroy

`kill(): void`

Destroys the session.

```php
Session::kill();
```

## Variable methods

### Set

`set(string $key, $value[, int $time]): void`

Sets a session variable.

```php
Session::set('name', $value);
```

### Get

`get(string $key): mixed`

Returns a session variable.

```php
Session::get('name');
```

### Has

`has(string $key): bool`

Returns `true` if a session variable exists, `false` otherwise.

```php
Session::has('name');
```

### Unset

`unset(string $key): void`

Unsets a session variable.

```php
Session::unset('name');
```

### Get variable time

`getVarTime(string $key[, bool $gmdate]): mixed`

Returns the variable time (in seconds).

```php
Session::getVarTime('name');
```

If a `string` is given as the second parameter, it will return the time in the given date format.

```php
Session::getVarTime('name', 'H:m:s');
```

### Set variable time

`setVarTime(string $key, int $time): void`

Sets the variable live time.

```php
Session::setVarTime('name', 10);
```

### Add time to variable

`addVarTime(string $key, int $time): void`

Adds time to a variable.

```php
Session::addVarTime('name', 10);
```
