# Use Node.js for development
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Copy source code
COPY . .

# Expose port 5173
EXPOSE 5173

# Keep container running and wait for dependencies
CMD ["sh", "-c", "tail -f /dev/null"]