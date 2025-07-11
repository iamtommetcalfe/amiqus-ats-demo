<p align="center">
  <img src="public/amiqus-logo.svg" alt="Amiqus Logo" width="200">
</p>

# Amiqus ATS Demo

This project is a Laravel 12 application with a Vue.js SPA frontend, powered by Vite and styled with Tailwind CSS. The application is API-first and uses Docker for local development.

## Project Overview

- **Backend**: Laravel 12 API
- **Frontend**: Vue.js SPA with Tailwind CSS
- **Build Tool**: Vite
- **Development Environment**: Docker

## Requirements

- Docker
- Docker Compose

## First Time Setup

1. Clone the repository:
   ```bash
   git clone git@github.com:iamtommetcalfe/amiqus-ats-demo.git
   cd amiqus-ats-demo
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

4. Install Composer dependencies:
   ```bash
   docker-compose exec app composer install
   ```

5. Generate application key:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. The application should now be accessible at:
   - Laravel app: http://localhost
   - Vite dev server: http://localhost:5173

## Useful Commands

### Docker Commands

- Start the containers:
  ```bash
  docker-compose up -d
  ```

- Stop the containers:
  ```bash
  docker-compose down
  ```

- View container logs:
  ```bash
  docker-compose logs -f
  ```

- Access the PHP container:
  ```bash
  docker-compose exec app bash
  ```

- Access the MySQL container:
  ```bash
  docker-compose exec db bash
  ```

- View Vite server logs:
  ```bash
  docker-compose logs -f node
  ```

### Laravel Commands

- Run migrations:
  ```bash
  docker-compose exec app php artisan migrate
  ```

- Clear cache:
  ```bash
  docker-compose exec app php artisan cache:clear
  ```

- Run tests:
  ```bash
  docker-compose exec app php artisan test
  ```

### Frontend Commands

- Install npm dependencies:
  ```bash
  docker-compose exec node npm install
  ```

- Run Vite development server:
  ```bash
  docker-compose exec node npm run dev
  ```

- Build for production:
  ```bash
  docker-compose exec node npm run build
  ```

### Code Style and Linting

This project uses Laravel Pint for PHP code style and ESLint with Prettier for JavaScript/TypeScript/Vue code style. Pre-commit hooks are set up using Husky and lint-staged to automatically check and fix code style issues before commits.

#### PHP Code Style

- Check PHP code style:
  ```bash
  docker-compose exec app php ./vendor/bin/pint --test
  ```

- Fix PHP code style:
  ```bash
  docker-compose exec app php ./vendor/bin/pint
  ```

#### JavaScript/TypeScript/Vue Code Style

- Check JavaScript/TypeScript/Vue code style:
  ```bash
  docker-compose exec node npm run lint
  ```

- Fix JavaScript/TypeScript/Vue code style:
  ```bash
  docker-compose exec node npm run lint:fix
  ```

- Format JavaScript/TypeScript/Vue code:
  ```bash
  docker-compose exec node npm run format
  ```

#### Pre-commit Hooks

Pre-commit hooks are set up using Husky and lint-staged to automatically check and fix code style issues before commits. The hooks will run Laravel Pint on PHP files and ESLint/Prettier on JavaScript/TypeScript/Vue files.

The pre-commit hooks should be automatically installed when you run `npm install` due to the "prepare" script in package.json.

To install the pre-commit hooks after cloning the repository:

```bash
docker-compose exec node npm install
```

If the pre-commit hooks are not working (not firing when you commit), you can manually install them:

```bash
docker-compose exec node npx husky
```

To skip the pre-commit hooks (not recommended):

```bash
git commit -m "Your commit message" --no-verify
```

If you're still having issues with pre-commit hooks not firing, check that the Git hooks are properly installed:

```bash
ls -la .git/hooks
```

You should see a `pre-commit` file (not `pre-commit.sample`). If not, try reinstalling Husky:

```bash
docker-compose exec node npx husky uninstall
docker-compose exec node npx husky
```

## Typical Development Flow

1. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

2. Make changes to your Laravel code in the `app` directory.

3. Make changes to your Vue components in the `resources/js/components` directory.

4. The Vite development server will automatically reload when you make changes to your Vue components.

5. Run tests to ensure your changes don't break existing functionality:
   ```bash
   docker-compose exec app php artisan test
   ```

6. When you're done, stop the Docker containers:
   ```bash
   docker-compose down
   ```

## Vite Server Configuration

The Vite development server is configured to run on port 5173 by default. If you need to change the port or host, you can do so by setting the following environment variables in your `.env` file:

```
VITE_PORT=5173
VITE_HOST=0.0.0.0
VITE_HMR_HOST=
VITE_HMR_PORT=5173
```

The Vite server is configured to bind to all interfaces (0.0.0.0) to ensure it's accessible from outside the Docker container. For Hot Module Replacement (HMR), the configuration is set to auto-detect the appropriate host based on the current URL by leaving `VITE_HMR_HOST` empty. This ensures that hot reloading works correctly when accessing the application from different devices or networks.

If you encounter connection errors like `ERR_CONNECTION_REFUSED` when trying to access Vite assets, check the following:

1. Make sure the Vite server is running:
   ```bash
   docker-compose logs -f node
   ```

2. Verify that the port is correctly exposed in the docker-compose.yml file:
   ```yaml
   ports:
     - "5173:5173"
   ```

3. Check if there are any firewall or network issues preventing access to the Vite server.

### Troubleshooting Hot Reloading

If hot reloading isn't working (changes in code aren't reflected in the browser), try the following:

1. Make sure `VITE_HMR_HOST` is empty in your `.env` file to allow auto-detection:
   ```
   VITE_HMR_HOST=
   ```

2. Restart the Vite server:
   ```bash
   docker-compose restart node
   ```

3. Clear your browser cache or try a different browser.

4. Check the browser console for any errors related to WebSocket connections.

5. If you're accessing the application from a different device or network, make sure the device can access the Docker host on port 5173.

### Troubleshooting Docker and Vite Issues

#### Automated Troubleshooting Script

For your convenience, we've included a troubleshooting script that can help diagnose and fix common Docker and Vite issues:

```bash
# Make the script executable
chmod +x docker-troubleshoot.sh

# Run the script
./docker-troubleshoot.sh
```

The script will check your Docker setup, look for port conflicts, and provide a menu of troubleshooting options.

#### Manual Troubleshooting Steps

If you prefer to troubleshoot manually or if the script doesn't resolve your issue, try the following steps:

1. **Rebuild the Docker containers**:
   ```bash
   # Stop the containers
   docker-compose down

   # Remove any cached images
   docker-compose rm -f

   # Rebuild and start the containers
   docker-compose up -d --build
   ```

2. **Clear Vite cache**:
   ```bash
   # Remove the node_modules/.vite directory
   docker-compose exec app rm -rf node_modules/.vite

   # Restart the node service
   docker-compose restart node
   ```

3. **Clear browser cache**:
   - Press Ctrl+Shift+Delete (Windows/Linux) or Cmd+Shift+Delete (Mac)
   - Select "Cached images and files" and clear them
   - Alternatively, use incognito/private browsing mode for testing

4. **Check for port conflicts**:
   ```bash
   # Check if anything is using port 5173
   lsof -i :5173
   # If something is using the port, stop it or change the VITE_PORT in .env
   ```

5. **Verify Docker volumes**:
   ```bash
   # Check if volumes are correctly mounted
   docker-compose exec app ls -la
   ```

6. **Check Docker logs**:
   ```bash
   # Check logs for the node service
   docker-compose logs -f node

   # Check logs for the app service
   docker-compose logs -f app
   ```

7. **Restart the entire Docker setup**:
   ```bash
   docker-compose down
   docker-compose up -d
   ```

8. **Update npm packages**:
   ```bash
   docker-compose exec app npm install
   ```

If you're still experiencing issues after trying these steps, check the Laravel and Vite documentation for more specific troubleshooting advice.

## Database Configuration

This application uses MySQL as the database. The database configuration is set in the `.env` file. Make sure your `.env` file has the following database configuration:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

If you're experiencing issues with the database, such as missing tables, check the following:

1. Verify that your `.env` file has the correct database configuration (as shown above)
2. Make sure the MySQL container is running:
   ```bash
   docker-compose ps
   ```
3. Run the migrations to create the tables:
   ```bash
   docker-compose exec app php artisan migrate
   ```
4. Run the database seeder to populate the database with sample ATS data:
   ```bash
   docker-compose exec app php artisan db:seed
   ```

   This will create sample job postings, candidates, interview stages, and interviews for the ATS functionality.

5. If you need to connect to the database directly, you can use the following command:
   ```bash
   docker-compose exec db mysql -u root -proot laravel
   ```

## Project Structure

- `app/` - Laravel application code
- `resources/js/` - Vue.js components and frontend code
- `resources/css/` - Tailwind CSS and other styles
- `routes/` - Laravel routes (web.php for SPA, api.php for API endpoints)

## Features

- API-first design
- Single Page Application with Vue.js
- Component-driven UI with Tailwind CSS
- Dockerized development environment
- Hot module replacement with Vite
- Applicant Tracking System (ATS) functionality:
  - Job postings management
  - Candidate tracking
  - Interview stages and pipeline
  - Applicant status tracking

## API Documentation

The API endpoints are defined in `routes/api.php`. The application follows RESTful API design principles.

### Example API Endpoints

- `GET /api/hello` - Returns a simple JSON response

### ATS API Endpoints

- `GET /api/ats/jobs` - Returns a list of all open job postings with applicant counts
- `GET /api/ats/jobs/{id}` - Returns details of a specific job posting with applicants grouped by interview stage

## ATS Functionality

The Applicant Tracking System (ATS) functionality allows you to manage job postings, candidates, and the interview process. Key features include:

1. **Job Postings Management**
   - View all open job postings with applicant counts
   - View detailed information about each job posting

2. **Candidate Tracking**
   - Track candidates applying for job postings
   - View candidate details including contact information and current employment

3. **Interview Pipeline**
   - Organize candidates by interview stages
   - Track the status of each interview (pending, scheduled, completed, cancelled)
   - Add notes and feedback for each candidate

### Database Schema

The ATS functionality uses the following database tables:

- `job_postings` - Stores information about job openings
- `candidates` - Stores information about job applicants
- `interview_stages` - Defines the stages in the interview process
- `interviews` - Tracks the relationship between candidates and job postings, including the current stage and status

### Frontend Components

- **Home Page** - Displays a list of all open job postings with applicant counts
- **Job Details Page** - Shows detailed information about a job posting and its applicants grouped by interview stage

## Amiqus API Integration

This application includes integration with the Amiqus API using OAuth 2.0 for authentication. The integration allows you to:

1. Configure Amiqus API credentials
2. Authorize the application to access the Amiqus API
3. Refresh access tokens when they expire
4. Disconnect from the Amiqus API

### Setup Instructions

1. Register your application with Amiqus to obtain OAuth credentials
2. Add your Amiqus API credentials to your `.env` file:
   ```
   AMIQUS_API_URL=https://api.amiqus.co
   AMIQUS_AUTH_URL=https://auth.amiqus.co
   AMIQUS_CLIENT_ID=your-client-id
   AMIQUS_CLIENT_SECRET=your-client-secret
   AMIQUS_REDIRECT_URI=http://localhost/api/amiqus/callback
   ```
3. Run database migrations to create the necessary tables:
   ```bash
   docker-compose exec app php artisan migrate
   ```
4. Navigate to the Integration Settings page in the application
5. Enter your Amiqus API credentials and save them
6. Click "Connect to Amiqus" to authorize the application

### OAuth Flow

The OAuth flow follows these steps:

1. User clicks "Connect to Amiqus" on the Integration Settings page
2. User is redirected to the Amiqus authorization page
3. User authorizes the application
4. User is redirected back to the application with an authorization code
5. The application exchanges the authorization code for an access token
6. The access token is stored in the database for future API requests

### Database Schema

The application uses the following database tables for OAuth integration:

- `amiqus_oauth_clients` - Stores client credentials (client ID, client secret, etc.)
- `amiqus_oauth_access_tokens` - Stores access tokens received from Amiqus
- `amiqus_oauth_refresh_tokens` - Stores refresh tokens for renewing access tokens

Note: The `access_token` and `refresh_token` columns are stored as TEXT fields to accommodate the length of JWT tokens, which can exceed the default VARCHAR(255) limit.

#### Refresh Tokens Handling

In the Amiqus API, refresh tokens can only be used once and then you need a new one, which is why there is no specific expiry for refresh tokens. However, the database schema requires the `expires_at` column to have a non-null value. To handle this, the application sets a far-future expiry date (10 years from creation) for refresh tokens that don't have a specific expiry time provided by the API.

### API Endpoints

The following API endpoints are available for the Amiqus integration:

- `GET /api/amiqus/settings` - Get the current Amiqus integration settings
- `POST /api/amiqus/settings` - Save Amiqus API credentials
- `GET /api/amiqus/authorize` - Redirect to the Amiqus authorization page
- `GET /api/amiqus/callback` - Handle the callback from Amiqus
- `POST /api/amiqus/refresh-token` - Refresh the access token
- `POST /api/amiqus/disconnect` - Disconnect from the Amiqus API

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
