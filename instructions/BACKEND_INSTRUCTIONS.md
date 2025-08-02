# Laravel Backend Development Instructions

## Project Overview
Laravel-based system with Admin/Merchant/User or more roles.

**Key Directories**: `app/Http/Controllers/Api/`, `app/Models/`, `app/Services/`, `app/Helper.php`, `lang/*.json`

## CRITICAL Development Rules

### 1. Response Handling - ALWAYS use `makeResponse()`
```php
// From app/Helper.php - NEVER use response()->json()
return makeResponse(true, __('Operation success'), $data);    // Success (200)
return makeResponse(false, __('Validation failed'));          // Error (422)
return makeResponse(422, __('Custom error message'));         // Custom status
return makeResponseErrorForField('email', __('Email exists')); // Field error

// Error pattern
try {
    \DB::beginTransaction();
    // Business logic
    \DB::commit();
    return makeResponse(true, __('Operation successful'), $data);
} catch (Exception $e) {
    \DB::rollBack();
    return makeResponse(false, $e);
}
```

### 2. Validation - NEVER String Format, ALWAYS Array Format
```php
// ❌ NEVER: 'required|string|max:255'
// ✅ ALWAYS: ['required', 'string', 'max:255']

$rules = [
    'username' => ['required', 'isUsername'],           // Custom validation
    'email' => ['required', 'email', 'unique:users'],
    'amount' => ['required', 'isFund'],                 // Custom validation  
    'contact_number' => ['required', 'isContactNumber'], // Custom validation
    'status' => ['required', 'string', 'in:' . implode(',', array_keys(Model::getStatusLists()))],
];

$messages = [
    'username.required' => __('Username is required'), 
    'email.unique' => __('This email is already registered'),
];

$this->validate($request, $rules, $messages);
```

#### Custom Validation Rules - Add to `CustomValidationServiceProvider.php`
**For repeating validation patterns, create custom rules:**

```php
// In app/Providers/CustomValidationServiceProvider.php
Validator::extend('isNumber', function ($attribute, $value, $parameters) {
    return preg_match('/^\d+$/', $value);
});

Validator::extend('isUsername', function ($attribute, $value, $parameters) {
    return preg_match('/^[A-Za-z0-9_]{6,32}$/', $value);
});

Validator::extend('isFund', function ($attribute, $value, $parameters) {
    return (float) $value > 0;
});
```

**CRITICAL**: Update translation files in `lang/*/validation.php`:
```php
// lang/zh/validation.php - Add to custom validation section (line 138+)
'is_number' => '错误的号码',
'is_username' => '错误的用户名，用户名格式必须由 a-z,0-9或_ 下划线组成并且介于6至32字符',
'is_fund' => '错误的金额',

// lang/en/validation.php - Add to custom validation section
'is_number' => 'Incorrect number format',
'is_username' => 'Incorrect username format, username must be combination of a-z,0-9 or underscore(_) only, 6 to 32 characters',
'is_fund' => 'Invalid amount',
```

**Available Custom Rules:**
- `isNumber` - Digits only
- `isPercentage` - Decimal percentage  
- `isFund` - Positive amount
- `isContactNumber` - Phone number format
- `isUsername` - Username format (a-z,0-9,_)
- `isPassword` - Password length (6-24)
- `isPassword2` - Security PIN (6 digits)
- `isYesNo` - Boolean 0/1

### 3. Translation - ALWAYS use `__()`
**ALL user-facing messages MUST use `__()` function**

- **Format**: Sentence case (`"Add new address"` ✅, `"Add New Address"` ❌)
- **Strategy**: ALL keys in `zh.json`, only different concepts in `en.json`

```php
return makeResponse(false, __('Invalid email address'));
'email.required' => __('Email is required'),
```

#### Parameterized Translations - Use `:parameter` Syntax
**For dynamic content in translations, use Laravel's parameter replacement**

```php
// Translation keys in lang/*.json files
"Insufficient x": ":x 不足",                    // zh.json
"x not found": ":x不存在",                      // zh.json  
"New payment amount x from y": "收到付款 :x 来自 :y",  // zh.json
"Transaction transfer credit out": "转帐至 :to_username",  // zh.json

"Insufficient x": "Insufficient :x",            // en.json
"x not found": ":x not found",                  // en.json
"New payment amount x from y": "New payment :x from :y",  // en.json
"Transaction transfer credit out": "Transfer to :to_username",  // en.json
```

**Usage in PHP:**
```php
// Single parameter
return makeResponse(false, __('x not found', ['x' => 'User']));
// Output: "用户不存在" (zh) / "User not found" (en)

// Multiple parameters  
$message = __('New payment amount x from y', [
    'x' => '$100',
    'y' => 'Merchant ABC'
]);
// Output: "收到付款 $100 来自 Merchant ABC" (zh) / "New payment $100 from Merchant ABC" (en)

// Named parameters (recommended)
$message = __('Transaction transfer credit out', [
    'to_username' => $user->username
]);
// Output: "转帐至 john123" (zh) / "Transfer to john123" (en)
```

**CRITICAL Rules for Parametrized Translations:**
- Use `:parameter` format (NOT `:PARAMETER` or `{parameter}`)
- Keep parameter names descriptive (`to_username` not `user`)
- **ALWAYS** update both `zh.json` and `en.json` files
- Test both languages to ensure parameters work correctly

#### Reserved `params` Column Pattern - Dynamic Translation Data
**For models that need dynamic translation parameters, use a reserved `params` JSON column**

```php
// Model setup with params column
public function getCasts(): array {
    return [
        'transaction_type' => 'integer',
        'amount' => 'float',
        'related_key' => 'string',
        'params' => 'array',  // ✅ JSON column for dynamic translation data
    ];
}

// Store parent data in params for child model translations
$transaction = new UserCreditTransaction();
$transaction->transaction_type = 11;  // Transfer credit out
$transaction->amount = 100.00;
$transaction->params = [
    'to_username' => 'john123',         // For "Transaction transfer credit out"
    'merchant_order_id' => 'ORD-001',   // For settlement references
    'deposit_id' => $deposit->id,       // For deposit references
];
$transaction->save();
```

**Translation with Dynamic Parameters:**
```php
// Translation keys with parameters from parent models
"Transaction transfer credit out": "转帐至 :to_username",           // zh.json
"Transaction transfer credit out": "Transfer to :to_username",     // en.json

"Merchant Transaction settlement canceled": "结算取消（:merchant_order_id）",  // zh.json
"Merchant Transaction settlement canceled": "Settlement canceled",              // en.json
```

**Model Implementation - `explainTransactionType()` Method:**
```php
public static function getTransactionTypeLists($params = []) {
    return [
        11 => __('Transaction transfer credit out', $params),
        21 => __('Transaction withdraw', $params),
        31 => __('Merchant Transaction settlement canceled', $params),
    ];
}

public function explainTransactionType() {
    // Prepare default parameter structure
    $params = [
        'to_username' => '',
        'from_username' => '',
        'merchant_order_id' => '',
        'deposit_id' => '',
        'withdrawal_id' => '',
    ];
    
    // Merge with stored params from database
    if ($this->params) {
        $params = array_merge($params, $this->params);
    }
    
    // Return translated string with parameters
    return static::getTransactionTypeLists($params)[$this->transaction_type] ?? __('Unknown');
}

// Auto-include in API responses
public function toArray() {
    $arr = parent::toArray();
    $arr['transaction_type_explained'] = $this->explainTransactionType();
    return $arr;
}
```

**Usage in Controllers:**
```php
public function createTransfer(Request $request) {
    try {
        \DB::beginTransaction();
        
        // Create outgoing transaction with recipient info in params
        $outTransaction = new UserCreditTransaction();
        $outTransaction->user_id = $request->input('from_user_id');
        $outTransaction->transaction_type = 11; // Transfer out
        $outTransaction->amount = -$request->input('amount');
        $outTransaction->params = [
            'to_username' => $toUser->username,  // ✅ Store for translation
        ];
        $outTransaction->save();
        
        // Create incoming transaction with sender info in params
        $inTransaction = new UserCreditTransaction();
        $inTransaction->user_id = $request->input('to_user_id');
        $inTransaction->transaction_type = 12; // Transfer in
        $inTransaction->amount = $request->input('amount');
        $inTransaction->params = [
            'from_username' => $fromUser->username,  // ✅ Store for translation
        ];
        $inTransaction->save();
        
        \DB::commit();
        return makeResponse(true, __('Transfer completed successfully'));
        
    } catch (\Exception $e) {
        \DB::rollBack();
        return makeResponse(false, $e);
    }
}
```

**Database Schema:**
```php
// Migration - Add params column to models needing dynamic translations
Schema::table('user_credit_transactions', function (Blueprint $table) {
    $table->json('params')->nullable()->default(null);  // ✅ JSON column for translation parameters
});

Schema::table('merchant_credit_transactions', function (Blueprint $table) {
    $table->json('params')->nullable()->default(null);  // ✅ Same pattern
});
```

**When to Use `params` Column:**
- ✅ Child models that reference parent data in translations
- ✅ Transaction records needing dynamic user/order references
- ✅ Audit logs with contextual information
- ✅ Any model where translation needs data from related models
- ❌ Simple static translations without parameters
- ❌ Direct parent-child relationships (use foreign keys instead)

### 4. BaseModel Magic Methods - CRITICAL Features

#### Enum Methods: `getXXXXLists()` & `explainXXXX()`
```php
// Define in models (MUST be static)
public static function getStatusLists()
{
    return [
        0 => __('Inactive'),
        1 => __('Active'),
        2 => __('Processing'),
    ];
}

// Usage
$model = new CompanyBank();
$model->status = 1;           // ✅ Valid - auto-validated
$model->status = 999;         // ❌ Throws "Invalid attribute Status"

// Magic explain methods automatically available
echo $model->explainStatus(); // "Active" (translated)

// In API responses
'status_label' => $model->explainStatus()
```

#### Multilingual Methods: `translateYYYYY()`
```php
// Database: field_en, field_zh columns
// Model setup
class Article extends BaseModel {
    use MultiLanguage;
    public function multiLanguageColumns() { return ['title', 'content']; }
}

// Usage
echo $article->translateTitle();   // Returns title_en or title_zh based on locale
```

### 5. Model Operations - NEVER Mass Assignment

#### Type Casting - CRITICAL
```php
// ✅ ALWAYS use 'float' for money, NEVER 'decimal'
public function getCasts(): array
{
    return [
        'amount' => 'float',              // ✅ NOT 'decimal'
        'transaction_fees' => 'float',    // ✅ NOT 'decimal'
        'user_id' => 'integer',
        'is_active' => 'boolean',
    ];
}
```

#### CRUD Operations - Explicit Assignment Only
```php
// ❌ NEVER: User::create([...]) or $user->update([...])
// ✅ ALWAYS: Explicit assignment with transactions

public function submitForm(Request $request)
{
    $rules = ['username' => ['required', 'string', 'max:255']];
    $this->validate($request, $rules);
    
    try {
        \DB::beginTransaction();
        
        // Create or Update (determined by ID presence)
        if ($request->filled('id')) {
            $user = User::find($request->get('id'));
            if (!$user) throw new \Exception(__('Record not found'));
        } else {
            $user = new User();
        }
        
        // Explicit assignment (NEVER mass assignment)
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->save();
        
        \DB::commit();
        return makeResponse(true, __('Operation successful'), ['user' => $user]);
        
    } catch (\Exception $e) {
        \DB::rollBack();
        return makeResponse(false, $e);
    }
}
```

#### Multilingual Fields - Use Helper
```php
// Generate validation rules
foreach ((new Article)->multiLanguageColumns() as $column) {
    foreach (loopLanguageForColumn($column) as $c) {
        $rules[$c['column']] = ['required', 'string', 'max:255'];
    }
}

// Assign values
foreach ((new Article)->multiLanguageColumns() as $column) {
    foreach (loopLanguageForColumn($column) as $c) {
        $article->{$c['column']} = $request->get($c['column']);
    }
}
```

### 6. API Routing - Use `buildForm`/`submitForm` Pattern

**NEVER use Laravel defaults (`store`, `update`, `create`, `edit`)**

```php
// Route structure
Route::group(['prefix' => 'management'], function () {
    Route::post('/admin/build_form', [AdminController::class, 'buildForm']);
    Route::post('/admin/submit_form', [AdminController::class, 'submitForm']);
    Route::post('/admin/delete', [AdminController::class, 'delete']);
});

// Controller methods
public function buildForm(Request $request)
{
    $data = [];
    
    // Load existing for edit
    if ($request->filled('id')) {
        $data['model'] = YourModel::find($request->get('id'));
    }
    
    // Load related data
    $data['categories'] = Category::all();
    $data['statuses'] = YourModel::getStatusLists();
    
    return makeResponse(true, null, $data);
}
```

**Route Naming**: 
- Paths: `/{prefix}/{module}/{action}` using snake_case
- Modules: Singular (`admin`, `merchant`, `deposit`)
- Actions: `build_form`, `submit_form`, `delete`

### 7. Laravel Traits - Essential Features

#### File Upload - `UploadFile` Trait
**For models with file/image fields**

```php
use App\Traits\UploadFile;

class YourModel extends BaseModel {
    use UploadFile;
    
    // CRITICAL: Define file upload settings
    public function fileColumns() {
        return [
            'avatar' => [
                'path' => '/user/avatar/',
                'rename' => 'id',           // ✅ For frequently updated files
                'width' => 200,
                'height' => 200,
            ],
            'receipt' => [
                'path' => '/transaction/receipt/',
                'rename' => 'random',       // ✅ For permanent files
            ],
        ];
    }
}

// Usage in controllers
$model->avatar = $request->file('avatar');  // Auto-handles upload
$model->save();
```

**When to use `rename = 'id'`**: 
- **User avatars, merchant logos** - files that update frequently
- **Benefits**: No old file cleanup, auto cache-busting with `?t=timestamp`
- **CRITICAL**: Must `$model->save()` first to get ID before file upload with $model->receipt = $request->file('receipt');

**When to use `rename = 'random'`**:
- **Receipts, documents** - permanent files that rarely change
- **Benefits**: Unique filenames, safe for concurrent uploads

#### Caching - `HasAllCache` Trait
**ONLY for data that APIs always load but seldom update (categories, settings, countries)**

```php
use App\Traits\HasAllCache;

class Category extends BaseModel {
    use HasAllCache;
    
    // Optional: Custom cache query
    public function cacheAllQuery() {
        return $this->where('is_active', 1)->orderBy('sorting');
    }
    
    // CRITICAL: Manual cache management methods
    public static function forgetCache() {
        $instance = new static;
        cache()->forget($instance->getAllCacheKey());
    }
    
    public static function buildCache() {
        return static::loadAllFromCache();
    }
}

// Usage - ALWAYS use this instead of Model::all()
$categories = Category::loadAllFromCache();  // ✅ Cached
$categories = Category::all();               // ❌ No cache
```

**CRITICAL Cache Management in Controllers:**
```php
public function submitForm(Request $request) {
    try {
        \DB::beginTransaction();
        // ... CRUD operations ...
        \DB::commit();
        
        // ALWAYS clear and rebuild cache after changes
        Category::forgetCache();
        Category::buildCache();
        
        return makeResponse(true, __('Operation successful'));
    } catch (\Exception $e) {
        \DB::rollBack();
        return makeResponse(false, $e);
    }
}

public function delete(Request $request, $id) {
    // ... delete logic ...
    
    // ALWAYS clear and rebuild cache after deletion
    Category::forgetCache();
    Category::buildCache();
    
    return makeResponse(true, __('Record deleted'));
}
```

**⚠️ WARNING**: Only use for reference data that rarely changes but is frequently accessed by APIs

#### Audit Logging - `HasAuditTrail` Trait
**For models requiring admin operation tracking**

```php
use App\Traits\HasAuditTrail;

class Merchant extends BaseModel {
    use HasAuditTrail;
    
    // REQUIRED: Description for audit logs
    public function getAuditTrailDescription(): string {
        return "Merchant #{$this->id} ({$this->business_name})";
    }
}

// Auto-logs when IN_ADMIN constant is true
// Tracks: created/updated/deleted with admin info + data changes
```

#### Contact Numbers - `HasContactNumber` Trait
**For models with international phone numbers**

```php
use App\Traits\HasContactNumber;

class User extends BaseModel {
    use HasContactNumber;
    
    // Database fields: contact_country_id, contact_number, full_contact_number
    // Auto-formats: MY (+60), SG (+65), ID (+62)
}

// Usage
$user->contact_country_id = 1;  // Malaysia
$user->contact_number = '123456789';
// Automatically creates: full_contact_number = '+60123456789'
echo $user->getContactNumber();  // '+60123456789'
```

#### File Galleries - `HasGalleries` Trait
**For models needing multiple file attachments**

```php
use App\Traits\HasGalleries;

class Product extends BaseModel {
    use HasGalleries;
}

// Usage
$product->attach($request->file('image'), ['group' => 'gallery', 'key' => 'main']);
$galleries = $product->attachmentsGroup('gallery');
$mainImage = $product->gallery('uuid-here');
```

### 8. Data Relationship Patterns

#### Foreign Keys vs Related Keys - Choose the Right Approach

**Use Foreign Keys (`user_id`, `order_id`) when:**
- Direct parent-child relationships (User → Orders)
- Frequent JOIN queries needed
- Database integrity constraints required
- Simple one-to-many or many-to-many relationships

**Use Related Keys (UUID `related_key`) when:**
- Complex multi-table business transactions
- Grouping related operations across multiple models
- Avoiding wide tables with too many foreign key columns
- Manual data tracing and debugging needs
- Bulk operations that span multiple tables

#### Related Key Pattern - UUID-based Relationship Tracing

```php
// Example: Sales transaction creating multiple related records
public function processSale(Request $request) {
    try {
        \DB::beginTransaction();
        
        // 1. Generate UUID for this business operation
        $relatedKey = \Str::uuid();
        
        // 2. Create main sales record
        $sale = new Sale();
        $sale->related_key = $relatedKey;
        $sale->amount = $request->input('amount');
        $sale->save();
        
        // 3. Create related orders (no need for sale_id foreign key)
        foreach ($request->input('items') as $item) {
            $order = new Order();
            $order->related_key = $relatedKey;  // ✅ Same UUID
            $order->product_id = $item['product_id'];
            $order->quantity = $item['quantity'];
            $order->save();
        }
        
        // 4. Create transaction records
        $transaction = new UserCreditTransaction();
        $transaction->related_key = $relatedKey;  // ✅ Same UUID
        $transaction->user_id = $request->input('user_id');
        $transaction->amount = $sale->amount;
        $transaction->transaction_type = 'SALE';
        $transaction->save();
        
        // 5. Create audit log
        $auditLog = new SaleAuditLog();
        $auditLog->related_key = $relatedKey;  // ✅ Same UUID
        $auditLog->operation = 'SALE_COMPLETED';
        $auditLog->save();
        
        \DB::commit();
        return makeResponse(true, __('Sale processed successfully'));
        
    } catch (\Exception $e) {
        \DB::rollBack();
        return makeResponse(false, $e);
    }
}
```

#### Querying Related Key Data

```php
// Find all records related to a specific business operation
$relatedKey = '550e8400-e29b-41d4-a716-446655440000';

// Manual tracing (for debugging/scripts)
$sales = Sale::where('related_key', $relatedKey)->get();
$orders = Order::where('related_key', $relatedKey)->get();
$transactions = UserCreditTransaction::where('related_key', $relatedKey)->get();
$auditLogs = SaleAuditLog::where('related_key', $relatedKey)->get();

// Or create helper method for bulk retrieval
public function getRelatedData($relatedKey) {
    return [
        'sales' => Sale::where('related_key', $relatedKey)->get(),
        'orders' => Order::where('related_key', $relatedKey)->get(),
        'transactions' => UserCreditTransaction::where('related_key', $relatedKey)->get(),
        'audit_logs' => SaleAuditLog::where('related_key', $relatedKey)->get(),
    ];
}
```

#### Database Schema Considerations

```php
// Migration example - Add related_key to tables that need grouping
Schema::table('sales', function (Blueprint $table) {
    $table->uuid('related_key')->nullable()->index();  // ✅ Indexed for performance
});

Schema::table('orders', function (Blueprint $table) {
    $table->uuid('related_key')->nullable()->index();  // ✅ Same pattern
    // No need for sale_id foreign key
});

Schema::table('user_credit_transactions', function (Blueprint $table) {
    $table->uuid('related_key')->nullable()->index();  // ✅ Same pattern
});
```

#### When to Use Each Approach

| Scenario | Use Foreign Key | Use Related Key |
|----------|----------------|----------------|
| User has many Orders | ✅ `orders.user_id` | ❌ |
| Order belongs to Product | ✅ `orders.product_id` | ❌ |
| Complex sale with orders + transactions + logs | ❌ | ✅ `related_key` |
| Deposit creates multiple financial records | ❌ | ✅ `related_key` |
| Simple parent-child with frequent JOINs | ✅ Foreign key | ❌ |
| Multi-step business process tracing | ❌ | ✅ `related_key` |

### 9. Model Structure Requirements
```php
<?php
namespace App\Models;

use App\Traits\{MultiLanguage, UploadFile, HasAllCache};
use Illuminate\Database\Eloquent\SoftDeletes;

class YourModel extends BaseModel  // ALWAYS extend BaseModel
{
    use SoftDeletes, MultiLanguage, UploadFile, HasAllCache;  // As needed
    
    // Type casting
    public function getCasts(): array {
        return [
            'related_key' => 'string',  // ✅ Include if using related key pattern
            'user_id' => 'integer',     // ✅ Traditional foreign keys
        ];
    }
    
    // File upload settings (if using UploadFile)
    public function fileColumns() {
        return [
            'avatar' => ['path' => '/model/avatar/', 'rename' => 'id'],
        ];
    }
    
    // Traditional relationships (when appropriate)
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    // Related key queries (when using UUID pattern)
    public function getRelatedRecords($relatedKey) {
        return static::where('related_key', $relatedKey)->get();
    }
    
    // Enum lists (if needed)
    public static function getStatusLists() {
        return [0 => __('Inactive'), 1 => __('Active')];
    }
    
    // Multilingual columns (if needed)
    public function multiLanguageColumns() {
        return ['title', 'content'];
    }
}
```

## CRITICAL Rules Checklist

**NEVER**:
- ❌ Use string validation format (`'required|string'`)
- ❌ Use mass assignment (`create([])`, `update([])`) 
- ❌ Use `'decimal'` casting for money
- ❌ Skip database transactions
- ❌ Use Laravel's default CRUD methods (`store`, `update`)
- ❌ Skip `__()` for user messages
- ❌ Use `related_key` for simple parent-child relationships (use foreign keys instead)
- ❌ Forget to index `related_key` columns when using UUID pattern

**ALWAYS**:
- ✅ Use array validation format (`['required', 'string']`)
- ✅ Use custom validation rules for repeating patterns (`isUsername`, `isFund`, etc.)
- ✅ Update both `CustomValidationServiceProvider.php` and `lang/*/validation.php` for new custom rules
- ✅ Use explicit assignment (`$model->field = $value`)
- ✅ Use `'float'` casting for monetary values
- ✅ Wrap database operations in transactions with try-catch
- ✅ Use `makeResponse()` helper for API responses
- ✅ Use `__()` function for all user-facing messages
- ✅ Extend `BaseModel` for all models
- ✅ Use `buildForm`/`submitForm` routing pattern
- ✅ Use `loopLanguageForColumn()` for multilingual fields
- ✅ Use `explainXXXX()` and `translateYYYYY()` magic methods in responses
- ✅ Include authentication (`auth:sanctum`) and permission middleware
- ✅ Use `Category::loadAllFromCache()` instead of `Category::all()` for reference data
- ✅ For file uploads: `rename = 'id'` for frequently updated files, `rename = 'random'` for permanent files
- ✅ **CRITICAL**: Call `Model::forgetCache()` and `Model::buildCache()` in CRUD controllers when using `HasAllCache`
- ✅ Use `related_key` (UUID) for complex multi-table business operations, foreign keys for simple relationships
- ✅ Generate `related_key` with `\Str::uuid()` and share across all related models in same business operation
- ✅ Index `related_key` columns for performance (`$table->uuid('related_key')->nullable()->index()`)
- ✅ Use `params` JSON column for storing dynamic translation parameters from parent models
- ✅ Cast `params` as `'array'` in model `getCasts()` method
- ✅ Include `explainXXXX()` methods in `toArray()` for automatic API response translation
