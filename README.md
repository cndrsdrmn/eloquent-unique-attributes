# Eloquent Unique Attributes

[![Packagist Version](https://img.shields.io/packagist/v/cndrsdrmn/eloquent-unique-attributes?label=stable)](https://packagist.org/packages/cndrsdrmn/eloquent-unique-attributes)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/cndrsdrmn/eloquent-unique-attributes/tests.yml?logo=github&label=CI)](https://github.com/cndrsdrmn/eloquent-unique-attributes/actions)
[![GitHub License](https://img.shields.io/github/license/cndrsdrmn/eloquent-unique-attributes)](https://github.com/cndrsdrmn/eloquent-unique-attributes/blob/master/LICENSE)

A Laravel package to enforce automatic generation of unique attribute values for Eloquent models, making it easy to maintain uniqueness without manual intervention.

---

## 🌟 Features

- Automatically generate unique attribute values for Eloquent models.
- Customizable formats (`numerify`, `lexify`, or mixed patterns).
- Flexible configuration for prefixes, suffixes, separators, and retries.
- Supports models with soft deletes.
- Fully customizable events for enforcing and notifying unique attribute changes.

---

## 📖 Background

Managing unique attributes in Eloquent models often involves tedious logic, especially when combining custom formats and real-time checks. This package provides an automatic, extensible solution. It ensures attributes are unique within the database and adhere to defined configurations.

---

## 🛠 Installation

Install the package using Composer:

```bash
composer require cndrsdrmn/eloquent-unique-attributes
```

---

## ⚙️ Configuration

To customize the default behavior, publish the configuration file:

```bash
php artisan vendor:publish --tag=unique-attributes-config
```

This will create a `config/unique_attributes.php` file:

```php
return [

    /**
     * The prefix to add to the beginning of the generated unique value.
     */
    'prefix' => '',

    /**
     * The suffix to add to the end of the generated unique value.
     */
    'suffix' => '',

    /**
     * The separator to use between prefix, value, and suffix.
     */
    'separator' => '_',

    /**
     * The maximum number of retries to generate a unique value before increasing the length.
     */
    'max_retries' => 100,

    /**
     * The default length of the generated unique value.
     */
    'length' => 8,

    /**
     * The format of the unique value.
     *
     * Supported options:
     * - `numerify` - Replace `#` with random digits.
     * - `lexify` - Replace `?` with random letters.
     * - `bothify` - Replace `#` with digits and `?` with letters.
     *
     * @see https://github.com/cndrsdrmn/php-string-formatter
     */
    'format' => 'bothify',
];
```

### Configurable Options:
- **`prefix`**: A string prepended to the generated value.
- **`suffix`**: A string appended to the generated value.
- **`separator`**: A separator between `prefix`, `value`, and `suffix`.
- **`max_retries`**: Maximum attempts to generate a unique value before increasing length.
- **`length`**: The initial length of the generated unique value.
- **`format`**: Determines the type of value (`numerify` for digits, `lexify` for letters, `botify` for mixed).

---

## 🚀 Usage

### 1. Add the Trait to Your Model

Include the `HasEnforcesUniqueAttributes` trait in any Eloquent model requiring unique attributes:

```php
namespace App\Models;

use Cndrsdrmn\EloquentUniqueAttributes\HasEnforcesUniqueAttributes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasEnforcesUniqueAttributes;

    /**
     * Define the unique attributes configuration.
     */
    public function enforcedUniqueColumns(): array
    {
        return [
            'barcode' => ['format' => 'numerify', 'separator' => '-'],
            'sku' => ['prefix' => 'SKU', 'length' => 10],
        ];
    }
}
```

### 2. Generate Unique Attributes

When creating the model, the unique values will automatically be enforced:

```php
$product = Product::create([
    'name' => 'Sample Product',
]);

echo $product->sku; // Example: SKU_1234567890
```

---

## 🔧 Advanced Customization

### Event Hooks
This package fires events during the unique value generation process:
- **`eloquent.enforcing`**: Fired before generating unique attributes. If a listener returns a value, the process of enforcing unique attributes is skipped.
- **`eloquent.enforced`**: After unique attributes are generated.

You can listen to these events in your application to implement custom logic:

```php
use Illuminate\Support\Facades\Event;

Event::listen('eloquent.enforcing: App\Models\Product', function ($model, $event) {
    // Returning any value here will skip the enforcement of unique attributes.
    return true;
});

Event::listen('eloquent.enforced: App\Models\Product', function ($model, $status) {
    // Execute logic after unique attributes are enforced.
    if ($status) {
        Log::info('Unique attributes enforced for model', ['model' => $model]);
    }
});
```
> **Note:** If a listener for the `eloquent.enforcing` event returns a value (truthy or falsy), the unique attribute generation process will be skipped for the current model.



---

## 🧪 Testing

This package includes PHPUnit tests. To run the tests, use the following command:

```bash
composer test
```

## 🤝 Contributing

We welcome contributions! Follow these steps to contribute:
1. Fork the repository.
2. Create a new branch (`git checkout -b feature-name`).
3. Commit your changes (`git commit -m "Add new feature"`).
4. Push to the branch (`git push origin feature-name`).
5. Open a Pull Request.

---

## 📄 License

This package is open-source software licensed under the [MIT license](./LICENSE).
