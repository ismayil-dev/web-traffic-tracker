# Development stage - single stage to preserve node_modules
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Copy source code
COPY . .

# Install nginx in the same stage
RUN apk add --no-cache nginx

# Create nginx directories
RUN mkdir -p /usr/share/nginx/html

# Create nginx config
RUN echo 'server { listen 80; location / { root /usr/share/nginx/html; index index.html; } }' > /etc/nginx/http.d/default.conf

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]