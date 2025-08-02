# Frontend Instructions for MSJPay Project

## Project Overview
Frontend is built with **Vue.js 3** (Composition API) + **Tailwind CSS**, structured as multiple applications:
- **Admin** (`resources/scripts/admin/`) - Administration panel
- **Merchant** (`resources/scripts/merchant/`) - Merchant dashboard
- **User** (`resources/scripts/user/`) - User interface

## 🚨 CRITICAL RULES FOR PAGE CREATION

### ✅ DO - Page Creation Guidelines

1. **Always Follow Existing Patterns**
    - Study existing pages in the same directory before creating new ones
    - Copy structure from similar pages (list-page.vue or model-form.vue)
    - Maintain consistent naming: `list-page.vue`, `model-form.vue`, `specific-name-page.vue`

2. **Reuse Auto* Components**
   ```vue
   // Always use these components - NEVER create custom alternatives
   <auto-datatable />     // For data tables
   <auto-modal />         // For modal dialogs
   <auto-button />        // For all buttons
   <form-input />         // For text inputs
   <form-select />        // For dropdowns
   <form-password />      // For password fields
   ```

3. **Standard Page Structure**
   ```vue
   <template>
       <div class="w-full">
           <!-- Content here -->
       </div>
   </template>

   <script setup>
   // Imports
   import AutoDatatable from '#/shared/components/auto-datatable.vue';
   import { useI18n } from 'vue-i18n';
   // ... other imports

   // Composables
   const $i18n = useI18n();
   const $helper = inject('$helper');

   // State and logic
   </script>
   ```

4. **i18n Translation Rules - MANDATORY Usage Patterns**
   ```vue
   <template>
       <!-- ✅ ALWAYS use $t() in templates -->
       <h1>{{ $t('Page title') }}</h1>
       <form-input :label="$t('Username')" />
       <auto-button>{{ $t('Submit') }}</auto-button>
   </template>

   <script setup>
   import { useI18n } from 'vue-i18n';
   
   // ✅ ALWAYS declare as $i18n (not destructured)
   const $i18n = useI18n();
   
   // ✅ ALWAYS use $i18n.t() in script setup
   const showError = (message) => {
       alert($i18n.t('Error occurred') + ': ' + message);
   };
   
   const dynamicOptions = computed(() => [
       { value: 'option1', text: $i18n.t('Option 1') },
       { value: 'option2', text: $i18n.t('Option 2') }
   ]);
   
   // ❌ NEVER destructure useI18n in script setup
   // const { t } = useI18n();          // WRONG
   // const { t: $t } = useI18n();      // WRONG
   </script>
   ```

   **Translation Usage Rules:**
    - **Template**: Always use `$t('Translation key')`
    - **Script setup**: Always use `$i18n.t('Translation key')`
    - **Composable declaration**: Always use `const $i18n = useI18n();`
    - **NEVER destructure** `useI18n()` in script setup
    - **Translation keys**: Always use sentence case English (`'User not found'`, not `'user_not_found'`)

5. **Admin Full-Page Layout - MANDATORY Structure**
   ```vue
   <template>
       <div class="w-full">
           <admin-content-card>
               <template #title>
                   <h1 class="text-2xl font-bold">
                       {{ $t('Page Title') }}
                   </h1>
               </template>
               
               <!-- Page content here -->
               <div v-if="formReady" class="w-full flex flex-col space-y-6">
                   <!-- Form sections or other content -->
               </div>
               
               <!-- Submit/Action buttons - MANDATORY grid layout -->
               <div class="grid lg:grid-cols-2 lg:gap-2 pt-4">
                   <auto-button type="plain" @click="$router.back()">
                       {{ $t('Back') }}
                   </auto-button>
                   <auto-button @click="onSubmit">
                       {{ isCreate ? $t('Create') : $t('Update') }}
                   </auto-button>
               </div>
           </admin-content-card>
       </div>
   </template>

   <script setup>
   import AdminContentCard from "#/admin/components/admin-content-card.vue";
   // ... other imports
   </script>
   ```

   **Admin Layout Rules:**
    - **ALWAYS wrap** full-page content with `<admin-content-card>`
    - **ALWAYS include** page title in `#title` slot
    - **NEVER put** Back button at the top - it should be side-by-side with action button
    - **MANDATORY**: Back and primary action buttons use `lg:grid lg:grid-cols-2 lg:gap-2`
    - **Primary button text**: Use conditional text - `Create` vs `Update` vs `Submit`
    - **Import**: Always import `AdminContentCard` component

### ❌ DON'T - Page Creation Anti-Patterns

1. **Never Create Custom Data Tables** - Always use `<auto-datatable>`
2. **Never Create Custom Form Components** - Use existing `form-*` components
3. **Never Create Custom Buttons** - Always use `<auto-button>`
4. **Never Create Custom Modals** - Always use `<auto-modal>`
5. **Never Skip i18n** - Always use `$t()` for text labels

## AutoDataTable Component Usage

### Basic Structure
```vue
<auto-datatable
    ref="dt"
    model="ModelName"
    :search-filters="searchFilters"
    :headers="headers"
    :hidden-data="hiddenData"
    @loaded="onDataLoaded"
>
    <template #tools="{busy}">
        <!-- Action buttons here -->
    </template>
    
    <template #default="{records}">
        <!-- Table rows here -->
    </template>
</auto-datatable>
```

### Search Filters Configuration
Based on **BaseDataTable.php filter patterns**, use these filter key formats:

**🚨 IMPORTANT: AutoDataTable automatically adds `f-date-from-created_at` and `f-date-to-created_at` filters. NEVER add these manually.**

#### Filter Key Patterns
```javascript
const searchFilters = [
    // 1. Simple LIKE search
    { label: $i18n.t('Username'), type: 'text', name: 'f-like-username' },
    
    // 2. Relationship LIKE search  
    { label: $i18n.t('Merchant'), type: 'text', name: 'f-has-like-merchant-username' },
    
    // 3. Exact relationship match
    { label: $i18n.t('Group'), type: 'text', name: 'f-has-group-group_name' },
    
    // 4. Exact column match
    { label: $i18n.t('Status'), type: 'select', name: 'f-status', options: enumOptions },
    
    // 5. Enum-based select filters
    { label: $i18n.t('Bank'), type: 'select', name: 'f-bank_code', 
      options: $helper.getEnumOptions('CompanyBank.bank_code') },
    
    // ❌ NEVER ADD: created_at date filters (automatically added)
    // { type: 'date-picker', name: 'f-date-from-created_at' }, // DON'T ADD
    // { type: 'date-picker', name: 'f-date-to-created_at' },   // DON'T ADD
];
```

#### Filter Backend Mapping (BaseDataTable.php patterns):
- `f-like-{column}` → `WHERE {column} LIKE '%value%'`
- `f-has-like-{relation}-{column}` → `whereHas('{relation}', fn($q) => $q->where('{column}', 'LIKE', '%value%'))`
- `f-has-{relation}-{column}` → `whereHas('{relation}', fn($q) => $q->where('{column}', '=', value))`
- `f-{column}` → `WHERE {column} = value`
- `f-date-from-{column}` → `WHERE {column} >= 'date 00:00:00'` (created_at handled automatically)
- `f-date-to-{column}` → `WHERE {column} <= 'date 23:59:59'` (created_at handled automatically)

### Headers Configuration
```javascript
const headers = [
    { label: '#', column: 'id' },
    { label: $i18n.t('Actions'), column: 'actions', sortable: false },
    { label: $i18n.t('Username'), column: 'username' },
    { label: $i18n.t('Amount'), column: 'amount', sum: true, decimal: 2 }, // Enables sum in footer
    { label: $i18n.t('Status'), column: 'status', export_column: 'status_explained' }, // Use different column for export
    { label: $i18n.t('Created at'), column: 'created_at' },
];
```

### Common Table Actions
```vue
<template #tools="{busy}">
    <auto-button
        :busy="busy"
        :full="false"
        @click="onFormOpen(null)"
    >
        <font-awesome-icon icon="fas fa-plus" />
        <span>{{ $t('Create') }}</span>
    </auto-button>
</template>

<template #default="{records}">
    <tr v-for="(record, index) in records" :key="record.id">
        <td>{{ index + 1 }}</td>
        <td class="flex justify-start items-center space-x-2">
            <auto-button small :full="false" @click="onFormOpen(record)">
                <font-awesome-icon icon="fas fa-pencil" />
            </auto-button>
            <auto-button type="danger" small :full="false" @click="onDelete(record)">
                <font-awesome-icon icon="fas fa-trash" />
            </auto-button>
        </td>
        <!-- Data columns -->
        <td>{{ record.username }}</td>
        <td>{{ $helper.fundFormat(record.amount) }}</td>
        <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
    </tr>
</template>
```

## 🚨 CRITICAL: build_form Usage Rules

### ✅ WHEN TO CALL build_form
**ONLY call build_form endpoint in these scenarios:**

1. **Form Pages (form-page.vue or model-form.vue)** - To load form data and dropdown options
2. **Edit Operations** - When editing existing records to populate form fields
3. **Form Initialization** - To get dropdown options (countries, categories, etc.) for forms

### ❌ NEVER CALL build_form
**NEVER call build_form endpoint in these scenarios:**

1. **List Pages (list-page.vue)** - DataTable handles its own data loading
2. **Dashboard Pages** - Use specific dashboard endpoints
3. **Filter Options** - Use domain enums or specific filter endpoints instead

### Correct Usage Examples

#### ✅ CORRECT - Form Page Usage
```vue
<!-- form-page.vue or model-form.vue -->
<script setup>
onMounted(async () => {
    // ✅ CORRECT: Load form data and dropdown options
    const data = await axios.post('/property/developer/build_form', { 
        id: $route.params.id || '' 
    });
    
    // Load form fields
    if (data.model) {
        defaultData.value = { /* populate form fields */ };
    }
    
    // Load dropdown options
    countryOptions.value = data.countries.map(country => ({
        value: String(country.id),
        text: country.name_en
    }));
});
</script>
```

#### ❌ WRONG - List Page Usage
```vue
<!-- list-page.vue -->
<script setup>
onMounted(async () => {
    // ❌ WRONG: Don't call build_form in list pages
    const response = await axios.post('/property/developer/build_form');
    countryOptions.value = response.data.countries; // DON'T DO THIS
});
</script>
```

#### ✅ CORRECT - List Page Pattern
```vue
<!-- list-page.vue -->
<script setup>
// ✅ CORRECT: Use domain enums for filters
:search-filters="[
    { label: $t('Status'), type: 'select', options: $helper.getEnumOptions('Developer.status') },
    { label: $t('Country'), type: 'text', name: 'f-has-like-country-name_en' },
]"

// ✅ CORRECT: DataTable loads its own data automatically
const dt = ref();
// No onMounted needed for list pages
</script>
```

### Filter Options Strategy

#### For List Pages:
- **Enum filters**: Use `$helper.getEnumOptions('Model.field')`
- **Text filters**: Use `f-like-field` or `f-has-like-relation-field` patterns
- **Date filters**: Use `f-date-from-field` and `f-date-to-field` patterns

#### For Form Pages:
- **Dropdown options**: Load via `build_form` endpoint
- **Dynamic options**: Use `build_form` to get related data

### Backend build_form Endpoint Pattern
```php
public function buildForm(Request $request)
{
    $data = [];
    
    // Load existing model for editing
    if ($request->filled('id')) {
        $data['model'] = Model::find($request->get('id'));
    }
    
    // Load dropdown options for forms
    $data['countries'] = Country::all();
    $data['categories'] = Category::all();
    
    return makeResponse(true, null, $data);
}
```

**Remember**: build_form is specifically for FORM data preparation, NOT for list page filtering!

## Domain Enums System

### Auto-Generated Enums
The system generates `resources/scripts/domain-enums.js` from Laravel models using:
```bash
php artisan generate:domain_enum
```

### Using Enums in Components
```javascript
// Get enum options for select dropdowns
const statusOptions = $helper.getEnumOptions('Deposit.status');
const bankOptions = $helper.getEnumOptions('CompanyBank.bank_code');

// In search filters
{ 
    label: $i18n.t('Status'), 
    type: 'select', 
    name: 'f-status', 
    options: $helper.getEnumOptions('Deposit.status') 
},
```

### Enum Access Patterns
```javascript
// Direct access
import DomainEnums from '#/domain-enums.js';
const statuses = DomainEnums.Deposit.status;

// Helper method (recommended)
const statusOptions = $helper.getEnumOptions('Deposit.status');
// Returns: [{ value: 'DEPOSIT_PENDING', text: 'Pending' }, ...]
```

## CRUD Form Strategy: Modal vs. New Page

### 🚨 CRITICAL: When to Use Modal vs. New Route

#### ✅ Use Modal When:
1. **Short forms** (≤ 6-8 fields)
2. **Simple data entry** (basic text, select, date inputs)
3. **Quick edits** that don't require extensive validation
4. **Common CRUD operations** (admin management, settings, etc.)
5. **Form fits comfortably** in modal without scrolling

#### ✅ Use New Route When:
1. **Long forms** (> 8 fields)
2. **Complex forms** with multiple sections, file uploads, rich text
3. **Multi-step processes** or wizards
4. **Forms requiring extensive validation** or real-time feedback
5. **Better UX on mobile** when form needs full screen space

### Modal Implementation Pattern
```vue
<!-- In list-page.vue -->
<template>
    <div class="w-full">
        <auto-datatable ref="dt" model="ModelName">
            <template #tools="{busy}">
                <auto-button @click="onFormOpen(null)">
                    {{ $t('Create') }}
                </auto-button>
            </template>
        </auto-datatable>
        
        <!-- Modal for Create/Update -->
        <auto-modal v-if="formOpened" @close="onFormClose">
            <model-form 
                :model="model" 
                @close="onFormClose" 
                @success="onFormClose" 
            />
        </auto-modal>
    </div>
</template>

<script setup>
// Standard modal handlers
const model = ref(null);
const formOpened = ref(false);

const onFormOpen = (record = null) => {
    model.value = record; // null for Create, object for Update
    formOpened.value = true;
};

const onFormClose = () => {
    model.value = null;
    formOpened.value = false;
    dt.value.fetchData(); // Refresh table
};
</script>
```

### New Route Implementation Pattern
```vue
<!-- Router setup -->
// routes/admin.js
{
    path: '/management/admin/create',
    name: 'admin.management.admin.create',
    component: () => import('@/pages/management/admin/form-page.vue')
},
{
    path: '/management/admin/:id/edit', 
    name: 'admin.management.admin.edit',
    component: () => import('@/pages/management/admin/form-page.vue')
}
```

```vue
<!-- In list-page.vue - Navigate instead of modal -->
<template #tools="{busy}">
    <auto-button @click="$router.push({ name: 'admin.management.admin.create' })">
        {{ $t('Create') }}
    </auto-button>
</template>

<template #default="{records}">
    <tr v-for="(record, index) in records" :key="record.id">
        <td class="flex space-x-2">
            <auto-button small :full="false" 
                @click="$router.push({ 
                    name: 'admin.management.admin.edit', 
                    params: { id: record.id } 
                })">
                <font-awesome-icon icon="fas fa-pencil" />
            </auto-button>
        </td>
    </tr>
</template>
```

```vue
<!-- form-page.vue - Dedicated form route -->
<template>
    <div class="w-full">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                {{ isCreate ? $t('Create admin') : $t('Edit admin') }}
            </h1>
            <auto-button type="plain" @click="$router.back()">
                {{ $t('Back') }}
            </auto-button>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <model-form 
                :model="existingModel" 
                @success="onSuccess" 
                @close="$router.back()" 
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const $route = useRoute();
const $router = useRouter();

const existingModel = ref(null);
const isCreate = computed(() => !$route.params.id);

const onSuccess = () => {
    // Navigate back to list after successful create/update
    $router.push({ name: 'admin.management.admin.list' });
};

onMounted(async () => {
    if (!isCreate.value) {
        // Load existing data for Update (build_form from backend)
        try {
            const data = await axios.post('/management/admin/build_form', { 
                id: $route.params.id 
            });
            existingModel.value = data.model;
        } catch (e) {
            console.error('Failed to load model:', e);
            $router.back();
        }
    }
});
</script>
```

### Form Component Integration
```vue
<!-- model-form.vue - Works for both modal and route -->
<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";

const props = defineProps({
    model: { type: Object, required: false, default: () => null }
});
const emit = defineEmits(['success', 'close']);

const defaultData = ref({
    username: '',
    email: '',
    name: '',
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/management/admin/submit_form');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                emit('success'); // Handle navigation in parent
            }
        });
    } catch (e) {
        console.error(e);
    }
};

// Load data for Update (when model prop exists)
onMounted(async () => {
    if (props.model?.id) {
        // For modal: model already loaded by parent
        defaultData.value = {
            id: props.model.id,
            username: props.model.username,
            email: props.model.email,
            name: props.model.name,
        };
    } else if (props.model) {
        // For new route: model loaded by parent's build_form call
        defaultData.value = {
            id: props.model.id || '',
            username: props.model.username || '',
            email: props.model.email || '',
            name: props.model.name || '',
        };
    }
});
</script>
```

### Decision Examples

#### ✅ Modal Examples:
```javascript
// Simple admin management - 4 fields
{ username, email, name, admin_group_id }

// Basic settings - 3 fields  
{ setting_key, setting_value, setting_type }

// Quick merchant credit adjustment - 2 fields
{ credit_type, amount }
```

#### ✅ New Route Examples:
```javascript
// Complex merchant form - 12+ fields
{ username, email, company_name, company_registration, 
  address, phone, website, api_callback_url, 
  settlement_method, bank_details, commission_rates, etc. }

// Article with rich content - Multiple sections
{ title, slug, content, featured_image, gallery, 
  seo_title, seo_description, tags, categories, 
  publish_date, status, etc. }

// User profile with extensive data
{ personal_info, contact_details, preferences, 
  security_settings, payment_methods, etc. }
```

### Routing Conventions
```javascript
// Create routes (no ID)
'/module/create'                    // e.g., '/admin/create'
'/category/module/create'           // e.g., '/management/admin/create'

// Update routes (with ID)  
'/module/:id/edit'                  // e.g., '/admin/123/edit'
'/category/module/:id/edit'         // e.g., '/management/admin/123/edit'

// Route names
'app.module.create'                 // e.g., 'admin.admin.create'
'app.module.edit'                   // e.g., 'admin.admin.edit'
'app.category.module.create'        // e.g., 'admin.management.admin.create'
'app.category.module.edit'          // e.g., 'admin.management.admin.edit'
```

## Form Creation Patterns

### Using useAutoForm Composable
```vue
<script setup>
import useAutoForm from "#/shared/composables/use-auto-form.js";

const props = defineProps({
    model: { type: Object, required: false, default: () => null }
});
const emit = defineEmits(['success', 'close']);

const defaultData = ref({
    username: '',
    email: '',
    name: '',
    // ... other fields
});

const { formBusy, formState, formError, formReady, submitForm } = useAutoForm(defaultData);

const onSubmitForm = async () => {
    try {
        const data = await submitForm('/api/endpoint');
        await $helper.alertSuccess({
            message: data.message,
            callback: async () => {
                emit('success');
            }
        });
    } catch (e) {
        console.error(e);
    }
};

// Load existing data for editing
onMounted(async () => {
    if (props.model?.id) {
        const data = await axios.post('/api/build_form', { id: props.model.id });
        defaultData.value = {
            id: props.model.id,
            username: data.model.username,
            email: data.model.email,
            // ... other fields
        };
    }
});
</script>
```

### Form Template Structure - Multiple Footer Solutions

**🚨 CRITICAL: No More Teleport Issues with Stacked Modals**

The new modal system eliminates teleport ID conflicts. Choose the best footer approach for your needs:

#### Option 1: Direct Footer in Content (Recommended)
```vue
<template>
    <div v-if="formReady" class="w-full flex flex-col space-y-4">
        <form-input 
            v-model="formState.username" 
            :errors="formError.username" 
            :label="$t('Username')" 
        />
        <form-input 
            v-model="formState.email" 
            :errors="formError.email" 
            :label="$t('Email')" 
        />
        <form-select 
            v-model="formState.status" 
            :errors="formError.status" 
            :label="$t('Status')" 
            :options="$helper.getEnumOptions('Model.status')" 
        />
        
        <!-- 🚨 CRITICAL: Use reusable modal-footer CSS class with manual layout -->
        <div class="modal-footer grid grid-cols-2 gap-4">
            <auto-button type="plain" :busy="formBusy" @click="emit('close')">
                {{ $t('Cancel') }}
            </auto-button>
            <auto-button :busy="formBusy" @click="onSubmitForm">
                {{ $t('Submit') }}
            </auto-button>
        </div>
    </div>
</template>
```

#### Option 2: Modal Footer Event (For Complex Footers)
```vue
<template>
    <div v-if="formReady" class="w-full flex flex-col space-y-4">
        <form-input 
            v-model="formState.username" 
            :errors="formError.username" 
            :label="$t('Username')" 
        />
        <!-- ... other form fields ... -->
    </div>
</template>

<script setup>
import { defineComponent, onMounted } from 'vue';

const emit = defineEmits(['close', 'set-footer']);

// Set footer content via event to modal
onMounted(() => {
    const FooterComponent = defineComponent({
        template: `
            <div class="grid grid-cols-2 gap-4">
                <auto-button type="plain" :busy="busy" @click="cancel">
                    {{ $t('Cancel') }}
                </auto-button>
                <auto-button :busy="busy" @click="submit">
                    {{ $t('Submit') }}
                </auto-button>
            </div>
        `,
        setup() {
            return {
                busy: formBusy,
                cancel: () => emit('close'),
                submit: onSubmitForm
            };
        }
    });
    
    emit('set-footer', FooterComponent);
});
</script>
```

#### Option 3: Simple Inline Footer (Quick Forms)
```vue
<template>
    <div v-if="formReady" class="w-full">
        <div class="flex flex-col space-y-4 mb-6">
            <form-input v-model="formState.username" :label="$t('Username')" />
            <form-input v-model="formState.email" :label="$t('Email')" />
        </div>
        
        <!-- Simple inline footer - using modal-footer class with flex layout -->
        <div class="modal-footer flex justify-end gap-4">
            <auto-button type="plain" @click="emit('close')">{{ $t('Cancel') }}</auto-button>
            <auto-button :busy="formBusy" @click="onSubmitForm">{{ $t('Submit') }}</auto-button>
        </div>
    </div>
</template>
```

**Footer Choice Guidelines:**
- **Option 1**: Best for most forms - clean separation, sticky footer
- **Option 2**: Use for complex footers with dynamic content or multiple actions
- **Option 3**: Quick forms with simple submit/cancel buttons

### 🚨 CRITICAL: Modal Footer CSS Classes - Reusable Utilities

**ALWAYS use these predefined CSS classes instead of writing long Tailwind classes:**

#### Available Modal Footer Class

```css
/* Base modal footer styling - layout must be added manually */
.modal-footer {
    /* Equivalent to: pt-6 mt-6 border-t border-gray-200 sticky bottom-0 bg-white -mx-4 -mb-4 px-4 py-4 rounded-b-xl */
    /* NOTE: This class only provides positioning and styling - you must add layout classes yourself */
}
```

#### Usage Examples

```vue
<!-- ✅ CORRECT: Two buttons (Cancel + Submit) -->
<div class="modal-footer grid grid-cols-2 gap-4">
    <auto-button type="plain" @click="emit('close')">{{ $t('Cancel') }}</auto-button>
    <auto-button :busy="busy" @click="onSubmit">{{ $t('Submit') }}</auto-button>
</div>

<!-- ✅ CORRECT: Single button (Submit only) -->
<div class="modal-footer flex justify-end">
    <auto-button :busy="busy" @click="onSubmit">{{ $t('Submit') }}</auto-button>
</div>

<!-- ✅ CORRECT: Three buttons (Back + Cancel + Submit) -->
<div class="modal-footer grid grid-cols-3 gap-4">
    <auto-button type="plain" @click="goBack">{{ $t('Back') }}</auto-button>
    <auto-button type="plain" @click="emit('close')">{{ $t('Cancel') }}</auto-button>
    <auto-button :busy="busy" @click="onSubmit">{{ $t('Submit') }}</auto-button>
</div>

<!-- ✅ CORRECT: Custom complex layout -->
<div class="modal-footer flex justify-between items-center">
    <auto-button type="plain" @click="goBack">{{ $t('Back') }}</auto-button>
    <div class="flex gap-4">
        <auto-button type="plain" @click="emit('close')">{{ $t('Cancel') }}</auto-button>
        <auto-button :busy="busy" @click="onSubmit">{{ $t('Submit') }}</auto-button>
    </div>
</div>

<!-- ❌ WRONG: Don't duplicate the modal-footer styling -->
<div class="pt-6 mt-6 border-t border-gray-200 sticky bottom-0 bg-white -mx-4 -mb-4 px-4 py-4 rounded-b-xl grid grid-cols-2 gap-4">
    <!-- buttons -->
</div>
```

#### Button Order Conventions

- **Two buttons**: Cancel (left), Submit (right)
- **Single button**: Submit (right-aligned)
- **Three buttons**: Back (left), Cancel (center), Submit (right)

#### Layout Patterns

| Layout | Use Case | Button Count | Classes to Add |
|--------|----------|--------------|----------------|
| Two columns | Standard forms | 2 | `grid grid-cols-2 gap-4` |
| Right-aligned | Confirmation dialogs, info modals | 1 | `flex justify-end` |
| Three columns | Multi-step forms, complex workflows | 3 | `grid grid-cols-3 gap-4` |
| Custom | Complex layouts | Variable | `flex justify-between items-center` etc. |

**CRITICAL**: Always use `modal-footer` class with your layout classes instead of duplicating the positioning/styling!

### 🐛 **Recent Fixes Applied:**

#### **Footer Floating Issue Fixed**
- **Problem**: Modal footer was floating above bottom padding when content was scrollable
- **Root Cause**: CSS class used `-mb-4` which created gap between footer and modal bottom
- **Solution**: Removed `-mb-4` from all modal footer classes
- **Result**: Footer now properly sticks to the very bottom of modal without gaps

#### **FocusTrap Warning Fixed**
- **Problem**: HeadlessUI Dialog showed "no focusable elements" warning
- **Root Cause**: Close button was a `<div>` instead of proper `<button>` element
- **Solution**:
    - Changed close button to proper `<button>` element with `type="button"`
    - Added `aria-label="Close modal"` for accessibility
    - Added focus styles with `focus:ring-2 focus:ring-brand-500`
    - Set `initial-focus` prop on Dialog to point to close button
- **Result**: No more warnings, proper keyboard navigation, better accessibility

## 🚨 CRITICAL: Modal Store System - Modern Approach

### Why Use Modal Store Instead of Direct Components

1. **Better State Management**: Single source of truth for modal state
2. **Modal Chaining**: Support for opening modals from within modals
3. **Consistent API**: Same interface across all apps (admin, merchant, user)
4. **Memory Efficiency**: No duplicate modal instances
5. **Enhanced UX**: Proper stacking, z-index management, and body scroll lock

### Store Injection and Usage

```vue
<script setup>
import { inject } from 'vue';

// Modal store is available in all apps via injection
const $modalStore = inject('$modalStore');

// Basic modal opening
const openModal = () => {
    $modalStore.open(
        ComponentToShow,           // Vue component
        { prop1: 'value' },        // Props for component
        { title: 'Modal Title' },  // Modal options
        () => {                    // Callback when modal closes
            console.log('Modal closed');
            dt.value.fetchData();  // Refresh data
        }
    );
};
</script>
```

### Modal Options Configuration

```javascript
// Available modal options
const modalOptions = {
    title: 'Modal Title',          // String - appears in modal header
    small: false,                  // Boolean - use smaller modal size
    maxHeight: 600,               // Number - max content height in pixels
    onCloseCallback: () => {}     // Function - called when modal closes
};

// Usage example
$modalStore.open(EditForm, 
    { userId: 123 }, 
    {
        title: 'Edit User Profile',
        small: true,
        maxHeight: 400
    },
    () => refreshUserList()
);
```

### Modal Chaining Support

```vue
<script setup>
// Opening modals from within modals (chaining)
const openSecondModal = () => {
    // This opens on top of current modal
    $modalStore.open(
        ConfirmationDialog,
        { message: 'Are you sure?' },
        { title: 'Confirm Action', small: true },
        (result) => {
            if (result.confirmed) {
                // Close all modals and perform action
                $modalStore.closeAllModals();
                performAction();
            }
        }
    );
};

// Close current modal only (maintains stack)
const closeCurrentModal = () => {
    $modalStore.close();
};

// Close all stacked modals
const closeAllModals = () => {
    $modalStore.closeAllModals();
};
</script>
```

### Component Integration Pattern

```vue
<!-- Modal-compatible component (e.g., model-form.vue) -->
<template>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        
        <!-- Form content -->
        <form @submit.prevent="onSubmit">
            <form-input v-model="formData.name" :label="$t('Name')" />
            <!-- ... other form fields ... -->
            
            <!-- Actions -->
            <div class="flex gap-2 mt-6">
                <auto-button type="plain" @click="$modalStore.close()">
                    {{ $t('Cancel') }}
                </auto-button>
                <auto-button @click="onSubmit" :busy="loading">
                    {{ $t('Save') }}
                </auto-button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { inject } from 'vue';

const props = defineProps({
    model: { type: Object, default: null },
    title: { type: String, default: 'Form' }
});

const $modalStore = inject('$modalStore');

const onSubmit = async () => {
    try {
        // Submit form logic
        await submitForm();
        
        // Success - close modal and trigger callback
        $modalStore.close();
    } catch (error) {
        console.error('Form submission failed:', error);
    }
};
</script>
```

### Store State and Methods

```javascript
// Available store properties (read-only)
$modalStore.isOpen           // Boolean - any modal open
$modalStore.modalCount       // Number - total open modals
$modalStore.hasMultipleModals // Boolean - more than one modal
$modalStore.currentModal     // Object - topmost modal data

// Available store methods
$modalStore.open(component, props, options, callback)  // Open new modal
$modalStore.close()                                   // Close current modal
$modalStore.closeModal(id)                           // Close specific modal
$modalStore.closeAllModals()                         // Close all modals
$modalStore.updateModalProps(id, newProps)           // Update modal props
$modalStore.getModal(id)                            // Get specific modal data
```

### Migration from Old Modal Pattern

```vue
<!-- ❌ OLD PATTERN - Don't use anymore -->
<template>
    <auto-modal v-if="modalOpen" @close="closeModal">
        <component-form :model="selectedModel" @success="onSuccess" />
    </auto-modal>
</template>

<script setup>
const modalOpen = ref(false);
const selectedModel = ref(null);

const openModal = (model) => {
    selectedModel.value = model;
    modalOpen.value = true;
};

const closeModal = () => {
    modalOpen.value = false;
    selectedModel.value = null;
};
</script>

<!-- ✅ NEW PATTERN - Use this approach -->
<script setup>
import { inject } from 'vue';
import ComponentForm from './component-form.vue';

const $modalStore = inject('$modalStore');

const openModal = (model) => {
    $modalStore.open(
        ComponentForm,
        { model },
        { title: model ? 'Edit Item' : 'Create Item' },
        () => {
            // Refresh data or perform cleanup
            dt.value.fetchData();
        }
    );
};
</script>
```

**CRITICAL Migration Rules:**
- **Remove all** `<auto-modal>` components from pages
- **Remove all** modal state variables (`modalOpen`, `selectedModel`, etc.)
- **Replace** modal open/close handlers with store methods
- **Use store callbacks** instead of component events for cleanup
- **Test modal chaining** functionality if nested modals are needed

## Component Architecture

### Auto* Components Overview
- **auto-datatable.vue** - Complete data table with pagination, sorting, filtering, export
- **Modal Store System** - Centralized modal management with chaining support
- **auto-button.vue** - Standardized button with loading states and variants

### Form Components
- **form-input.vue** - Text input with error handling
- **form-select.vue** - Dropdown with enum support
- **form-password.vue** - Password input with toggle visibility
- **form-date-picker.vue** - Date selection component
- **form-textarea.vue** - Multi-line text input
- **form-file.vue** - File upload component
- **form-editor.vue** - Rich text editor with image upload (requires folder permission)

#### form-editor.vue Usage - MANDATORY Permission System

**🚨 CRITICAL: The `folder` prop is REQUIRED for image upload functionality and must match EditorController permissions.**

```vue
<template>
    <!-- ✅ ALWAYS specify folder prop -->
    <form-editor
        v-model="formState.content_en"
        :errors="formError.content_en"
        :label="$t('Content (English)')"
        folder="article"
    />
    
    <!-- ✅ Multi-language example -->
    <form-editor
        v-for="l in $locales"
        :key="`content-${l}`"
        v-model="formState[`content_${l}`]"
        :name="`content_${l}`"
        :errors="formError[`content_${l}`]"
        :label="`${$t('Content')} (${$t(\`Locale \${l}\`)})`"
        folder="article"
    />
</template>
```

**Folder Permission System:**
```php
// EditorController.php - Available folders and required permissions
const FOLDERS = [
    'article' => ['Manage article'],     // For article content
    'page' => ['Manage page'],           // For page content  
    'setting' => ['Manage setting'],     // For settings content
];
```

**Usage Rules:**
- **ALWAYS** provide `folder` prop - image upload won't work without it
- **Folder name** must exist in `EditorController::FOLDERS` array
- **Admin permission** is checked automatically against folder requirements
- **Image uploads** are processed at `/editor_upload_image` endpoint
- **Images** are automatically resized to 700px width and stored with date-based paths

**Available Folders:**
- `folder="article"` - Requires "Manage article" permission
- `folder="page"` - Requires "Manage page" permission
- `folder="setting"` - Requires "Manage setting" permission

**Adding New Folders:**
To add a new folder for editor uploads:
1. Add folder mapping to `EditorController::FOLDERS` array
2. Specify required permission(s) for the folder
3. Use the folder name in `form-editor` components

## Setting Store (Global Data)

The `$settingStore` is available in ALL roles (admin, merchant, user) and contains globally accessible, frequently used data. **ALWAYS** use `$settingStore` for country-related operations.

### Accessing Setting Store
```vue
<script setup>
const $settingStore = inject('$settingStore');
</script>
```

### Country Data Usage

**CRITICAL RULE**: For ALL country-related operations (lists, selects, filters), use data from `$settingStore`:

```vue
<script setup>
const $settingStore = inject('$settingStore');

// ✅ CORRECT: Use settingStore for country options
const countryOptions = computed(() => {
    return $settingStore.countryForOptions;
});

// ❌ WRONG: Don't load countries from API calls
// const countries = ref([]);
// onMounted(() => {
//     axios.post('/some/build_form').then(data => countries.value = data.countries);
// });
</script>

<template>
    <!-- Country Select -->
    <form-select
        v-model="formState.country_id"
        :options="countryOptions"
        :label="$t('Country')"
    />
</template>
```

### Available Setting Store Data

#### Country Lists
```javascript
// Options format for form-select
$settingStore.countryForOptions
// Returns: [{ value: "1", text: "Malaysia" }, { value: "2", text: "Singapore" }]

// Raw country data
$settingStore.settings.country
// Returns: [{ id: 1, name: "Malaysia", iso2: "MY", phone_code: "+60" }]
```

#### Default Country
```javascript
// Get default country object
$settingStore.defaultCountry
// Returns: { id: 1, name: "Malaysia", iso2: "MY", phone_code: "+60" }

// Auto-select default country in form-select
<form-select
    v-model="formState.country_id"
    :options="countryOptions"
    :label="$t('Country')"
    :default-value="$settingStore.defaultCountry?.id"
/>
```

#### Phone Code/Extension Data
```javascript
// Extension options for country codes
$settingStore.extForOptions
// Returns: [{ value: "1", text: "+60" }, { value: "2", text: "+65" }]
```

### Special Cases

#### Form-Contact Component
The `form-contact` component **already handles** default country selection automatically - no manual intervention needed:

```vue
<!-- form-contact automatically uses defaultCountry -->
<form-contact
    v-model:select-value="formState.contact_country_id"
    v-model:input-value="formState.contact_number"
    :label="$t('Contact number')"
/>
```

#### Manual Default Country Selection
For regular country selects, manually set default country:

```vue
<script setup>
const $settingStore = inject('$settingStore');

onMounted(async () => {
    // Ensure settings are loaded
    if (!$settingStore.settings) await $settingStore.fetchSettings();
    
    // Set default country if not already set
    if (!formState.country_id && $settingStore.defaultCountry) {
        formState.country_id = String($settingStore.defaultCountry.id);
    }
});
</script>
```

### Loading Pattern
Always ensure settings are loaded before using:

```vue
<script setup>
onMounted(async () => {
    // Check and load settings if needed
    if (!$settingStore.settings) {
        await $settingStore.fetchSettings();
    }
    
    // Now safe to use country data
    if ($settingStore.defaultCountry && !formState.country_id) {
        formState.country_id = String($settingStore.defaultCountry.id);
    }
});
</script>
```

### Button Types and States
```vue
<!-- Primary button (default) -->
<auto-button @click="doAction">{{ $t('Submit') }}</auto-button>

<!-- Secondary variants -->
<auto-button type="plain">{{ $t('Cancel') }}</auto-button>
<auto-button type="danger">{{ $t('Delete') }}</auto-button>
<auto-button type="success">{{ $t('Approve') }}</auto-button>

<!-- Size variants -->
<auto-button small :full="false">{{ $t('Edit') }}</auto-button>

<!-- Loading state -->
<auto-button :busy="isLoading">{{ $t('Processing') }}</auto-button>
```

## State Management Patterns

### Reactive Data Structure
```javascript
// Page-level state
const dt = ref(); // DataTable reference
const model = ref(null); // Current model being edited
const formOpened = ref(false); // Modal visibility
const statistics = ref({ // Optional statistics
    total_amount: 0,
    total_count: 0
});

// Standard handlers
const onFormOpen = (record = null) => {
    model.value = record;
    formOpened.value = true;
};

const onFormClose = () => {
    model.value = null;
    formOpened.value = false;
    dt.value.fetchData(); // Refresh table
};

const onDelete = async (record) => {
    await $helper.alertConfirm({
        message: $t('Are you sure you want to delete this item?'),
        callback: async (result) => {
            if (result.isConfirmed) {
                try {
                    const data = await axios.post('/api/delete', { id: record.id });
                    await $helper.alertSuccess({
                        message: data.message,
                        callback: async () => {
                            await dt.value.fetchData();
                        }
                    });
                } catch (e) {
                    await $helper.alertError({
                        message: e.response?.data?.message || $t('Something went wrong')
                    });
                    console.error(e);
                }
            }
        }
    });
};
```

## Styling Guidelines

### Tailwind CSS Usage
```vue
<!-- Layout -->
<div class="w-full flex flex-col space-y-4">
<div class="grid grid-cols-2 gap-4">
<div class="flex justify-between items-center">

<!-- Cards -->
<div class="bg-white rounded-lg shadow p-4">

<!-- Spacing -->
<div class="space-y-4">        <!-- Vertical spacing -->
<div class="space-x-2">        <!-- Horizontal spacing -->
<div class="p-4">              <!-- Padding -->
<div class="mb-4">             <!-- Margin bottom -->

<!-- Responsive -->
<div class="w-full lg:grid lg:grid-cols-3 lg:space-y-0 lg:gap-4">
```

### Component-Specific Classes
```vue
<!-- DataTable wrapper -->
<div class="w-full">
    <auto-datatable class="..." />
</div>

<!-- Form layouts -->
<div class="w-full flex flex-col space-y-4">
    <!-- Form components -->
</div>

<!-- Button groups -->
<div class="flex justify-start items-center space-x-2">
    <auto-button small :full="false">Edit</auto-button>
    <auto-button type="danger" small :full="false">Delete</auto-button>
</div>
```


## Vue Router Navigation Rules

### 🚨 CRITICAL: Always Use Route Names, Never Path Strings

#### ✅ CORRECT - Use route names with object syntax:
```javascript
// Basic navigation
$router.push({ name: 'admin.deposit.list' })

// With parameters
$router.push({ 
    name: 'admin.management.admin.edit', 
    params: { id: record.id } 
})

// With query parameters
$router.push({ 
    name: 'admin.deposit.list', 
    query: { status: 'pending' } 
})

// With both params and query
$router.push({ 
    name: 'admin.merchant.edit', 
    params: { id: merchant.id },
    query: { tab: 'settings' }
})
```

#### ❌ WRONG - Never use path strings:
```javascript
// DON'T DO THIS - Path strings are fragile
$router.push('/admin/deposit/list')                    // ❌ Wrong
$router.push(`/admin/management/admin/${id}/edit`)     // ❌ Wrong  
$router.push('/admin/deposit/list?status=pending')     // ❌ Wrong
```

#### Why Use Route Names?
1. **Type safety**: Routes are validated at build time
2. **Refactoring**: Path changes won't break navigation
3. **Maintainability**: Centralized route definitions
4. **Parameters**: Clean parameter passing
5. **IDE support**: Better autocomplete and error detection

#### Navigation Examples in Components:
```vue
<template>
    <!-- In buttons -->
    <auto-button @click="$router.push({ name: 'admin.deposit.create' })">
        {{ $t('Create deposit') }}
    </auto-button>
    
    <!-- In DataTable actions -->
    <auto-button @click="$router.push({ 
        name: 'admin.merchant.edit', 
        params: { id: record.id } 
    })">
        {{ $t('Edit') }}
    </auto-button>
</template>

<script setup>
// In functions
const goToList = () => {
    $router.push({ name: 'admin.deposit.list' });
};

const editRecord = (id) => {
    $router.push({ 
        name: 'admin.deposit.edit', 
        params: { id } 
    });
};

// After successful operations
const onSuccess = () => {
    $router.push({ name: 'admin.management.admin.list' });
};
</script>
```

## Helper Functions

### $helper Utility Methods

#### 🚨 CRITICAL: Always Use Helper Methods for Formatting

```javascript
// === CURRENCY & NUMBER FORMATTING ===
// ALWAYS use for displaying money/numbers (not for inputs)
$helper.fundFormat(amount)                    // Format: 1,234.56
$helper.fundFormat(amount, decimals)          // Custom decimals: 1,234.5678

// Examples in templates:
{{ $helper.fundFormat(record.amount) }}           // Display amounts
{{ $helper.fundFormat(record.balance, 4) }}       // Display crypto with 4 decimals
{{ $helper.fundFormat(statistics.total_profit) }} // Display calculated totals

// === DATE & TIME FORMATTING ===
// Server always returns ISO8601 format - ALWAYS format for display
$helper.dateFormat(dateString)               // Format: 2024-01-15
$helper.dateTimeFormat(dateString)           // Format: 2024-01-15 14:30:25

// Examples in templates:
{{ $helper.dateFormat(record.created_at) }}      // Show date only
{{ $helper.dateTimeFormat(record.updated_at) }}  // Show date and time
{{ record.expired_at ? $helper.dateTimeFormat(record.expired_at) : '-' }} // Handle nulls

// === ALERT DIALOGS ===
// Use these for user feedback - NOT native alert()
await $helper.alertSuccess({ message, callback })
await $helper.alertError({ message, callback })  
await $helper.alertWarning({ message, callback })
await $helper.alertConfirm({ message, callback })

// Examples:
await $helper.alertSuccess({
    message: 'Operation completed successfully',
    callback: () => router.push('/list')
});

await $helper.alertConfirm({
    message: 'Are you sure you want to delete this item?',
    callback: async (result) => {
        if (result.isConfirmed) {
            await deleteItem();
        }
    }
});

// === ENUM HANDLING ===
$helper.getEnumOptions('Model.field')        // Get select options from domain enums

// === FORM UTILITIES ===
$helper.handleFormError(error)               // Process API validation errors
$helper.extractFormData(formData)            // Extract form data for API calls
```

#### Formatting Rules - NON-NEGOTIABLE
```vue
<template>
    <!-- ✅ CORRECT - Always format display values -->
    <td>{{ $helper.fundFormat(record.amount) }}</td>
    <td>{{ $helper.dateTimeFormat(record.created_at) }}</td>
    <td>{{ record.balance ? $helper.fundFormat(record.balance) : '-' }}</td>
    
    <!-- ❌ WRONG - Never display raw values -->
    <td>{{ record.amount }}</td>              <!-- Raw number -->
    <td>{{ record.created_at }}</td>          <!-- ISO8601 string -->
    <td>{{ record.balance || 0 }}</td>        <!-- Unformatted number -->
    
    <!-- ✅ CORRECT - Use helper alerts -->
    <script setup>
    const showSuccess = async () => {
        await $helper.alertSuccess({ message: 'Saved successfully' });
    };
    </script>
    
    <!-- ❌ WRONG - Never use native alerts -->
    <script setup>
    const showSuccess = () => {
        alert('Saved successfully');  // DON'T DO THIS
    };
    </script>
</template>
```

## Translation System

### 🚨 CRITICAL: Translation Rules
1. **Always use translations**: ALL user-visible text MUST use `$t()` or `t()`
2. **Pure English keys**: Use "How are you" NOT "How Are You" or "how_are_you"
3. **Shared system**: Frontend and backend share same translation files
4. **Minimal English translations**: Only add to `en.json` when significantly different from Chinese

### Translation Files Structure
```
lang/
├── zh.json              # Chinese translations (ALL keys must be here)
├── en.json              # English translations (only when different)
├── zh_permission.json   # Permission names in Chinese
└── en_permission.json   # Permission names in English (usually empty)
```

### Key Formatting Rules
```json
// ✅ CORRECT - Pure English, sentence case
"How are you": "你好吗",
"Something went wrong": "出现了问题",
"New payment amount x from y": "收到付款 :x 来自 :y",

// ❌ WRONG - Title case or snake_case
"How Are You": "你好吗",           // Wrong: Title case
"Something_went_wrong": "出现了问题", // Wrong: snake_case
```

### Usage Patterns

#### In Vue Templates
```vue
<template>
    <!-- Simple translations -->
    <form-input :label="$t('Email address')" />
    <auto-button>{{ $t('Save changes') }}</auto-button>
    
    <!-- Parameterized translations -->
    <div>{{ $t('Welcome back, :name', { name: user.name }) }}</div>
    <div>{{ $t('Insufficient x', { x: 'Credit' }) }}</div>
    <div>{{ $t('New payment amount x from y', { 
        x: $helper.fundFormat(payment.amount), 
        y: payment.merchant_name 
    }) }}</div>
</template>
```

#### In Script Setup
```vue
<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

// Simple usage
const showMessage = () => {
    alert(t('Operation successful'))
}

// With parameters
const showError = (username) => {
    alert(t('x not found', { x: username }))
}
</script>
```

### English Translation Strategy
```json
// zh.json (Chinese - REQUIRED for ALL keys)
{
    "Cancel": "取消",
    "Loading": "加载中", 
    "Save": "保存",
    "Credit 1": "现金分",
    "Password2": "安全密码",
    "Insufficient x": ":x 不足"
}

// en.json (English - ONLY when different concept)
{
    "Credit 1": "Cash Point",           // Different concept
    "Password2": "Security PIN",        // Different concept  
    "Insufficient x": "Insufficient :x" // Different grammar
    
    // NOTE: "Cancel", "Loading", "Save" NOT included - same meaning
}
```

## Development Workflow

### 1. Before Creating New Pages
```bash
# Check existing patterns
ls resources/scripts/admin/pages/main/management/admin/
# Study: list-page.vue, model-form.vue

# Check for similar functionality
grep -r "AutoDataTable" resources/scripts/admin/pages/
```

### 2. Page Creation Checklist
- [ ] **Decide CRUD strategy**: Modal (≤6-8 fields) vs. New Route (>8 fields, complex forms)
- [ ] Follow existing directory structure
- [ ] Use standard naming conventions
- [ ] Import and use Auto* components
- [ ] Implement proper search filters with correct filter keys
- [ ] Add i18n support for all text with `$t()` or `t()`
- [ ] **CRITICAL**: Use `$helper.fundFormat()` for ALL displayed money/numbers
- [ ] **CRITICAL**: Use `$helper.dateFormat()` or `$helper.dateTimeFormat()` for ALL dates
- [ ] **CRITICAL**: Use `$helper.alertSuccess/Error/Warning/Confirm()` instead of native alerts
- [ ] **CRITICAL**: Use `$router.push({ name: 'route.name' })` - NEVER use path strings
- [ ] Implement proper error handling with helper methods
- [ ] Add loading states with `:busy` props
- [ ] **For new routes**: Set up Create (`/module/create`) and Update (`/module/:id/edit`) routes
- [ ] **For modals**: Implement proper model passing and form state management

### 3. Testing New Pages
- [ ] Test all CRUD operations
- [ ] Verify search filters work correctly
- [ ] Test sorting and pagination
- [ ] Check responsive design
- [ ] Validate form submissions and error handling
- [ ] Test export functionality (if enabled)

## Common Patterns by Page Type

### List Pages (CRUD Tables)
1. **AutoDataTable** with model binding
2. **Search filters** using correct filter key patterns
3. **Tools slot** for create button
4. **Default slot** for custom row rendering
5. **Modal integration** for forms
6. **Statistics cards** (optional, for financial data)

### Form Pages (Create/Edit)
1. **useAutoForm** composable for state management
2. **Form components** for inputs
3. **Teleport** for modal footer buttons
4. **Validation** with error display
5. **Loading states** during submission

### Custom Pages (Reports, Dashboards)
1. **Statistics cards** for key metrics
2. **Chart components** for data visualization
3. **Filter forms** for date ranges and criteria
4. **Export functionality** for reports
5. **Real-time data** with auto-refresh

## Performance Guidelines

### 1. Component Loading
- Use **dynamic imports** for heavy components
- Implement **lazy loading** for routes
- Use **v-show** vs **v-if** appropriately

### 2. Data Management
- Use **computed properties** for derived data
- Implement **proper watchers** with deep/immediate options
- **Debounce** search inputs and API calls

### 3. Memory Management
- **Clear intervals** in `onBeforeUnmount`
- **Remove event listeners** when component unmounts
- **Avoid memory leaks** in long-lived pages

## Security Guidelines

### 1. Data Validation
- **Never trust** client-side validation only
- **Sanitize** user inputs before display
- **Use** proper CSRF tokens for forms

### 2. Permission Handling
```javascript
// Check permissions before showing actions
const hasEditPermission = computed(() => {
    return $helper.hasPermission($admin.value, 'Manage Admin');
});

// Conditional rendering
<auto-button v-if="hasEditPermission" @click="onEdit">
    {{ $t('Edit') }}
</auto-button>
```

### 3. API Security
- **Always** use authenticated endpoints
- **Handle** 401/403 responses properly
- **Don't expose** sensitive data in client code

## Module Creation Rules - MANDATORY Namespace Patterns

**When creating new modules, ALWAYS create new namespaces and route prefixes instead of reusing existing ones**

### Vue.js Router Namespace Pattern
```javascript
// ✅ ALWAYS: Create dedicated namespace and prefix for new modules
{
    path: 'inventory',                    // NEW namespace
    component: BlankRouter,
    redirect: {name: 'admin.inventory.product.list'},
    children: [
        {
            path: 'product/list',
            component: () => import('./pages/main/inventory/product/list-page.vue'),
            name: 'admin.inventory.product.list',
        },
        {
            path: 'product/form/:id?',
            component: () => import('./pages/main/inventory/product/model-form.vue'),
            name: 'admin.inventory.product.form',
        },
        {
            path: 'category/list',
            component: () => import('./pages/main/inventory/category/list-page.vue'),
            name: 'admin.inventory.category.list',
        },
    ],
},

// ❌ NEVER: Reuse existing namespaces unless specifically mentioned
{
    path: 'management',                   // WRONG - reusing existing namespace
    children: [
        {
            path: 'product/list',         // WRONG - should be new namespace
            name: 'admin.management.product.list',
        }
    ]
}
```

**Vue.js Directory Structure:**
```
resources/scripts/admin/pages/main/
├── inventory/                    # NEW module directory
│   ├── product/
│   │   ├── list-page.vue
│   │   └── model-form.vue
│   └── category/
│       ├── list-page.vue
│       └── model-form.vue
├── management/                   # EXISTING - don't reuse
├── merchant/                     # EXISTING - don't reuse
└── user/                        # EXISTING - don't reuse
```

**Route Naming Guidelines:**
- **Format**: `admin.{module}.{submodule}.{action}`
- **Module**: Singular naming (`inventory`, `report`, `analytics`)
- **Actions**: `list`, `form`, `detail`
- **Always use route names** instead of path strings

### Admin Side Menu Structure - Decision Tree

**When adding new admin routes, decide between first-level or sub-level menu items based on complexity:**

#### First-Level Menu Items (Top-Level Navigation)
```javascript
// ✅ Use for: Single action modules or very important features
{
    name: 'Dashboard', 
    route: 'admin.home', 
    icon: 'fa-home'  // FontAwesome icon required
},
{
    name: 'Manage settlement', 
    icon: 'fa-money-bill',      // FontAwesome icon required 
    route: 'admin.settlement.list', 
    permissions: ['Manage settlement'], 
    notification: 'pending_settlement'
},
```

**First-Level Guidelines:**
- **ALWAYS include** FontAwesome icon (`fa-*` format)
- **Use for**: Dashboard-style pages, critical single actions, main business functions
- **Examples**: Dashboard, Settlement, Quick reports, System status
- **Route format**: Direct route or simple sub-route

#### Sub-Level Menu Items (Grouped Navigation)
```javascript
// ✅ Use for: Multiple related actions or complex modules
{
    name: 'Inventory',              // Parent group name
    icon: 'fa-boxes-stacked',       // FontAwesome icon required
    children: [
        {
            name: 'Manage product', 
            route: 'admin.inventory.product.list', 
            permissions: ['Manage product']
        },
        {
            name: 'Manage category', 
            route: 'admin.inventory.category.list', 
            permissions: ['Manage category']
        },
        {
            name: 'Inventory report', 
            route: 'admin.inventory.report.list', 
            permissions: ['Inventory report']
        },
    ]
},
```

**Sub-Level Guidelines:**
- **Parent requires** FontAwesome icon
- **Children don't need** icons (handled by menu styling)
- **Use for**: CRUD operations, related features, complex workflows
- **Examples**: User management, Content management, Financial operations
- **Route format**: Nested module routes

### FontAwesome Icon Discovery Process

**ALWAYS search, discover, and think about the most appropriate icon for your module:**

#### Step 1: Search FontAwesome Library
```javascript
// 🔍 Process for finding the right icon:
// 1. Visit https://fontawesome.com/icons
// 2. Search by keywords related to your module function
// 3. Browse categories: Business, Technology, Shopping, etc.
// 4. Consider semantic meaning, not just visual appeal
// 5. Choose icons that users will intuitively understand
```

#### Step 2: Add New Icons to font-awesome.js
```javascript
// File: resources/scripts/admin/font-awesome.js

// 1. Import the new icon from FontAwesome
import {
    faCalendar,
    faCheck,
    // ... existing imports ...
    faBoxesStacked,        // ✅ NEW: Add your discovered icon
    faChartLine,          // ✅ NEW: Add another discovered icon
} from '@fortawesome/free-solid-svg-icons';

// 2. Add to library (MUST include in this call)
library.add(
    faKey, faCopy, faCalendar, 
    // ... existing icons ...
    faBoxesStacked,        // ✅ NEW: Include in library.add()
    faChartLine,          // ✅ NEW: Include in library.add()
);
```

#### Step 3: Use Icon in Side Menu
```javascript
// After adding to font-awesome.js, use in side-bar.js
{
    name: 'Inventory',
    icon: 'fa-boxes-stacked',    // ✅ Now available to use
    children: [...]
}
```

**Icon Discovery Guidelines:**
- **Think semantically**: What does this module DO, not what it looks like
- **Consider user context**: Will users immediately understand the connection?
- **Browse categories**: Business, Shopping, Medical, Technology, etc.
- **Check alternatives**: FontAwesome often has multiple variations
- **Prefer solid icons**: More visible in navigation menus
- **Test comprehension**: Does the icon make sense without the label?

**Examples of Good Discovery Process:**
```javascript
// Inventory Management Module:
// 🤔 Think: Storage, organization, tracking, boxes, warehouse
// 🔍 Search: "inventory", "boxes", "warehouse", "stack"
// ✅ Found: fa-boxes-stacked (perfect semantic match)

// Customer Support Module:
// 🤔 Think: Help, communication, assistance, headphones
// 🔍 Search: "support", "headset", "help", "customer"  
// ✅ Found: fa-headset (clear support association)

// Analytics Module:
// 🤔 Think: Data, trends, graphs, insights, measurement
// 🔍 Search: "chart", "analytics", "graph", "data"
// ✅ Found: fa-chart-line (represents data trends)
```

### Side Menu Implementation Example

```javascript
// File: resources/scripts/admin/side-bar.js

export default [
    // First-level: Dashboard (always first)
    {name: 'Dashboard', route: 'admin.home', icon: 'fa-home'},
    
    // Sub-level: Complex module with multiple actions
    {
        name: 'Inventory',
        icon: 'fa-boxes-stacked',
        children: [
            {name: 'Manage product', route: 'admin.inventory.product.list', permissions: ['Manage product']},
            {name: 'Manage category', route: 'admin.inventory.category.list', permissions: ['Manage category']},
            {name: 'Stock report', route: 'admin.inventory.report.stock', permissions: ['Inventory report']},
        ]
    },
    
    // First-level: Important single action
    {
        name: 'System status', 
        icon: 'fa-heartbeat', 
        route: 'admin.system.status', 
        permissions: ['System monitoring']
    },
    
    // Sub-level: User-related operations
    {
        name: 'User Management',
        icon: 'fa-users',
        children: [
            {name: 'Manage user', route: 'admin.user.user.list', permissions: ['Manage user']},
            {name: 'User groups', route: 'admin.user.group.list', permissions: ['Manage user group']},
            {name: 'User activity', route: 'admin.user.activity.list', permissions: ['View user activity']},
        ]
    },
];
```

### Decision Matrix for Menu Structure

| Module Characteristics | Use First-Level | Use Sub-Level |
|----------------------|------------------|---------------|
| Single main action | ✅ First-level | ❌ |
| Multiple related actions | ❌ | ✅ Sub-level |
| Dashboard/overview page | ✅ First-level | ❌ |
| CRUD operations (Create/Read/Update/Delete) | ❌ | ✅ Sub-level |
| System-critical function | ✅ First-level | ❌ |
| 3+ related pages | ❌ | ✅ Sub-level |
| Quick access needed | ✅ First-level | ❌ |
| Complex workflow | ❌ | ✅ Sub-level |

### Menu Item Properties Reference

```javascript
{
    name: 'Display Name',           // REQUIRED: Translated display name
    route: 'admin.module.action',   // REQUIRED: Vue route name (for direct links)
    icon: 'fa-icon-name',          // REQUIRED: FontAwesome icon (first-level only)
    permissions: ['Permission'],    // OPTIONAL: Array of required permissions
    notification: 'notification_key', // OPTIONAL: Notification badge key
    isDeveloper: true,             // OPTIONAL: Show only in development mode
    children: [...]                // OPTIONAL: Sub-menu items (removes route property)
}
```

**CRITICAL Side Menu Rules:**
- **ALWAYS include** `icon` property for first-level items
- **NEVER include** `icon` property for sub-level items
- **ALWAYS use** route names, never path strings
- **ALWAYS add** `permissions` array for access control
- **ALWAYS search** FontAwesome library for semantically appropriate icons
- **ALWAYS add new icons** to `resources/scripts/admin/font-awesome.js` before using
- **Group related** actions under sub-level menus
- **Keep first-level** menu items for critical/single actions only

### Vue.js Navigation - ALWAYS Use Route Names
```javascript
// ✅ ALWAYS: Use route names for navigation
router.push({name: 'admin.inventory.product.list'});
router.push({name: 'admin.inventory.product.form', params: {id: 123}});

// ❌ NEVER: Use path strings
router.push('/admin/inventory/product/list');           // WRONG
router.push(`/admin/inventory/product/form/${id}`);     // WRONG
```

## Contact Number Handling - MANDATORY Patterns

**Contact numbers MUST use the FormContact component for consistent international formatting**

### FormContact Component Usage
```vue
<template>
    <!-- ✅ ALWAYS: Use FormContact for contact number inputs -->
    <form-contact 
        v-model:select-value="formState.contact_country_id" 
        v-model:input-value="formState.contact_number" 
        :errors="formError" 
        :label="$t('Contact number')" 
    />
    
    <!-- ✅ For multiple contacts: Use descriptive prefixes -->
    <form-contact 
        v-model:select-value="formState.emergency_contact_country_id" 
        v-model:input-value="formState.emergency_contact_number" 
        :errors="formError" 
        :label="$t('Emergency contact number')"
        select-name="emergency_contact_country_id"
        input-name="emergency_contact_number"
    />
    
    <form-contact 
        v-model:select-value="formState.business_contact_country_id" 
        v-model:input-value="formState.business_contact_number" 
        :errors="formError" 
        :label="$t('Business contact number')"
        select-name="business_contact_country_id" 
        input-name="business_contact_number"
    />
</template>

<script setup>
import FormContact from "#/shared/components/form/form-contact.vue";

// Form state structure
const defaultData = ref({
    // Single contact pattern
    contact_country_id: '',
    contact_number: '',
    
    // Multiple contact pattern
    emergency_contact_country_id: '',
    emergency_contact_number: '',
    business_contact_country_id: '',
    business_contact_number: '',
});
</script>
```

### FormContact Component Features
- **Auto-loads** country options from `$settingStore.settings.country`
- **Auto-selects** default country from `$settingStore.defaultCountry`
- **Searchable dropdown** for country selection (name, ISO2, ISO3, ext)
- **Formatted display** shows country extension (e.g., +60, +65, +62)
- **Error handling** for both country and number fields
- **Validation integration** with backend validation errors

### Error Handling Pattern
```vue
<template>
    <!-- FormContact automatically handles errors for both fields -->
    <form-contact 
        v-model:select-value="formState.contact_country_id" 
        v-model:input-value="formState.contact_number" 
        :errors="formError"  <!-- Object containing field-specific errors -->
        :label="$t('Contact number')" 
    />
</template>

<script setup>
// Form errors from backend automatically map to component
const formError = ref({
    contact_country_id: ['Country is required'],
    contact_number: ['Contact number format is invalid'],
    emergency_contact_country_id: ['Emergency country is required'],
    emergency_contact_number: ['Emergency contact format is invalid'],
});
</script>
```

### When to Use FormContact

| Scenario | Use FormContact | Notes |
|----------|----------------|-------|
| Single contact number | ✅ Standard usage | Use default `select-name` and `input-name` |
| Multiple fixed contacts | ✅ With custom names | Specify `select-name` and `input-name` props |
| Dynamic contact list | ✅ In v-for loops | Use indexed names or separate component instances |
| Display-only contacts | ❌ Use formatted display | Show `full_contact_number` with country flag |

### Contact Number Validation
```javascript
// FormContact integrates with backend validation
// Backend returns errors like:
{
    "contact_country_id": ["Country is required"],
    "contact_number": ["Contact number format is invalid"]
}

// Component automatically displays these errors
// No additional frontend validation needed
```

**CRITICAL FormContact Rules:**
- **ALWAYS use** `FormContact` for contact number inputs
- **NEVER create** custom contact input components
- **Always bind** both `select-value` and `input-value` with v-model
- **Always provide** `:errors` prop for validation feedback
- **Use descriptive** `select-name` and `input-name` for multiple contacts
- **Import** from `#/shared/components/form/form-contact.vue`

---

## 📝 Final Reminders

1. **ALWAYS** reuse existing Auto* components
2. **NEVER** create custom data tables or forms
3. **FOLLOW** the filter key patterns exactly as specified
4. **USE** domain enums through the helper system
5. **MAINTAIN** consistency with existing pages
6. **TEST** thoroughly before committing changes
7. **CRITICAL**: ALWAYS format displayed values:
    - `$helper.fundFormat()` for money/numbers
    - `$helper.dateFormat()` / `$helper.dateTimeFormat()` for dates
    - `$helper.alertSuccess/Error/Warning/Confirm()` for user feedback
8. **CRITICAL**: ALWAYS use route names: `$router.push({ name: 'route.name' })`
9. **NEVER** display raw values, use native `alert()`, or navigate with path strings


**Remember**: The goal is consistency, maintainability, and reusability. When in doubt, copy from an existing similar page and modify as needed.

