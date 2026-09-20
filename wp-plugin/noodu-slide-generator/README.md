# Noodu Slide Generator - WordPress Plugin

AI-powered educational slide generator plugin for WordPress. Create professional presentations using OpenAI-compatible APIs.

## Features

✅ **AI-Powered Generation** - Create slides with OpenAI or compatible APIs
✅ **Dynamic Model Selection** - Fetch and choose from available models
✅ **PDF Export** - Generate publication-ready PDF presentations
✅ **Project Management** - Store and manage all your generated modules
✅ **Revision Tracking** - Track changes and revisions to presentations
✅ **REST API** - Full REST API for programmatic access
✅ **Shortcode Support** - Use `[noodu_generator]` on any page/post
✅ **WordPress Native** - Uses WordPress database and settings

## Installation

### Method 1: Direct Upload (Easiest)

1. Download the plugin folder
2. Upload to `/wp-content/plugins/`
3. Activate the plugin from WordPress Admin > Plugins
4. Go to Noodu > Settings to configure API

### Method 2: Manual Setup

```bash
# 1. Clone or download the plugin
cd /path/to/wordpress/wp-content/plugins/
# Copy noodu-slide-generator folder here

# 2. Activate in WordPress admin
# Plugins > Noodu Slide Generator > Activate

# 3. Configure settings
# Noodu > Settings (in WordPress admin)
```

## Configuration

1. **Get OpenAI API Key**
   - Visit https://platform.openai.com/api-keys
   - Create a new API key

2. **Configure in WordPress**
   - Go to Noodu > Settings in WordPress admin
   - Enter API Base URL: `https://api.openai.com/v1`
   - Paste your API key
   - Select your default model
   - Save

3. **System Requirements**
   - poppler-utils: `sudo apt-get install poppler-utils`
   - Chromium (for PDF generation)
   - PHP 7.4+

## Usage

### Admin Dashboard

1. Go to **Noodu > Dashboard**
2. Enter Project Name
3. Click **Fetch Models** to get available models
4. Select a model
5. Write your prompt describing the slides
6. Click **Generate Slides**
7. View and download from **Noodu > Projects**

### Using Shortcode

Add to any page or post:

```
[noodu_generator]
```

Users with `edit_posts` capability can use the generator on the frontend.

### REST API

**Fetch Models**
```
GET /wp-json/noodu/v1/models
Headers: X-WP-Nonce: <nonce>
```

**Generate Slides**
```
POST /wp-json/noodu/v1/generate
Headers: X-WP-Nonce: <nonce>
Body: {
    "project_name": "Module Name",
    "model": "gpt-4",
    "prompt": "Create slides about..."
}
```

**List Projects**
```
GET /wp-json/noodu/v1/projects
Headers: X-WP-Nonce: <nonce>
```

**Get Project**
```
GET /wp-json/noodu/v1/projects/{id}
Headers: X-WP-Nonce: <nonce>
```

**Delete Project**
```
DELETE /wp-json/noodu/v1/projects/{id}
Headers: X-WP-Nonce: <nonce>
```

**Download PDF**
```
GET /wp-json/noodu/v1/download/{id}
Headers: X-WP-Nonce: <nonce>
```

## Database

The plugin creates two tables:

**wp_noodu_projects**
- `id` - Project ID
- `user_id` - WordPress user ID
- `name` - Project name
- `code` - Unique project code
- `prompt` - Original prompt
- `model` - AI model used
- `status` - generating/completed/error
- `pdf_file` - Generated PDF filename
- `slide_count` - Number of slides
- `slides_data` - JSON slide data
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**wp_noodu_revisions**
- `id` - Revision ID
- `project_id` - Associated project
- `prompt` - Revision prompt
- `status` - pending/completed
- `created_at` - Creation timestamp

## File Storage

Generated files are stored in:
```
/wp-content/uploads/noodu-slides/
```

## Troubleshooting

### PDF Generation Fails

1. Install Chromium:
   ```bash
   sudo apt-get install chromium-browser
   ```

2. Check permissions:
   ```bash
   chmod 755 /wp-content/uploads/noodu-slides/
   ```

### API Errors

1. Verify API key is valid
2. Check Base URL format (include `/v1`)
3. Test connection in Settings page
4. Check API rate limits

### Text Extraction Issues

Install poppler-utils:
```bash
sudo apt-get install poppler-utils
```

### Permissions

- Admins can access all features from Dashboard
- Authors/Editors can use shortcode on pages/posts
- Subscribers cannot use the generator

## Security

- API keys are stored securely in WordPress options
- REST API requires WordPress nonce
- Only authenticated users can access
- File uploads validated by type and size
- SQL injection protection via wpdb

## Support

For issues:
1. Check plugin documentation
2. Verify system requirements
3. Enable WordPress debug mode:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
4. Check error logs in `/wp-content/debug.log`

## License

GPL v2 or later

## Changelog

### v1.0.0
- Initial release
- OpenAI API integration
- PDF generation
- REST API endpoints
- WordPress dashboard
- Shortcode support

## Credits

Built for Noodu Academy by Claude AI
