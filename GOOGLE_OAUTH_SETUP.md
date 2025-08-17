# OAuth Setup Instructions (Google & Apple)

To enable Google and Apple OAuth authentication in your Laravel application, follow these steps:

## 1. Create Google OAuth Credentials

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API:
   - Go to "APIs & Services" > "Library"
   - Search for "Google+ API" and enable it
4. Create OAuth 2.0 credentials:
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth 2.0 Client IDs"
   - Configure the OAuth consent screen if prompted
   - Application type: "Web application"
   - Name: "My Trains App" (or your preferred name)
   - Authorized redirect URIs: Add your callback URL(s):
     - For local development: `http://my-trains.local/auth/google/callback`
     - For production: `https://yourdomain.com/auth/google/callback`

## 2. Create Apple OAuth Credentials

1. Go to the [Apple Developer Portal](https://developer.apple.com/)
2. Sign in with your Apple Developer account
3. Go to "Certificates, Identifiers & Profiles"
4. Create a new App ID:
   - Select "App IDs" from the sidebar
   - Click the "+" button to register a new App ID
   - Select "App" and continue
   - Fill in the description and Bundle ID (e.g., `com.yourcompany.mytrains`)
   - Enable "Sign In with Apple" capability
5. Create a Services ID:
   - Select "Services IDs" from the sidebar
   - Click the "+" button to register a new Services ID
   - Fill in the description and identifier (this will be your `APPLE_CLIENT_ID`)
   - Enable "Sign In with Apple" and configure:
     - Primary App ID: Select the App ID you created above
     - Domains and Subdomains: Add your domain (e.g., `my-trains.local` or your production domain)
     - Return URLs: Add your callback URL (e.g., `http://my-trains.local/auth/apple/callback`)
6. Create a Private Key:
   - Select "Keys" from the sidebar
   - Click the "+" button to register a new key
   - Give it a name and enable "Sign In with Apple"
   - Configure the key with your App ID
   - Download the private key file (.p8) - you'll need this for the client secret

## 3. Configure Environment Variables

Add the following variables to your `.env` file:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://my-trains.local/auth/google/callback

# Apple OAuth Configuration
APPLE_CLIENT_ID=your_apple_services_id_here
APPLE_CLIENT_SECRET=your_generated_apple_client_secret_here
APPLE_REDIRECT_URI=http://my-trains.local/auth/apple/callback
```

Replace:
- `your_google_client_id_here` with your actual Google Client ID
- `your_google_client_secret_here` with your actual Google Client Secret
- `your_apple_services_id_here` with your Apple Services ID
- `your_generated_apple_client_secret_here` with your generated Apple client secret (JWT token)
- Update the redirect URIs to match your domain

### Apple Client Secret Generation

Apple requires a JWT token as the client secret. You'll need to generate this using:
- Your Team ID (found in Apple Developer Portal)
- Your Services ID (Client ID)
- Your Key ID (from the private key you downloaded)
- Your Private Key (.p8 file)

You can use online JWT generators or create a script to generate the token. The token should be signed with ES256 algorithm.

## 4. Update Redirect URIs for Production

For production deployment, update both redirect URIs in your `.env` file:

```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
APPLE_REDIRECT_URI=https://yourdomain.com/auth/apple/callback
```

And add these URLs to your OAuth credentials:
- Google: In the Google Cloud Console
- Apple: In the Apple Developer Portal Services ID configuration

## 5. Test the Implementation

1. Visit your login page (`/login`)
2. Click either "Sign in with Google" or "Sign in with Apple"
3. You should be redirected to the respective authentication page
4. After successful authentication, you'll be redirected back to your app

## Features

✅ **New User Registration**: Creates new accounts automatically from Google/Apple profiles
✅ **Existing User Login**: Logs in users who already have accounts
✅ **Account Linking**: Links Google/Apple accounts to existing email-based accounts
✅ **Avatar Integration**: Uses Google profile pictures as user avatars (Apple may not provide avatars)
✅ **Email Verification**: Google/Apple accounts are pre-verified
✅ **Multilingual**: Supports English and Romanian interface
✅ **Dual Provider Support**: Both Google and Apple authentication available

## Security Notes

- Users created via OAuth don't have passwords (password field is nullable)
- Google avatars are prioritized over uploaded profile images
- Apple may not always provide user avatars
- All OAuth users get the default "user" role
- Email verification is automatically marked as complete for OAuth accounts
- Users can link multiple OAuth providers to the same email address

## Troubleshooting

### Google OAuth
- **Error 400**: Check your redirect URI matches exactly in both `.env` and Google Console
- **Error 401**: Verify your Client ID and Client Secret are correct
- **Redirect mismatch**: Ensure the callback URL is added to Google OAuth credentials
- **Missing scopes**: The app requests email and profile information by default

### Apple OAuth
- **Invalid client**: Verify your Services ID (Client ID) is correct
- **Invalid client secret**: Ensure your JWT token is properly generated and not expired
- **Redirect URI mismatch**: Check that the callback URL matches in both `.env` and Apple Developer Portal
- **Domain not verified**: Make sure your domain is added to the Services ID configuration
- **Missing name**: Apple doesn't always provide user names, app handles this gracefully
