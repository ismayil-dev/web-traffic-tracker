# Use Node.js for development
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# Expose port 5173
EXPOSE 5173

# Use a script to handle npm install and dev server startup
CMD ["sh", "-c", "npm install && npm run dev -- --host 0.0.0.0"]