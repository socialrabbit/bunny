# Common Installation Issues

## Provider Not Found Error

If you encounter the following error when installing Bunny:

```bash
Class "socialrabbit\Bunny\Providers\BunnyServiceProvider" not found
```

### Solution

This error occurs due to incorrect namespace configuration. Here's how to fix it:

1. First, remove the package:
```bash
composer remove socialrabbit/bunny
```

2. Clear composer's cache:
```bash
composer clear-cache
```

3. Clear Laravel's cache:
```bash
php artisan cache:clear
php artisan config:clear
```

4. Install the package with the correct version:
```bash
composer require socialrabbit/bunny:^1.0
```

5. If the error persists, check your `composer.json` file has the correct PSR-4 autoload configuration:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Bunny\\": "vendor/socialrabbit/bunny/src/"
    }
}
```

6. Run composer's autoload:
```bash
composer dump-autoload
```

### Environment Requirements

Make sure your environment meets these requirements:
- PHP >= 8.1
- Laravel >= 10.0
- Composer 2.x

### Still Having Issues?

If you're still experiencing problems:
1. Create an issue on our [GitHub Issues](https://github.com/socialrabbit/bunny/issues) page
2. Include your:
   - Laravel version
   - PHP version
   - Composer version
   - Full error message
   - Steps to reproduce

### Need Help?

- 📖 [Read the documentation](https://github.com/socialrabbit/bunny/tree/main/docs)
- 📧 [Email support](mailto:iamsocialrabbit@gmail.com)
