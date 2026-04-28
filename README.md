# slash-dw/core-kit

A shared Laravel core utilities package.

## Requirements

- PHP `^8.5`
- Laravel `^13.0`

Exact Laravel component constraints are defined in `composer.json`.

## What This Package Provides

### HTTP
- `ApiResponseFactory`
- `DownloadResponseFactory`
- `TraceIdResolver`
- `PaginationOptionsProvider`

### Logging
- `LogContextBuilder`
- `ExceptionLogger`
- `ThrowSiteCapture`

### Cache and Persistence
- `CacheInvalidator`
- `TaggedCacheInvalidator`
- `AbstractEloquentRepository`
- `EloquentQueryFilterContract`

### Enum Traits
- `BaseEnumTrait`
- `HasColorTrait`
- `HasSortOrderTrait`

### Controller Traits
- `ApiResponses`
- `HandlesDownloadResponses`
- `HasPaginationOptions`

## Trait Usage Example

```php
use SlashDw\CoreKit\Enums\Concerns\BaseEnumTrait;
use SlashDw\CoreKit\Enums\Concerns\HasColorTrait;

enum Status: int
{
    use BaseEnumTrait;
    use HasColorTrait;

    case Draft = 1;

    public function label(): string
    {
        return 'Draft';
    }
}
```

## Test Status

- PHPUnit: 47 tests / 171 assertions
- PHPStan: clean
- Pint: passed

## Dev Commands

```bash
composer install
./vendor/bin/phpunit -c phpunit.xml.dist
./vendor/bin/phpstan analyse -c phpstan.neon.dist --memory-limit=1G
./vendor/bin/pint --format agent
```
