#!/bin/bash
echo "Setting up Web Traffic Tracker..."

setup_env_file() {
    local dir=$1

    if [ -f "$dir/.env.example" ]; then
        if [ ! -f "$dir/.env" ]; then
            cp "$dir/.env.example" "$dir/.env"
            echo -e "Created $dir/.env from .env.example"
        else
            echo -e "$dir/.env already exists, skipping"
        fi
    else
        echo -e "$dir/.env.example not found"
    fi
}

# Setup .env files for each service
setup_env_file "./api"
setup_env_file "./frontend"
setup_env_file "./tracker"

echo ""
echo "Configuration files created:"
echo "- api/.env (API configuration)"
echo "- frontend/.env (Frontend configuration)"
echo "- tracker/.env (Tracker configuration)"

echo "Starting the application..."
docker-compose up -d
echo ""
echo -e "Setup complete!"