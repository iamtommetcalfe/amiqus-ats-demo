# Contributing to Amiqus ATS Demo

Thank you for considering contributing to the Amiqus ATS Demo project! This document outlines the guidelines for contributing to this project.

## Code of Conduct

Please be respectful and considerate when interacting with others in this project. Harassment or abusive behavior will not be tolerated.

## Development Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone git@github.com:iamtommetcalfe/amiqus-ats-demo.git
   cd amiqus-ats-demo
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```
5. Install Composer dependencies:
   ```bash
   docker-compose exec app composer install
   ```
6. Generate application key:
   ```bash
   docker-compose exec app php artisan key:generate
   ```
7. Run database migrations and seed the database:
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```
8. Install NPM dependencies and build assets:
   ```bash
   docker-compose exec app npm install
   docker-compose exec app npm run dev
   ```

## Development Workflow

1. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/your-feature-name
   ```
   or
   ```bash
   git checkout -b fix/issue-you-are-fixing
   ```

2. Make your changes and commit them with clear, descriptive commit messages:
   ```bash
   git commit -m "Add feature: description of the feature"
   ```

3. Push your branch to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

4. Create a pull request from your fork to the main repository.

## Coding Standards

- Follow the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard for PHP code
- Use Laravel's coding style for PHP files
- Follow Vue.js style guide for JavaScript and Vue components
- Use meaningful variable and function names
- Write clear comments for complex logic
- Keep functions and methods small and focused on a single responsibility

## Testing

- Write tests for new features or bug fixes
- Ensure all tests pass before submitting a pull request:
  ```bash
  docker-compose exec app php artisan test
  ```

## Documentation

- Update the README.md file if necessary
- Document new features or changes to existing functionality
- Add comments to your code where appropriate

## Pull Request Process

1. Ensure your code follows the project's coding standards
2. Update documentation if necessary
3. Make sure all tests pass
4. Describe your changes in the pull request description
5. Link to any related issues

## Questions or Issues

If you have any questions or encounter any issues, please open an issue on the GitHub repository.

Thank you for contributing!
