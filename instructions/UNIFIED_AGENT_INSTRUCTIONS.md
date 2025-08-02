# Unified Agent Instructions for MSJPay Project

## Overview
This document serves as the central guide for AI agents working on the project, a Laravel + Vue.js 3 system. It provides efficient instruction loading strategies to minimize token usage while maximizing development effectiveness.

## Project Architecture
- **Backend**: Laravel 11+ with Sanctum authentication
- **Frontend**: Vue.js 3 + Tailwind CSS (3 separate apps: Admin, Merchant, User)
- **Database**: MySQL/SQLite with comprehensive migrations
- **Additional**: Go-based robot processes for bank integration

## Instruction Loading Strategy

### 🚨 CRITICAL: Token Usage Optimization
**DO NOT load instruction files repeatedly in the same conversation.** Once loaded, reference the knowledge without re-reading files.

### When to Load Backend Instructions (`instructions/BACKEND_INSTRUCTIONS.md`)
Load **ONLY** when working on:
- Laravel controllers, models, or services
- Database migrations or seeders
- API endpoints and routing
- Queue jobs and background processing
- Authentication and authorization
- PHP-related configuration

**Example tasks requiring backend instructions:**
- "Create a new API endpoint for merchant transactions"
- "Fix the deposit callback processing"
- "Add validation to the withdrawal controller"
- "Create a new Eloquent model"

### When to Load Frontend Instructions (`instructions/FRONTEND_INSTRUCTIONS.md`)
Load **ONLY** when working on:
- Vue.js components or pages
- Tailwind CSS styling
- Frontend routing (Vue Router)
- State management (Pinia)
- UI/UX improvements
- JavaScript/TypeScript code

**Example tasks requiring frontend instructions:**
- "Create a new Vue component for transaction history"
- "Style the merchant dashboard with Tailwind"
- "Add form validation to the deposit page"
- "Implement responsive design for mobile"

### When to Load Both Instructions
Load **BOTH** only when working on:
- Full-stack features spanning both backend and frontend
- API integration between Laravel and Vue.js
- Authentication flows
- Complete CRUD operations

**Example tasks requiring both:**
- "Implement a complete user registration system"
- "Create a new payment gateway integration"
- "Build a comprehensive reporting dashboard"

## Efficient Usage Guidelines

### 1. Initial Assessment
Before loading any instructions, determine:
- Is this primarily a backend, frontend, or full-stack task?
- What specific technologies are involved?
- Do I need architecture context or specific implementation details?

### 2. Single Load Policy
- Load instruction files **ONCE** per conversation session
- Reference loaded knowledge without re-reading
- If additional context is needed, ask specific questions instead of reloading

### 3. Context-Aware Loading
```
GOOD: "I need to work on the merchant dashboard (Vue.js). Let me load frontend instructions."
BAD: "Let me load both backend and frontend instructions just in case."
```

### 4. Progressive Loading
Start with the most relevant instruction set, then load additional ones only if cross-stack work emerges.

## Project-Specific Context

### Key Entities
- **Users**: End customers face to public user
- **Merchants**: Businesses accepting payments  
- **Admins**: System administrators
- **Company Banks**: Banking institutions for processing
- **Transactions**: Wallet transactions (deposits, withdrawals, etc.)

### Core Business Logic
- Multi-level user management and permissions
- Real-time transaction monitoring
- Webhook-based callback systems

### Security Considerations
- All endpoints require authentication (Sanctum tokens)
- Role-based permissions (Admin/Merchant/User)
- Input validation on all forms and APIs
- CSRF protection enabled
- Sensitive data logging restrictions

## Common Development Patterns

### API Response Format
```json
{
  "success": true,
  "data": {},
  "message": "Operation successful"
}
```

### Frontend Component Structure
- Use Composition API (`<script setup>`)
- Implement proper TypeScript typing
- Follow Tailwind utility-first approach
- Use Pinia for state management

### Database Naming Conventions
- Snake_case for table and column names
- Plural table names (users, deposits, merchants)
- Foreign keys follow `{table}_id` pattern
- Use soft deletes where appropriate

## Development Workflow

### Before Starting Work
1. Identify the primary technology stack needed
2. Load the appropriate instruction file(s) **ONCE**
3. Review existing code patterns in the relevant directory
4. Check for existing tests and follow the same structure

### During Development
- Follow established patterns from loaded instructions
- Use existing components and utilities when possible
- Maintain consistency with current codebase style
- Implement proper error handling and validation

### After Implementation
- Test both happy path and error scenarios
- Ensure responsive design (for frontend changes)
- Verify API endpoints with proper status codes
- Update documentation if adding new features

## File Organization Reference

### Backend Structure
```
app/
├── Http/Controllers/Api/
│   ├── Admin/        # Admin-specific controllers
│   ├── Merchant/     # Merchant-specific controllers
│   └── User/         # User-specific controllers
├── Models/           # Eloquent models
├── Services/         # Business logic services
└── DataTables/       # DataTable classes
```

### Frontend Structure
```
resources/scripts/
├── admin/           # Admin application
├── merchant/        # Merchant application  
├── user/           # User application
└── shared/         # Common components and utilities
```

## Emergency Protocols

### If Instructions Seem Outdated
1. Check the actual codebase structure first
2. Look for recent commits or changes
3. Ask for clarification on current patterns
4. **DO NOT** reload instruction files - update your understanding based on current code

### If Cross-Stack Work Emerges
1. Complete current stack work first
2. Load additional instructions only if absolutely necessary
3. Maintain context from previously loaded instructions

## Translation System (Frontend & Backend Shared)

### 🚨 CRITICAL TRANSLATION RULES - MANDATORY FOR ALL DEVELOPMENT

#### Translation Key Formatting - NON-NEGOTIABLE
```json
// ✅ ALWAYS CORRECT - Pure English, sentence case
"How are you": "你好吗",
"Something went wrong": "出现了问题", 
"New payment amount x from y": "收到付款 :x 来自 :y",
"Email address": "电子邮件地址",
"Save changes": "保存更改",

// ❌ NEVER ACCEPTABLE - Title case or snake_case
"How Are You": "你好吗",           // WRONG: Title case
"Something_Went_Wrong": "出现了问题", // WRONG: snake_case  
"EMAIL_ADDRESS": "电子邮件地址",      // WRONG: ALL CAPS
"saveChanges": "保存更改",          // WRONG: camelCase
```

#### File Structure & Strategy
```
lang/
├── zh.json              # Chinese translations (ALL KEYS REQUIRED HERE)
├── en.json              # English (ONLY when significantly different)
├── zh_permission.json   # Admin permission names in Chinese
└── en_permission.json   # Admin permission names (usually empty)
```

#### English Translation Decision Tree
```json
// zh.json - MANDATORY for ALL keys
{
    // === SIMPLE TRANSLATIONS (same meaning) ===
    "Cancel": "取消",
    "Loading": "加载中",
    "Save": "保存",
    "Username": "用户名",
    "Email": "电子邮件地址",
    "Password": "密码",
    
    // === DIFFERENT CONCEPTS ===
    "Credit 1": "现金分",                // Different concept
    "Password2": "安全密码",             // Different concept  
    "Unit": "单位",                      // Different concept
    
    // === PARAMETERIZED TRANSLATIONS ===
    "Insufficient x": ":x 不足",
    "x not found": ":x不存在",
    "New payment amount x from y": "收到付款 :x 来自 :y",
    "Minimum of x characters": "最少 :x 个字符",
    "Please select at least x": "请选择最少 :x 项",
    "Welcome back, :name": "欢迎回来，:name"
}

// en.json - ONLY add when different concept/grammar
{
    // === DIFFERENT CONCEPTS ===
    "Credit 1": "Cash Point",           // ✅ ADD: Different concept
    "Password2": "Security PIN",        // ✅ ADD: Different concept
    "Unit": "Unit",                     // ✅ ADD: Same word but different usage context
    
    // === PARAMETERIZED TRANSLATIONS (different grammar) ===
    "Insufficient x": "Insufficient :x", // ✅ ADD: Different grammar structure
    "x not found": ":x not found",      // ✅ ADD: Different word order
    "New payment amount x from y": "New payment :x from :y", // ✅ ADD: Different structure
    "Minimum of x characters": "Minimum :x characters", // ✅ ADD: Different grammar
    "Please select at least x": "Please select at least :x", // ✅ ADD: Different structure
    "Welcome back, :name": "Welcome back, :name!", // ✅ ADD: Different punctuation/tone
    
    // ❌ DON'T ADD: Same meaning as Chinese
    // "Cancel": "Cancel",              // Same meaning - DON'T ADD
    // "Loading": "Loading",            // Same meaning - DON'T ADD  
    // "Save": "Save",                  // Same meaning - DON'T ADD
    // "Username": "Username",          // Same meaning - DON'T ADD
    // "Email": "Email",                // Same meaning - DON'T ADD
    // "Password": "Password"           // Same meaning - DON'T ADD
}
```

#### Frontend Usage (Vue.js)
```vue
<template>
    <!-- Always use $t() for ALL user-visible text -->
    <form-input :label="$t('Email address')" />
    <auto-button>{{ $t('Save changes') }}</auto-button>
    
    <!-- Parameterized translations -->
    <div>{{ $t('Welcome back, :name', { name: user.name }) }}</div>
    <div>{{ $t('Insufficient x', { x: 'Credit' }) }}</div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const $i18n = useI18n()

// Use t() for dynamic messages
const showError = (item) => {
    alert(t('x not found', { x: item }))
}
</script>
```

#### Backend Usage (Laravel)
```php
// Always use __() for user-facing messages
return makeResponse(false, __('Record not found'));
return makeResponse(true, __('Operation successful'));

// Parameterized translations
return makeResponse(false, __('x not found', ['x' => $username]));
return makeResponse(false, __('Insufficient x', ['x' => 'Credit']));
```

## Permission System (Admin Only)

### Permission Structure
- **Permission definitions**: Stored in `lang/zh_permission.json`
- **Route protection**: `adminHasPermission:Permission Name` middleware  
- **DataTable protection**: Each DataTable has `permissions()` method
- **Frontend checks**: `$helper.hasPermission($admin.value, 'Permission Name')`

### Permission Examples
```json
// lang/zh_permission.json
{
    "Manage admin": "管理员管理",
    "Manage deposit": "充值管理", 
    "Export": "下载",
    "Dashboard statistics": "首页数据"
}
```

### Usage Patterns

#### Route Protection
```php
// routes/api/admin.php
Route::post('/deposit/build_form', [DepositController::class, 'buildForm'])
    ->middleware('adminHasPermission:Manage deposit');
```

#### DataTable Protection  
```php
// app/DataTables/Admin/DepositDataTable.php
public function permissions(): array
{
    return ['Manage deposit'];
}
```

#### Frontend Permission Checks
```vue
<script setup>
const hasExportPermission = computed(() => {
    return $helper.hasPermission($admin.value, 'Export');
});
</script>

<template>
    <auto-button v-if="hasExportPermission" @click="onExport">
        {{ $t('Export') }}
    </auto-button>
</template>
```

#### Sidebar Menu Filtering
```javascript
// admin/side-bar.js - Menus automatically filtered by permissions
{
    name: 'Manage deposit', 
    route: 'admin.deposit.list', 
    permissions: ['Manage deposit']
},
```

## Final Reminders

✅ **DO:**
- Load instructions once per conversation
- Choose the most relevant instruction set
- Reference loaded knowledge efficiently
- Ask specific questions when unclear
- **ALWAYS use pure English sentence case for translation keys**
- **ALWAYS add ALL keys to zh.json first**
- **ONLY add to en.json when concepts/grammar differ significantly**
- **ORGANIZE translations**: Group by sections (Simple/Different Concepts/Parameterized)
- **INSERT in proper sections**: Don't append to end, find correct category

❌ **DON'T:**
- Load instruction files multiple times
- Load both backend and frontend "just in case"  
- Re-read files when you have questions
- Ignore token usage optimization
- **NEVER use Title Case or snake_case for translation keys**
- **NEVER add simple identical translations to en.json**
- **NEVER hardcode user-visible text without translations**
- **NEVER randomly append translations**: Always find the correct section
- **NEVER mix categories**: Keep Simple/Different Concepts/Parameterized separate

**Remember**: Efficiency in instruction loading leads to better development outcomes and cost optimization.
**Remember**: This guide is mandatory. Every rule here must be followed. When in doubt, be stricter rather than lenient. Always prioritize user experience, code maintainability, and consistency.
**Remember**: Translation keys are ALWAYS pure English sentence case. Permission system protects admin routes, DataTables, and frontend components.
