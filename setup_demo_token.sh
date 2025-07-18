#!/bin/bash

echo "Generating demo token and updating frontend configuration..."

# Check if containers are running
if ! docker ps | grep -q "traffic-tracker-php"; then
    echo "Error: PHP container is not running. Please run setup.sh first."
    exit 1
fi

# Generate the demo token
echo "Generating demo token..."
TOKEN=$(docker exec traffic-tracker-php php generate_demo_token.php | tr -d '\n' | tr -d ' ')

if [ -z "$TOKEN" ]; then
    echo "Error: Failed to generate demo token"
    exit 1
fi

echo "Generated token: $TOKEN"

# Update frontend/.env file
ENV_FILE="frontend/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo "Error: $ENV_FILE not found"
    exit 1
fi

sed -i.tmp "s/^VITE_API_TOKEN=.*/VITE_API_TOKEN=\"$TOKEN\"/" "$ENV_FILE"
rm "$ENV_FILE.tmp"

echo "Updated $ENV_FILE with new token"
echo "Demo token setup complete!"