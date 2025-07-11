#!/bin/bash

# Docker and Vite Troubleshooting Script
echo "=== Docker and Vite Troubleshooting Script ==="
echo "This script will help you troubleshoot Docker and Vite issues."
echo ""

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check if Docker is running
echo "Checking if Docker is running..."
if ! command_exists docker; then
    echo "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! docker info >/dev/null 2>&1; then
    echo "Docker is not running. Please start Docker first."
    exit 1
fi

echo "Docker is running."
echo ""

# Check Docker Compose
echo "Checking Docker Compose..."
if ! command_exists docker-compose; then
    echo "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "Docker Compose is installed."
echo ""

# Check Docker containers
echo "Checking Docker containers..."
containers=$(docker-compose ps -q)
if [ -z "$containers" ]; then
    echo "No Docker containers are running. Starting containers..."
    docker-compose up -d
else
    echo "Docker containers are running."
fi
echo ""

# Check for port conflicts
echo "Checking for port conflicts..."
if command_exists lsof; then
    port_5173=$(lsof -i :5173 | grep LISTEN)
    if [ -n "$port_5173" ]; then
        echo "Port 5173 is in use by another process:"
        echo "$port_5173"
        echo "This might cause conflicts with Vite. Consider changing the VITE_PORT in .env or stopping the conflicting process."
    else
        echo "No conflicts found on port 5173."
    fi
else
    echo "lsof command not found. Skipping port conflict check."
fi
echo ""

# Menu for troubleshooting options
echo "Please select a troubleshooting option:"
echo "1. Restart Docker containers"
echo "2. Rebuild Docker containers"
echo "3. Clear Vite cache"
echo "4. Check Docker logs"
echo "5. Update npm packages"
echo "6. Full reset (rebuild containers, clear cache, update packages)"
echo "7. Exit"
echo ""

read -p "Enter your choice (1-7): " choice

case $choice in
    1)
        echo "Restarting Docker containers..."
        docker-compose down
        docker-compose up -d
        echo "Docker containers restarted."
        ;;
    2)
        echo "Rebuilding Docker containers..."
        docker-compose down
        docker-compose rm -f
        docker-compose up -d --build
        echo "Docker containers rebuilt."
        ;;
    3)
        echo "Clearing Vite cache..."
        docker-compose exec app rm -rf node_modules/.vite
        docker-compose restart node
        echo "Vite cache cleared and node service restarted."
        ;;
    4)
        echo "Checking Docker logs..."
        echo "Logs for node service:"
        docker-compose logs node
        echo ""
        echo "Logs for app service:"
        docker-compose logs app
        ;;
    5)
        echo "Updating npm packages..."
        docker-compose exec app npm install
        docker-compose restart node
        echo "npm packages updated and node service restarted."
        ;;
    6)
        echo "Performing full reset..."
        docker-compose down
        docker-compose rm -f
        docker-compose up -d --build
        docker-compose exec app rm -rf node_modules/.vite
        docker-compose exec app npm install
        docker-compose restart node
        echo "Full reset completed."
        ;;
    7)
        echo "Exiting..."
        exit 0
        ;;
    *)
        echo "Invalid choice. Exiting..."
        exit 1
        ;;
esac

echo ""
echo "Troubleshooting completed. If you're still experiencing issues, please check the README.md for more detailed troubleshooting steps."
