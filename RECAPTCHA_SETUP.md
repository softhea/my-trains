# reCAPTCHA Setup Instructions

To enable Google reCAPTCHA protection on your registration form, follow these steps:

## 1. Create Google reCAPTCHA Credentials

1. Go to the [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Sign in with your Google account
3. Click "Create" or "+" to register a new site
4. Fill in the registration form:
   - **Label**: "My Trains App" (or your preferred name)
   - **reCAPTCHA type**: Select "reCAPTCHA v2" > "I'm not a robot" Checkbox
   - **Domains**: Add your domains:
     - For local development: `my-trains.local` or `localhost`
     - For production: `yourdomain.com`
   - **Accept the reCAPTCHA Terms of Service**
5. Click "Submit"
6. Copy your **Site Key** and **Secret Key**

## 2. Configure Environment Variables

Add the following variables to your `.env` file:

```env
# Google reCAPTCHA Configuration
NOCAPTCHA_SITEKEY=your_recaptcha_site_key_here
NOCAPTCHA_SECRET=your_recaptcha_secret_key_here
```

Replace:
- `your_recaptcha_site_key_here` with your actual reCAPTCHA Site Key
- `your_recaptcha_secret_key_here` with your actual reCAPTCHA Secret Key

## 3. Update for Production

For production deployment, make sure to:

1. Add your production domain to the reCAPTCHA site settings in Google Console
2. Update your `.env` file with the correct keys for production

## 4. Test the Implementation

1. Visit your registration page (`/register`)
2. Fill in the form fields
3. Complete the reCAPTCHA challenge by checking "I'm not a robot"
4. Submit the form
5. Registration should work if CAPTCHA is completed, fail if not

## Features Implemented

✅ **Password Confirmation Removed**: No longer required to confirm password
✅ **reCAPTCHA Integration**: Google reCAPTCHA v2 "I'm not a robot" checkbox
✅ **Spam Protection**: Prevents automated bot registrations
✅ **Validation**: Server-side verification of CAPTCHA response
✅ **Error Handling**: Clear error messages for failed CAPTCHA verification
✅ **Multilingual**: Supports English and Romanian interface
✅ **Clean UI**: CAPTCHA integrates seamlessly with the registration form

## What Changed

### Removed:
- Password confirmation field from registration form
- `confirmed` validation rule for password
- Password confirmation input and validation

### Added:
- Google reCAPTCHA v2 widget
- CAPTCHA validation in registration controller
- Security check label and error handling
- Environment variables for CAPTCHA keys

## Security Benefits

- **Bot Protection**: Prevents automated spam registrations
- **Simplified UX**: Users no longer need to type password twice
- **Server-side Validation**: CAPTCHA is verified on the server for security
- **Rate Limiting**: reCAPTCHA provides natural rate limiting

## Troubleshooting

- **CAPTCHA not loading**: Check your Site Key is correct in `.env`
- **Validation always fails**: Verify Secret Key is correct in `.env`
- **Domain errors**: Ensure your domain is added to reCAPTCHA site settings
- **Localhost issues**: Add `localhost` and your local domain to reCAPTCHA settings
- **SSL issues**: reCAPTCHA requires HTTPS for production domains

## Configuration Details

The reCAPTCHA configuration is in `config/captcha.php`:
- **secret**: Secret key from environment variable
- **sitekey**: Site key from environment variable  
- **timeout**: 30 seconds timeout for verification
