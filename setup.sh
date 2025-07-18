#!/bin/bash

echo "Setting up Traffic Tracker..."

check_docker() {
    if ! command -v docker &> /dev/null; then
        echo "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        echo "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
}

create_env_files() {
    echo "Creating environment files..."
    
    for dir in api frontend tracker; do
        if [ -f "./$dir/.env.example" ]; then
            if [ ! -f "./$dir/.env" ]; then
                cp "./$dir/.env.example" "./$dir/.env"
                echo "Created $dir/.env"
            fi
        fi
    done
}

start_containers() {
    echo "Starting Docker containers..."
    docker-compose up -d
    
    echo "Waiting for containers to be ready..."
    sleep 10
    
    for i in {1..30}; do
        if docker exec traffic-tracker-php php --version > /dev/null 2>&1; then
            echo "PHP container is ready"
            break
        fi
        echo "Waiting for PHP container... ($i/30)"
        sleep 2
    done
}

install_dependencies() {
    echo "Installing PHP dependencies..."
    if docker exec traffic-tracker-php composer install --optimize-autoloader; then
        echo "PHP dependencies installed"
    else
        echo "Failed to install PHP dependencies"
        exit 1
    fi
    
    echo "Frontend dependencies will be installed automatically..."
    echo "Waiting for frontend container to be ready..."
    sleep 5
    
    for i in {1..30}; do
        if docker logs traffic-tracker-frontend 2>&1 | grep -q "ready in"; then
            echo "Frontend dev server is ready"
            break
        fi
        echo "Waiting for frontend dev server... ($i/30)"
        sleep 2
    done
    
    echo "Installing Tracker dependencies..."
    if docker exec traffic-tracker-tracker npm install; then
        echo "Tracker dependencies installed"
    else
        echo "Failed to install Tracker dependencies"
        exit 1
    fi
    
    echo "Building tracker..."
    if docker exec traffic-tracker-tracker npm run build; then
        echo "Tracker built successfully"
    else
        echo "Failed to build tracker"
        exit 1
    fi
    
    echo "Setting up tracker files..."
    docker exec traffic-tracker-tracker cp -r /app/dist /usr/share/nginx/html/dist
    docker exec traffic-tracker-tracker cp /app/example.html /usr/share/nginx/html/index.html
    echo "Tracker files copied to nginx"
}

restart_services() {
    echo "Restarting services..."
    docker-compose restart frontend tracker
}

show_summary() {
    echo ""
    echo "Setup Complete!"
    echo ""
    echo "Your Traffic Tracker is now running:"
    echo "Frontend Dashboard: http://localhost:3000"
    echo "API Backend:        http://localhost:8001"
    echo "Tracker Example:    http://localhost:8080"
    echo "Database:           localhost:3307"
    echo ""
}

main() {
    check_docker
    create_env_files
    start_containers
    install_dependencies
    restart_services
    ./setup_demo_token.sh
    show_summary
}

main