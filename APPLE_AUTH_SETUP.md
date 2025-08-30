# Apple Authentication Feature Flag

This document explains how to control the visibility of Apple Sign In/Register buttons using the `APPLE_AUTH_ENABLED` environment variable.

## Overview

The Apple authentication feature can be enabled or disabled using an environment variable flag. By default, the Apple Sign In/Register buttons are **hidden** from the UI.

## Configuration

### Environment Variable

Add the following variable to your `.env` file:

```env
# Apple Authentication Feature Flag
# Set to true to enable Apple Sign In/Register buttons (default: false)
APPLE_AUTH_ENABLED=false
```

### Possible Values

- `true` - Enables Apple Sign In/Register buttons on login and registration pages
- `false` - Hides Apple Sign In/Register buttons (default behavior)
- Not set - Defaults to `false` (buttons hidden)

## Usage Examples

### To Enable Apple Authentication

1. Set the environment variable in your `.env` file:
   ```env
   APPLE_AUTH_ENABLED=true
   ```

2. Make sure you have configured your Apple OAuth credentials:
   ```env
   APPLE_CLIENT_ID=your_apple_client_id
   APPLE_CLIENT_SECRET=your_apple_client_secret
   APPLE_REDIRECT_URI=https://yourdomain.com/auth/apple/callback
   ```

3. Clear the config cache:
   ```bash
   php artisan config:clear
   ```

### To Disable Apple Authentication (Default)

1. Set the environment variable in your `.env` file:
   ```env
   APPLE_AUTH_ENABLED=false
   ```

2. Or simply remove the variable entirely (defaults to `false`)

3. Clear the config cache:
   ```bash
   php artisan config:clear
   ```

## What Changes When Disabled

When `APPLE_AUTH_ENABLED=false` (or not set):

- Apple Sign In button is hidden on the login page
- Apple Sign Up button is hidden on the registration page
- Apple OAuth routes (`/auth/apple` and `/auth/apple/callback`) are not registered
- No Apple authentication functionality is available

## What Changes When Enabled

When `APPLE_AUTH_ENABLED=true`:

- Apple Sign In button appears on the login page
- Apple Sign Up button appears on the registration page
- Apple OAuth routes are registered and functional
- Users can authenticate using their Apple ID

## Technical Implementation

The feature flag is implemented in several places:

1. **Configuration** (`config/services.php`):
   ```php
   'apple' => [
       'client_id' => env('APPLE_CLIENT_ID'),
       'client_secret' => env('APPLE_CLIENT_SECRET'),
       'redirect' => env('APPLE_REDIRECT_URI'),
       'enabled' => env('APPLE_AUTH_ENABLED', false),
   ],
   ```

2. **Routes** (`routes/auth.php`):
   ```php
   if (config('services.apple.enabled')) {
       Route::get('auth/apple', [SocialiteController::class, 'redirectToApple'])
           ->name('auth.apple');
       Route::get('auth/apple/callback', [SocialiteController::class, 'handleAppleCallback'])
           ->name('auth.apple.callback');
   }
   ```

3. **Views** (`resources/views/auth/login.blade.php` and `resources/views/auth/register.blade.php`):
   ```php
   @if(config('services.apple.enabled') && Route::has('auth.apple'))
   <!-- Apple Login Button -->
   <a href="{{ route('auth.apple') }}" ...>
       <!-- Button content -->
   </a>
   @endif
   ```

## Security Considerations

- When disabled, the Apple OAuth routes are not registered, providing additional security
- The feature flag prevents accidental exposure of incomplete Apple authentication setup
- Users cannot access Apple authentication endpoints when the feature is disabled

## Troubleshooting

If you're having issues:

1. **Buttons not showing when enabled:**
   - Verify `APPLE_AUTH_ENABLED=true` in your `.env` file
   - Run `php artisan config:clear` to clear config cache
   - Check that your Apple OAuth credentials are properly configured

2. **Routes not working:**
   - Ensure the environment variable is set to `true`
   - Clear the route cache: `php artisan route:clear`
   - Verify routes exist: `php artisan route:list | grep apple`

3. **Still seeing buttons when disabled:**
   - Verify `APPLE_AUTH_ENABLED=false` or remove the variable
   - Clear config and view caches: `php artisan config:clear && php artisan view:clear`
