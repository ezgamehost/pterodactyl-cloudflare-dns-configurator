# Development Setup Guide

This guide is for developers who want to contribute to or modify the Cloudflare DNS Configurator plugin.

## 🛠️ Development Environment

### Prerequisites
- PHP 8.0 or higher
- Composer
- Git
- A Pterodactyl installation for testing
- Cloudflare account with API access

### 1. Fork and Clone
```bash
# Fork the repository on GitHub first, then:
git clone https://github.com/YOUR_USERNAME/whmcs-pterodactyl-cloudflare-dns-configurator.git
cd whmcs-pterodactyl-cloudflare-dns-configurator

# Add upstream remote
git remote add upstream https://github.com/ORIGINAL_USERNAME/whmcs-pterodactyl-cloudflare-dns-configurator.git
```

### 2. Install Dependencies
```bash
# Install development dependencies
composer install

# Install in development mode
composer install --dev
```

### 3. Development Setup
```bash
# Copy environment file
cp env.example .env

# Configure for development
# Edit .env with your Cloudflare credentials
nano .env
```

## 🏗️ Project Structure

```
ptero-plugin/
├── CloudflareDnsConfiguratorPlugin.php  # Main plugin class
├── Services/
│   └── CloudflareDnsService.php        # Cloudflare API service
├── Providers/
│   ├── CloudflareDnsServiceProvider.php # Main service provider
│   └── ViewServiceProvider.php          # View component provider
├── Views/
│   ├── Components/                      # View component classes
│   └── components/                      # Blade templates
├── config/
│   └── cloudflare.php                   # Configuration
├── database/
│   └── migrations/                      # Database migrations
├── composer.json                        # Dependencies
└── README.md                           # Documentation
```

## 🔧 Development Workflow

### 1. Make Changes
```bash
# Create a feature branch
git checkout -b feature/your-feature-name

# Make your changes
# Test locally
# Commit your changes
git add .
git commit -m "feat: add your feature description"
```

### 2. Testing
```bash
# Test the plugin in your Pterodactyl installation
# Copy the plugin to your Pterodactyl app/Plugins/ directory
cp -r . /path/to/pterodactyl/app/Plugins/CloudflareDnsConfigurator/

# Test functionality
# Check logs for errors
tail -f /path/to/pterodactyl/storage/logs/laravel.log
```

### 3. Code Quality
```bash
# Run PHP syntax check
find . -name "*.php" -exec php -l {} \;

# Check for coding standards (if you have PHP_CodeSniffer)
./vendor/bin/phpcs --standard=PSR12 .

# Run tests (if you add them)
./vendor/bin/phpunit
```

## 📝 Adding New Features

### 1. New Service Methods
```php
// In Services/CloudflareDnsService.php
public function newFeature(): bool
{
    // Implementation
    return true;
}
```

### 2. New View Components
```php
// Create Views/Components/NewComponent.php
namespace Pterodactyl\Plugins\CloudflareDnsConfigurator\Views\Components;

use Illuminate\View\Component;

class NewComponent extends Component
{
    public function render()
    {
        return view('cloudflare-dns-configurator::components.new-component');
    }
}
```

### 3. New Configuration Options
```php
// In config/cloudflare.php
'new_feature' => env('CLOUDFLARE_NEW_FEATURE', false),
```

## 🧪 Testing

### Manual Testing
1. **Create a test Minecraft server** in Pterodactyl
2. **Check logs** for plugin activity
3. **Verify DNS records** are created in Cloudflare
4. **Test DNS display** in the panel
5. **Delete server** and verify cleanup

### Automated Testing (Future)
```bash
# Run tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

## 📚 Useful Commands

### Development
```bash
# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Publish configuration
php artisan vendor:publish --tag=cloudflare-dns-config --force

# Publish views
php artisan vendor:publish --tag=cloudflare-dns-configurator-views --force

# Check plugin status
php artisan tinker
>>> app('Pterodactyl\Plugins\CloudflareDnsConfigurator\CloudflareDnsConfiguratorPlugin')
```

### Git Workflow
```bash
# Update from upstream
git fetch upstream
git checkout main
git merge upstream/main

# Push your changes
git push origin feature/your-feature-name

# Create pull request on GitHub
```

## 🐛 Debugging

### Enable Debug Mode
```env
CLOUDFLARE_DEBUG=true
```

### Check Logs
```bash
# Follow logs in real-time
tail -f storage/logs/laravel.log | grep "Cloudflare DNS Configurator"

# Check specific log levels
tail -f storage/logs/laravel.log | grep -E "(ERROR|WARNING|INFO)"
```

### Common Issues
1. **Service provider not registered** - Check `config/app.php`
2. **Views not found** - Run `php artisan view:clear`
3. **Configuration not loaded** - Check `config/cloudflare.php` exists
4. **Database fields missing** - Run `php artisan migrate`

## 📦 Building for Production

### 1. Clean Development Files
```bash
# Remove development files
rm -rf .git
rm -rf tests/
rm -rf .github/
rm -rf docs/
rm DEVELOPMENT.md
rm QUICKSTART.md
```

### 2. Create Release Package
```bash
# Create ZIP for distribution
zip -r cloudflare-dns-configurator-v1.0.0.zip . -x "*.git*" "tests/*" ".github/*" "docs/*" "DEVELOPMENT.md" "QUICKSTART.md"
```

## 🤝 Contributing

### Before Submitting
- [ ] Code follows PSR-12 standards
- [ ] All tests pass
- [ ] Documentation updated
- [ ] No sensitive data in commits
- [ ] Feature tested manually

### Pull Request Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested manually
- [ ] All tests pass
- [ ] No new warnings

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
```

## 📖 Additional Resources

- [Pterodactyl Plugin Development](https://pterodactyl.io/community/customization/plugins.html)
- [Laravel Development](https://laravel.com/docs)
- [Cloudflare API Documentation](https://developers.cloudflare.com/api/)
- [PHP Standards](https://www.php-fig.org/psr/)

---

Happy coding! 🚀
