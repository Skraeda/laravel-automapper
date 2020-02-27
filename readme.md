# Laravel AutoMapper
This package integrates [AutoMapper+](https://github.com/mark-gerarts/automapper-plus) by Mark Gerarts into Laravel.

This documentation will assume you are familiar with how `AutoMapper+` works.

## Installation
Install via composer:

```
composer require skraeda/laravel-automapper
```

## AutoMapping
For this example, we want to be able to easily map between `Employee` and `EmployeeDto` models.

### Example models
```php
namespace App\Models;

class Employee
{
    protected $firstName;
    protected $lastName;
    protected $birthYear;

    public function __construct($firstName = null, $lastName = null, $birthYear = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthYear = $birthYear;
    }

    public function getFirstName() { return $this->firstName; }
    public function getLastName() { return $this->lastName; }
    public function getBirthYear() { return $this->birthYear; }
}

class EmployeeDto
{
    protected $firstName;
    protected $lastName;
    protected $birthYear;
}
```

### Registering the mappings
We can register the mappings in our `boot` method in any `ServiceProvider`

```php
namespace App\Providers;

use App\Models\Employee;
use App\Models\EmployeeDto;
use Illuminate\Support\ServiceProvider;
use AutoMapper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register AutoMapper mappings.
     *
     * @see https://github.com/mark-gerarts/automapper-plus#registering-mappings
     * @return void
     */
    public function boot()
    {
        AutoMapper::getConfiguration()
                  ->registerMapping(Employee::class, EmployeeDto::class)
                  ->reverseMap();
    }
}
```

### Mapping between the models
Using the facade or alias
```php
// use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade as AutoMapper
use AutoMapper;

$dto = AutoMapper::map(new Employee("John", "Doe", 1980), EmployeeDto::class);
$john = AutoMapper::map($dto, Employee::class);
```

Using the contract
```php
use Skraeda\AutoMapper\Contracts\AutoMapperContract;

$mapper = App::make(AutoMapperContract::class);
$dto = $mapper->map(new Employee("John", "Doe", 1980), EmployeeDto::class);
$john = $mapper->map($dto, Employee::class);
```

Using the helper functions
```php
$dto = auto_map(new Employee("John", "Doe", 1980), EmployeeDto::class);
$john = auto_map($dto, Employee::class);
```

## Custom Mappers
`AutoMapper+` allows us to define separate classes to perform the mapping if it requires more complicated logic or if we need better performance.

Let's change `EmployeeDto` to look like this instead

```php
class EmployeeDto
{
    protected $fullName;
    protected $age;

    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function setAge($age) { $this->age = $age; }
}
```
To teach the `AutoMapper+` how to use a custom mapper to map to the new `EmployeeDto` model then delete the old mapping and create a new custom mapper.

```php
namespace App\Mappings;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Illuminate\Support\Carbon;

class EmployeeToDtoMapper extends CustomMapper
{
    /**
     * Map properties of source to destination.
     *
     * @param array|object $source
     * @param object $destination
     * @param array $context
     * @return mixed
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $destination->setFullName(sprintf("%s %s", $source->getFirstName(), $source->getLastName()));
        $destination->setAge(Carbon::now()->subYears($source->getBirthYear())->year);
        return $destination;
    }
}
```

You can manually register this custom mapping in your `ServiceProvider` or you can use a `mapping.php` config file.

Publish the config file

```
php artisan vendor:publish --provider=Skraeda\\AutoMapper\\Providers\\AutoMapperServiceProvider
```

Register custom mappers

```php
'custom' => [
    \App\Mappings\EmployeeToDtoMapper::class => [
        'source' => \App\Models\Employee::class,
        'target' => \App\Models\EmployeeDto::class
    ]
]
```

## Helpers and methods
### Collection macro
You can use the `autoMap` method on a collection to map into some target class.

```php
use Illuminate\Support\Collection;

$employees = Collection::make([new Employee("John", "Doe", 1980), new Employee("Jane", "Doe", 1985)]);
$dtos = $employees->autoMap(EmployeeDto::class);
```

### Generator command
You can use the `make:mapper` artisan command to generate the boilercode for a custom mapper.

```
php artisan make:mapper EmployeeToDtoMapper
```

### AutoMapperContract
```php
public function map($source, string $targetClass, array $context = []);
public function mapToObject($source, $target, array $context = []);
public function mapMultiple($collection, string $targetClass, array $context = []): \Illuminate\Support\Collection;
public function getConfiguration(): \AutoMapperPlus\Configuration\AutoMapperConfigInterface;
```

### AutoMapperFacade
```php
/**
 * @method static mixed map($source, string $targetClass, array $context)
 * @method static mixed mapToObject($source, $target, array $context)
 * @method static \Illuminate\Support\Collection mapMultiple($collection, string $targetClass, array $context)
 * @method static \AutoMapperPlus\Configuration\AutoMapperConfigInterface getConfiguration()
 */
```

### Helper functions
```php
function auto_map($source, string $targetClass, array $context = []);
function auto_map_to_object($source, $target, array $context = []);
function auto_map_multiple($collection, string $targetClass, array $context = []): \Illuminate\Support\Collection;
```

## Testing
### Run
```
vendor/bin/phpunit
```

Alternatively

```
composer test
```

### Generate coverage
```
vendor/bin/phpunit --coverage-html=coverage --log-junit coverage/report.xml
```

Alternatively

```
composer test:coverage
```

### Run static analysis
```
vendor/bin/phpstan analyse -l 1 src tests
```

Alternatively

```
composer stan
```
