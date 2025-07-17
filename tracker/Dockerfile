# Development stage - single stage to preserve node_modules
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# .env file will be mounted as volume in docker-compose

# Build the project
RUN npm run build

# Install nginx in the same stage
RUN apk add --no-cache nginx

# Create nginx directories and copy built files
RUN mkdir -p /usr/share/nginx/html
RUN cp -r /app/dist /usr/share/nginx/html/dist
RUN cp /app/example.html /usr/share/nginx/html/index.html

# Create nginx config
RUN echo 'server { listen 80; location / { root /usr/share/nginx/html; index index.html; } }' > /etc/nginx/http.d/default.conf

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]