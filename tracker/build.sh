#!/bin/bash

echo "Building Traffic Tracker..."

# Install dependencies
echo "Installing dependencies..."
npm install

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Build the project
echo "🔨 Building project..."
npm run build

echo "Build completed!"