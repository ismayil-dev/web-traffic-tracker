# Traffic Tracker - Docker Setup

A comprehensive traffic analytics system with PHP API backend, React frontend dashboard, and HTML tracking example.

## Quick Start

Start all services with Docker Compose:

```bash
docker-compose up -d
```

This will start:
- **Database**: MySQL 8.0 on port `3306`
- **API**: PHP 8.2 + nginx on port `8001` 
- **Frontend Dashboard**: React app on port `3000`
- **Tracker Example**: HTML demo on port `8080`

## Services

### 1. API (http://localhost:8001)
- PHP 8.2 with nginx and php-fpm
- RESTful API with JWT authentication
- Database: MySQL with visits, unique_visitors, daily_stats tables

### 2. Frontend Dashboard (http://localhost:3000)
- React TypeScript dashboard
- Real-time analytics visualization
- Custom date period selection

### 3. Tracker Example (http://localhost:8080)
- HTML integration example
- Live tracking demonstration
- Copy-paste JavaScript snippet

## Database Schema

- **visits**: Raw visit events with timestamps
- **unique_visitors**: Deduplicated visitor tracking
- **daily_stats**: Pre-aggregated daily metrics

## Environment Variables

### Frontend (.env)
- `VITE_API_BASE_URL`: API endpoint URL
- `VITE_API_TOKEN`: JWT authentication token
- `VITE_DOMAIN_ID`: Domain identifier

### Docker
All environment variables are configured in `docker-compose.yml`

## Development

To rebuild specific services:

```bash
# Rebuild API
docker-compose build php api

# Rebuild frontend
docker-compose build frontend

# View logs
docker-compose logs -f [service-name]
```

## API Endpoints

- `GET /api/v1/analytics` - Main analytics data
- `GET /api/v1/analytics/top-pages` - Popular pages
- `GET /api/v1/analytics/visitor-breakdown` - Browser/OS/Device stats  
- `GET /api/v1/analytics/recent-visits` - Live activity feed
- `GET /api/v1/analytics/historical` - Chart data
- `POST /api/v1/visits` - Track new visits

## Integration

Add this script to any website to start tracking:

```html
<script>
(function() {
    const config = {
        apiUrl: 'http://localhost:8001/api/v1/visits',
        token: 'YOUR_JWT_TOKEN',
        domainId: 1
    };
    
    function trackVisit() {
        const data = {
            domain_id: config.domainId,
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer || '',
            user_agent: navigator.userAgent,
            visit_time: new Date().toISOString()
        };

        fetch(config.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + config.token
            },
            body: JSON.stringify(data)
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', trackVisit);
    } else {
        trackVisit();
    }
})();
</script>
```

## Stopping Services

```bash
docker-compose down
```

To remove volumes:
```bash
docker-compose down -v
```