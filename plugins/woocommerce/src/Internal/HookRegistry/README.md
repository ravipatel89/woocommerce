## Callback Registration


### A Listener that implements HandleActions or HandleFilters
```php
return [
    'action_name' => [
        AListener::class
    ]
];
```

### Global Function
```php
return [
    'action_name' => [
        '__return_false'
    ]
];
```

### Class + Public Method


| Key      |                            Description | Required | Default |
|----------|---------------------------------------| ----------|---------|
| array[0] |      Class name that contains array[1] | Yes | N/A |
| array[1] |          method name in array[0] class | Yes | N/A|
| array[2] |                               priority | No| 10|
| array[3] |                          accepted_args | No| 1|

```php
return [
    'action_name' => [
        [AListener::class, 'method', 10, 1]
    ]
];
```
