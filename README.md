# MVC Base Framework

A simple and lightweight PHP MVC framework for building web applications.

## Features

- **Lightweight and Fast**: Minimal overhead and fast performance
- **Simple Routing System**: Easy-to-use routing system with parameter support
- **Database Abstraction**: Built-in database layer with PDO
- **Template Engine**: Flexible view system with layout support
- **Security**: Built-in CSRF protection and input validation
- **Repository Pattern**: Clean data access layer
- **Model-View-Controller Architecture**: Clean separation of concerns

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

## Installation

1. **Clone or download the framework**
   ```bash
   git clone <repository-url> mvc-base
   cd mvc-base
   ```

2. **Configure your web server**
   - Point your document root to the `public` directory
   - Ensure mod_rewrite is enabled (for Apache)

3. **Set up the database**
   ```bash
   mysql -u root -p < database.sql
   ```

4. **Configure environment variables**
   - Copy `.env.example` to `.env` (if available)
   - Update database credentials and other settings in `.env`

5. **Set permissions**
   ```bash
   chmod 755 public/
   chmod 644 .env
   ```

## Configuration

### Environment Variables

Create a `.env` file in the root directory with the following variables:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=mvc_base
DB_USER=root
DB_PASS=

# Application Configuration
APP_NAME=MVC Base Framework
APP_URL=http://localhost/mvc_base
APP_DEBUG=true
APP_ENV=development

# Security
APP_KEY=your-secret-key-here

# Timezone
APP_TIMEZONE=Asia/Ho_Chi_Minh
```

### Database Configuration

The framework uses PDO for database operations. Update your database credentials in the `.env` file.

## Project Structure

```
mvc-base/
├── .env                              # Environment configuration
├── .htaccess                         # URL rewriting and security
├── config/
│   └── config.php                    # Configuration loader
├── core/                             # Framework core
│   ├── Router.php                    # Routing system
│   ├── Model.php                     # Base Model class
│   ├── Database.php                  # Database connection
│   └── View.php                      # View rendering
├── app/
│   ├── bootstrap.php                 # Application bootstrap
│   ├── controllers/                  # Controllers
│   ├── repositories/                 # Data access layer
│   ├── models/                       # Models
│   └── views/                        # Views and templates
├── routes/
│   └── web.php                       # Route definitions
└── public/                           # Public assets
    ├── index.php                     # Entry point
    ├── css/                          # Stylesheets
    ├── js/                           # JavaScript files
    └── images/                       # Images
```

## Usage

### Routing

Define routes in `routes/web.php`:

```php
// Basic routes
$router->get('/', 'HomeController@index');
$router->post('/users', 'UserController@store');

// Routes with parameters
$router->get('/users/{id}', 'UserController@show');

// Route groups
$router->group('/admin', function($router) {
    $router->get('/users', 'UserController@index');
    $router->post('/users', 'UserController@store');
});
```

### Controllers

Create controllers in `app/controllers/`:

```php
<?php
namespace App\Controllers;

class UserController extends BaseController
{
    public function index()
    {
        $users = User::all();
        return $this->view('users/index', ['users' => $users]);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        return $this->view('users/show', ['user' => $user]);
    }
}
```

### Models

Create models in `app/models/`:

```php
<?php
namespace App\Models;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}
```

### Views

Create views in `app/views/`:

```php
<!-- app/views/users/index.php -->
<h1>Users</h1>
<?php foreach ($users as $user): ?>
    <div class="user">
        <h3><?= htmlspecialchars($user->name) ?></h3>
        <p><?= htmlspecialchars($user->email) ?></p>
    </div>
<?php endforeach; ?>
```

### Database Operations

```php
// Using the Model
$user = User::find(1);
$user->name = 'New Name';
$user->save();

// Using the Repository
$userRepository = new UserRepository();
$users = $userRepository->getAll();
$user = $userRepository->findById(1);
```

## API Usage

The framework includes basic API functionality:

```php
// Return JSON response
return $this->json(['message' => 'Success', 'data' => $data]);

// API routes
$router->get('/api/users', 'UserController@api');
$router->get('/api/users/{id}', 'UserController@apiShow');
```

## Security Features

- **CSRF Protection**: Built-in CSRF token generation and validation
- **Input Validation**: Form validation with customizable rules
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: HTML escaping in views
- **Secure Headers**: Security headers in .htaccess

## Helper Functions

The framework provides several helper functions:

```php
// URL generation
url('/path')                    // Generate URL
asset('css/style.css')          // Generate asset URL

// View helpers
view('template', $data)         // Render view
redirect('/path')               // Redirect
back()                          // Redirect back

// Form helpers
csrf_field()                    // CSRF token field
old('field_name')               // Get old input value

// Utility functions
dd($data)                       // Dump and die
dump($data)                     // Dump data
config('key')                   // Get config value
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please open an issue on GitHub or contact the development team.

## Changelog

### Version 1.0.0
- Initial release
- Basic MVC structure
- Routing system
- Database abstraction
- View system
- User management
- Security features
