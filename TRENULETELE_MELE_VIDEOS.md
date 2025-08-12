# Using Real Trenuletele Mele Videos

## Current Status
✅ All products now have placeholder videos that can be easily replaced with real Trenuletele Mele content.

## How to Get Real Video URLs

1. **Visit the Trenuletele Mele Channel:**
   - Go to: https://www.youtube.com/@TrenuleteleMele/videos
   - Browse through their video collection

2. **Copy Video URLs:**
   - Click on any video you want to use
   - Copy the URL from the address bar (format: `https://www.youtube.com/watch?v=VIDEO_ID`)

## How to Replace Videos

### Method 1: Replace Individual Videos
Use the custom Laravel command to replace videos one by one:

```bash
php84 artisan videos:replace "OLD_URL" "NEW_TRENULETELE_MELE_URL"
```

**Example:**
```bash
php84 artisan videos:replace "https://www.youtube.com/watch?v=StTqXEQ2l-Y" "https://www.youtube.com/watch?v=REAL_TRENULETELE_MELE_VIDEO_ID"
```

### Method 2: Update Multiple Videos at Once
Edit the file: `app/Console/Commands/UpdateProductVideos.php`

Replace the placeholder URLs in lines 36-60 with real Trenuletele Mele video URLs, then run:
```bash
php84 artisan products:update-videos
```

### Method 3: Manual Database Update
You can also update videos directly in the database:
```sql
UPDATE videos SET url = 'https://www.youtube.com/watch?v=REAL_VIDEO_ID' WHERE url = 'https://www.youtube.com/watch?v=OLD_VIDEO_ID';
```

## Video Categories and Suggestions

### Steam Locomotives
Look for videos featuring:
- CFR steam locomotives
- Vintage steam trains
- Steam locomotive restoration

### Electric Trains
Look for videos featuring:
- CFR electric locomotives
- Modern electric trains
- High-speed electric trains

### Freight Cars
Look for videos featuring:
- CFR Cargo trains
- Freight operations
- Cargo transportation

### Passenger Cars
Look for videos featuring:
- CFR passenger services
- Intercity trains
- Passenger car showcases

### Train Sets
Look for videos featuring:
- Model train collections
- Complete train sets
- Model railway layouts

## Commands Available

1. **View current videos:**
   ```bash
   php84 artisan videos:report
   ```

2. **Replace a video:**
   ```bash
   php84 artisan videos:replace "old_url" "new_url"
   ```

3. **Update all videos:**
   ```bash
   php84 artisan products:update-videos
   ```

## Notes
- All video URLs are automatically converted to embed format for display
- Videos support multiple YouTube URL formats (watch, youtu.be, embed)
- The application validates YouTube URLs before displaying them
- Videos are displayed responsively on product pages

## Current Video Distribution
- **16 total videos** across **10 products**
- Each product has 1-2 relevant videos
- Videos are categorized by product type for better relevance

