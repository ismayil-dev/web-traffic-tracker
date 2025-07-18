# Web Traffic Tracker

A comprehensive traffic analytics system with PHP API backend, React frontend dashboard, and JavaScript tracking client.

## Installation

### Prerequisites

Make sure you have Docker and Docker Compose installed on your system.

### Setup Process

1. Clone the repository and navigate to the project directory
2. Run the setup script:

```bash
./setup.sh
```

This script will create `.env` files for all services and start the application. The build process will install all dependencies inside the containers. This might take a few minutes on the first run.

### Services and Ports

After startup, you can access:

- Frontend Dashboard: http://localhost:3000
- API Backend: http://localhost:8001
- Tracker Example: http://localhost:8080
- Database: localhost:3307 (MySQL)

## Project Structure

The project consists of three main services:

**API Service** - PHP 8.4 backend with nginx
- Handles analytics data and visitor tracking
- JWT-based authentication
- RESTful API endpoints
- Located in `api/` directory

**Frontend Service** - React TypeScript dashboard
- Real-time analytics visualization
- Interactive charts and metrics
- Located in `frontend/` directory

**Tracker Service** - JavaScript tracking client
- Lightweight tracking script
- Webpack-based build system
- Located in `tracker/` directory

## Environment Configuration

Each service has its own `.env` file with specific configuration:

**API Configuration** (`api/.env`)
```
APP_ENV=local
APP_URL=http://localhost

DB_TYPE=mysql
DB_HOST=database
DB_PORT=3306
DB_NAME=traffic_tracker
DB_USER=tracker_user
DB_PASSWORD=tracker_password

JWT_SECRET=your-secret-key-here
```

**Frontend Configuration** (`frontend/.env`)
```
VITE_API_BASE_URL=http://localhost:8001/api/v1
VITE_API_TOKEN="your-jwt-token-here"
VITE_DOMAIN_ID=1
```

**Tracker Configuration** (`tracker/.env`)
```
API_ENDPOINT=http://localhost:8001/api/v1/track-visit
```

## API Endpoints

The API provides several endpoints for analytics data:

**Analytics Data**
- `GET /api/v1/analytics` - Main analytics statistics
- `GET /api/v1/analytics/top-pages` - Most visited pages
- `GET /api/v1/analytics/visitor-breakdown` - Browser, OS, and device statistics
- `GET /api/v1/analytics/recent-visits` - Recent visitor activity
- `GET /api/v1/analytics/historical` - Historical data for charts

**Tracking**
- `POST /api/v1/track-visit` - Record a new visit

All endpoints require JWT authentication via the `Authorization: Bearer <token>` header.

## Website Integration

To add tracking to your website, include the tracking script:

```html
<script src="http://localhost:8080/dist/tracker.min.js" 
        data-api-key="your-domain-api-key">
</script>
```

The tracker will automatically collect page views, visitor information, and send data to your API backend.

## Database Schema

The system uses MySQL with the following main tables:

- `visits` - Raw visit events with timestamps and visitor data
- `users` - User accounts for dashboard access
- `domains` - Registered domains and their API keys
- `daily_stats` - Pre-aggregated daily statistics for performance


## Troubleshooting

**Port conflicts**: If ports 3000, 8001, or 8080 are already in use, modify the port mappings in `docker-compose.yml`.


**CORS errors**: Verify that the frontend is connecting to the correct API URL (http://localhost:8001).

**Missing dependencies**: If you encounter issues with missing packages, rebuild the containers with `docker-compose build --no-cache`.

**Invalid API key errors**: If frontend API requests fail due to invalid API key, run `php generate_demo_token.php` in the api directory, then copy the generated token and paste it in `frontend/.env` file as `VITE_API_TOKEN`.

## Development Notes

The application is designed for development with Docker. All services restart automatically on failure, and configuration changes can be made by editing the respective `.env` files.

Database migrations and initial data setup are handled automatically when the containers start for the first time.