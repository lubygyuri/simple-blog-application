# Installation Guide

## Prerequisites

- Docker and Docker Compose installed on your system
- Git
- A text editor of your choice

## Installation steps

### 1. Clone the repository
```bash
git clone git@github.com:lubygyuri/simple-blog-application.git
```

### 2. Setup
#### 2.1 Move into the cloned directory
```bash
cd simple-blog-application
```

#### 2.1 Copy the environment file
```bash
cp .env.example .env
```

#### 2.2 Setup the environment
On **development** environment:
```bash
cp dev.compose.override.yml compose.override.yml
```

On **production** environment:
```bash
cp prod.compose.override.yml compose.override.yml
```

#### 2.3 Adjust the environment file (.env) to suit your needs
*(For development, you might just leave it as it is)*

#### 2.4. Configure local domain (only for development)
Add the following line to your `/etc/hosts` file:
```bash
echo "127.0.0.1 sba.test" | sudo tee -a /etc/hosts
```

### 3. Build and start docker containers
```bash
docker compose up -d
```

### 4. Install dependencies
```bash
docker compose exec backend composer install
```

### 5. Laravel application setup
```bash
# Generate application key
docker compose exec backend php artisan key:generate

# Run database migrations
docker compose exec backend php artisan migrate

# Create storage link
docker compose exec backend php artisan storage:link
```

### 6. Seed the database (only for development)
```bash
docker compose exec backend php artisan db:seed
```

### 7. Make sure you have the correct permissions
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 8. Accessing the application 
Application: http://sba.test
