# Form Components Documentation

## Overview
Reusable Blade components for consistent form handling across the admin dashboard. These components provide automatic error handling, old value persistence, and RTL support.

## Available Components

### 1. Text Input Component
**Location:** `resources/views/components/form/input.blade.php`

**Usage:**
```blade
<x-form.input
    name="field_name"
    label="Field Label"
    type="text"
    :value="'default value'"
    :required="true"
    placeholder="Enter value..."
    icon="user"
    help-text="Additional help text"
/>
```

**Parameters:**
- `name` (required): Input field name
- `label` (optional): Label text
- `type` (optional): Input type (text, email, password, number, url, tel, date, etc.) - default: 'text'
- `value` (optional): Default value
- `required` (optional): Boolean - default: false
- `placeholder` (optional): Placeholder text
- `icon` (optional): FontAwesome icon name (without 'fa-' prefix)
- `help-text` (optional): Helper text below input
- `disabled` (optional): Boolean - default: false
- `readonly` (optional): Boolean - default: false

**Examples:**
```blade
{{-- Basic text input --}}
<x-form.input name="title" label="Title" :required="true" />

{{-- Email input with icon --}}
<x-form.input name="email" type="email" label="Email" icon="envelope" />

{{-- Number input with step --}}
<x-form.input name="price" type="number" label="Price" step="0.01" />

{{-- URL input with help text --}}
<x-form.input 
    name="website" 
    type="url" 
    label="Website" 
    icon="link"
    help-text="Enter full URL including https://"
/>
```

---

### 2. Textarea Component
**Location:** `resources/views/components/form/textarea.blade.php`

**Usage:**
```blade
<x-form.textarea
    name="description"
    label="Description"
    :rows="4"
    :required="false"
    placeholder="Enter description..."
    help-text="Detailed description"
/>
```

**Parameters:**
- `name` (required): Textarea field name
- `label` (optional): Label text
- `value` (optional): Default value
- `required` (optional): Boolean - default: false
- `placeholder` (optional): Placeholder text
- `help-text` (optional): Helper text below textarea
- `rows` (optional): Number of rows - default: 3
- `disabled` (optional): Boolean - default: false
- `readonly` (optional): Boolean - default: false

**Examples:**
```blade
{{-- Basic textarea --}}
<x-form.textarea name="notes" label="Notes" />

{{-- Large textarea with help text --}}
<x-form.textarea 
    name="bio" 
    label="Biography" 
    :rows="8"
    help-text="Enter your full biography"
/>
```

---

### 3. Select Component
**Location:** `resources/views/components/form/select.blade.php`

**Usage:**
```blade
<x-form.select
    name="status"
    label="Status"
    :options="[
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending'
    ]"
    :value="'active'"
    :required="true"
    placeholder="Select status..."
    icon="toggle-on"
/>
```

**Parameters:**
- `name` (required): Select field name
- `label` (optional): Label text
- `options` (required): Array of value => label pairs
- `value` (optional): Selected value
- `required` (optional): Boolean - default: false
- `placeholder` (optional): Placeholder option text - default: 'اختر...'
- `icon` (optional): FontAwesome icon name
- `help-text` (optional): Helper text below select
- `disabled` (optional): Boolean - default: false

**Examples:**
```blade
{{-- Simple select --}}
<x-form.select 
    name="role" 
    label="User Role"
    :options="['admin' => 'Admin', 'user' => 'User']"
/>

{{-- Select with model data --}}
<x-form.select 
    name="city_id" 
    label="City"
    :options="$cities->pluck('name', 'id')->toArray()"
    :value="$user->city_id"
    icon="city"
/>
```

---

### 4. Checkbox Component
**Location:** `resources/views/components/form/checkbox.blade.php`

**Usage:**
```blade
<x-form.checkbox
    name="is_active"
    label="Active"
    :checked="true"
    help-text="Enable this option"
/>
```

**Parameters:**
- `name` (required): Checkbox field name
- `label` (required): Label text
- `value` (optional): Checkbox value - default: '1'
- `checked` (optional): Boolean - default: false
- `help-text` (optional): Helper text below checkbox
- `disabled` (optional): Boolean - default: false

**Examples:**
```blade
{{-- Basic checkbox --}}
<x-form.checkbox name="accept_terms" label="I accept terms" />

{{-- Checked by default --}}
<x-form.checkbox 
    name="is_featured" 
    label="Featured Item"
    :checked="true"
/>

{{-- With help text --}}
<x-form.checkbox 
    name="newsletter" 
    label="Subscribe to newsletter"
    help-text="Receive weekly updates"
/>
```

---

### 5. File Upload Component
**Location:** `resources/views/components/form/file.blade.php`

**Usage:**
```blade
<x-form.file
    name="image"
    label="Upload Image"
    accept="image/*"
    :preview="true"
    :required="false"
    help-text="Max file size: 2MB"
    :current-file="$model->image ?? null"
/>
```

**Parameters:**
- `name` (required): File input field name
- `label` (optional): Label text
- `required` (optional): Boolean - default: false
- `help-text` (optional): Helper text below input
- `accept` (optional): Accepted file types - default: 'image/*'
- `preview` (optional): Boolean - Show image preview - default: false
- `current-file` (optional): Path to current file (for edit forms)

**Examples:**
```blade
{{-- Image upload with preview --}}
<x-form.file 
    name="avatar" 
    label="Profile Picture"
    :preview="true"
    accept="image/jpeg,image/png"
    help-text="JPG or PNG, max 2MB"
/>

{{-- Document upload --}}
<x-form.file 
    name="document" 
    label="Upload Document"
    accept=".pdf,.doc,.docx"
    help-text="PDF or Word documents only"
/>

{{-- Edit form with existing file --}}
<x-form.file 
    name="logo" 
    label="Company Logo"
    :preview="true"
    :current-file="$company->logo"
/>
```

---

## Features

### ✅ Automatic Error Handling
All components automatically display validation errors from Laravel's `$errors` bag:
```blade
<x-form.input name="email" label="Email" />
{{-- If validation fails, error message is automatically displayed --}}
```

### ✅ Old Value Persistence
Components automatically restore old input values after validation failure:
```blade
<x-form.input name="name" />
{{-- If form submission fails, the previously entered value is restored --}}
```

### ✅ RTL Support
All components are designed with RTL (Right-to-Left) support for Arabic interfaces.

### ✅ Icon Support
Most components support FontAwesome icons:
```blade
<x-form.input name="email" icon="envelope" />
<x-form.select name="status" icon="toggle-on" />
```

### ✅ Help Text
All components support contextual help text:
```blade
<x-form.input 
    name="slug" 
    help-text="Used in URLs (e.g., my-page)"
/>
```

### ✅ Required Indicator
Required fields automatically show a red asterisk:
```blade
<x-form.input name="name" :required="true" />
{{-- Label will show: "Name *" --}}
```

---

## Form Structure Example

### Complete Form Using Components:
```blade
<form method="POST" action="{{ route('admin.cities.store') }}" enctype="multipart/form-data">
    @csrf
    
    {{-- Display all errors at top --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Validation Errors</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <x-form.input
                name="name"
                label="City Name"
                :required="true"
                icon="city"
                placeholder="Cairo"
            />
        </div>
        
        <div class="col-md-6">
            <x-form.input
                name="slug"
                label="URL Slug"
                :required="true"
                icon="link"
                help-text="Used in URLs (e.g., cairo)"
            />
        </div>
    </div>

    <x-form.textarea
        name="description"
        label="Description"
        :rows="4"
        placeholder="Enter detailed description..."
    />

    <x-form.select
        name="status"
        label="Status"
        :options="['active' => 'Active', 'inactive' => 'Inactive']"
        :required="true"
        icon="toggle-on"
    />

    <x-form.file
        name="image"
        label="City Image"
        :preview="true"
        help-text="JPG, PNG, GIF (max 2MB)"
    />

    <x-form.checkbox
        name="is_featured"
        label="Featured City"
        help-text="Show in featured cities list"
    />

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save
    </button>
</form>
```

---

## Styling

All components use Bootstrap 4/5 classes and are fully compatible with the admin dashboard theme. Error states use Bootstrap's `.is-invalid` class and display errors with `.invalid-feedback`.

### Custom Styling:
You can add custom classes using the `attributes` parameter:
```blade
<x-form.input 
    name="title" 
    class="form-control-lg" 
    data-toggle="tooltip"
/>
```

---

## Migration Guide

### Before (Old Style):
```blade
<div class="form-group">
    <label for="name">Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" 
           id="name" name="name" value="{{ old('name') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

### After (Using Component):
```blade
<x-form.input name="name" label="Name" :required="true" />
```

**Benefits:**
- 10 lines → 1 line
- Consistent error handling
- Automatic old value restoration
- Built-in icon and help text support

---

## Notes

1. **Form Enctype:** Remember to add `enctype="multipart/form-data"` when using file upload components
2. **CSRF Token:** Always include `@csrf` in your forms
3. **Validation:** Components work seamlessly with Laravel's validation
4. **Customization:** You can modify component files in `resources/views/components/form/` to match your design needs

---

## Controller Integration

### Store Method Example:
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',
        'image' => 'nullable|image|max:2048',
        'is_featured' => 'nullable|boolean',
    ]);

    try {
        $model = Model::create($validated);
        
        if ($request->hasFile('image')) {
            $model->image = $request->file('image')->store('images', 'public');
            $model->save();
        }

        return redirect()->route('admin.models.index')
            ->with('success', 'Created successfully');
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

## Future Enhancements

Potential additions for future versions:
- Date/Time Picker Component
- Rich Text Editor Component
- Multi-select Component
- Tags Input Component
- Color Picker Component
- Range Slider Component
- Switch Toggle Component
- Radio Button Group Component
- File Manager Component with drag-drop

---

## Support

For issues or feature requests, please update the component files in `resources/views/components/form/` or contact the development team.
