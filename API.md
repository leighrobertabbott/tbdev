# TorrentBits 2025 API Documentation

## Base URL
```
/api
```

## Authentication

Most endpoints require authentication via JWT token. Include the token in the Authorization header:

```
Authorization: Bearer YOUR_JWT_TOKEN
```

Or as a cookie named `auth_token`.

## Endpoints

### Authentication

#### POST /api/auth/login
Login and receive JWT token.

**Request:**
```json
{
  "username": "user123",
  "password": "password123"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "username": "user123",
    "class": 0
  }
}
```

#### POST /api/auth/logout
Logout (requires authentication).

#### GET /api/auth/me
Get current user information (requires authentication).

### Torrents

#### GET /api/torrents
List torrents with pagination and filters.

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `category` (int): Filter by category ID
- `search` (string): Search query

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Example Torrent",
      "size": 1073741824,
      "seeders": 10,
      "leechers": 5,
      "added": 1640995200
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 50,
    "total": 100,
    "pages": 2
  }
}
```

#### GET /api/torrents/{id}
Get torrent details.

#### POST /api/torrents
Create new torrent (requires authentication).

**Request:**
```json
{
  "name": "New Torrent",
  "category": 1,
  "info_hash": "...",
  "size": 1073741824
}
```

### Users

#### GET /api/users/{id}
Get user details.

#### GET /api/users/{id}/stats
Get user statistics.

**Response:**
```json
{
  "data": {
    "id": 1,
    "username": "user123",
    "uploaded": 10737418240,
    "downloaded": 5368709120,
    "ratio": "2.00"
  }
}
```

### Statistics

#### GET /api/stats
Get global statistics (no authentication required).

**Response:**
```json
{
  "data": {
    "users": 1000,
    "torrents": 5000,
    "peers": 15000,
    "seeders": 10000,
    "leechers": 5000
  }
}
```

### Categories

#### GET /api/categories
Get all categories (no authentication required).

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Movies",
      "image": "movies.gif"
    }
  ]
}
```

### Search

#### GET /api/search
Advanced search with filters and sorting (requires authentication).

**Query Parameters:**
- `q` (string): Search query
- `category` (int): Filter by category
- `sort` (string): Sort by (`newest`, `seeders`, `size`)
- `page` (int): Page number

## Error Responses

All errors follow this format:

```json
{
  "error": "Error message here"
}
```

**Status Codes:**
- `200` - Success
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Server Error

## Rate Limiting

Some endpoints have rate limiting:
- Login: 5 attempts per 5 minutes per IP
- API requests: 100 requests per minute per user

## Pagination

All list endpoints support pagination with these parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 50, max: 100)

## Examples

### JavaScript/Fetch
```javascript
// Login
const response = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ username: 'user', password: 'pass' })
});
const { token } = await response.json();

// Get torrents
const torrents = await fetch('/api/torrents?page=1', {
  headers: { 'Authorization': `Bearer ${token}` }
}).then(r => r.json());
```

### cURL
```bash
# Login
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user","password":"pass"}'

# Get torrents
curl http://localhost/api/torrents \
  -H "Authorization: Bearer YOUR_TOKEN"
```

