# GitHub Authentication Setup

This project requires GitHub authentication to access private repositories and packages.

## Problem

If you see errors like:
```
Could not authenticate against github.com
The oauth token for github.com seems invalid
```

The GitHub token in `auth.json` is expired or invalid.

## Solution

### Step 1: Generate a New GitHub Personal Access Token

1. Go to GitHub: https://github.com/settings/tokens
2. Click "Generate new token" → "Generate new token (classic)"
3. Give it a descriptive name (e.g., "Sowidu Composer Access")
4. Select the following scopes:
   - ✅ `repo` (Full control of private repositories)
   - ✅ `read:packages` (Download packages from GitHub Package Registry)
5. Click "Generate token"
6. **Copy the token immediately** (you won't be able to see it again)

### Step 2: Update auth.json

**Option A: Manual Update**
Edit `auth.json` and replace the token:

```json
{
    "github-oauth": {
        "github.com": "ghp_YOUR_NEW_TOKEN_HERE"
    }
}
```

**Option B: Using Composer (Recommended)**
```bash
./vendor/bin/sail composer config --global github-oauth.github.com YOUR_NEW_TOKEN
```

Or for project-specific (recommended):
```bash
./vendor/bin/sail composer config github-oauth.github.com YOUR_NEW_TOKEN
```

### Step 3: Verify Authentication

Test the authentication:
```bash
./vendor/bin/sail composer diagnose
```

You should see:
```
Checking github.com oauth access: OK
```

### Step 4: Test Installation

Try installing dependencies:
```bash
./vendor/bin/sail composer install
```

## Security Notes

⚠️ **Important**: 
- Never commit `auth.json` to version control
- Add `auth.json` to `.gitignore` if not already there
- Use environment variables or secure storage for tokens in CI/CD
- Rotate tokens regularly

## Troubleshooting

### Token Still Not Working
1. Verify the token has the correct scopes
2. Check if the token has expired
3. Ensure you're using the correct token format (starts with `ghp_`)
4. Try regenerating the token

### Using Environment Variables (Alternative)

Instead of `auth.json`, you can use environment variables:

```bash
export COMPOSER_AUTH='{"github-oauth":{"github.com":"ghp_YOUR_TOKEN"}}'
./vendor/bin/sail composer install
```

Or add to your `.env` file and use in Sail:
```env
COMPOSER_AUTH={"github-oauth":{"github.com":"ghp_YOUR_TOKEN"}}
```

---

**Last Updated**: 2024-12-10

