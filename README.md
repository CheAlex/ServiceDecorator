# ServiceDecorator

```php
class CreateUserService
{
    #[Transactional]
    #[Flushable]
    public function execute(CreateUserRequest $request): CreateUserResponse
    {
        echo 'полезная нагрузка;';

        return CreateUserResponse::success();
    }

    #[Transactional]
    public function handle(CreateUserRequest $request): CreateUserResponse
    {
        echo 'полезная нагрузка;';

        return CreateUserResponse::success();
    }
}
```

`transaction:start;flush:start;полезная нагрузка;flush:stop;transaction:stop;`
