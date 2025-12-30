# Quick Reference: Form Components

## One-Line Component Usage

### Text Input
```blade
<x-form.input name="title" label="Title" :required="true" />
```

### Email Input
```blade
<x-form.input name="email" type="email" label="Email" icon="envelope" />
```

### Number Input
```blade
<x-form.input name="price" type="number" step="0.01" label="Price" />
```

### Textarea
```blade
<x-form.textarea name="description" label="Description" :rows="4" />
```

### Select
```blade
<x-form.select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" />
```

### Checkbox
```blade
<x-form.checkbox name="is_active" label="Active" :checked="true" />
```

### File Upload
```blade
<x-form.file name="image" label="Image" :preview="true" />
```

---

## Common Patterns

### Input with Icon
```blade
<x-form.input name="phone" icon="phone" label="Phone" />
```

### Input with Help Text
```blade
<x-form.input name="slug" help-text="Used in URLs" />
```

### Required Field
```blade
<x-form.input name="name" :required="true" />
```

### With Default Value
```blade
<x-form.input name="sort_order" :value="0" />
```

---

## Error Display

### Top of Form
```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

---

## Controller Validation

```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);

try {
    Model::create($validated);
    return redirect()->route('...')->with('success', 'Done!');
} catch (\Exception $e) {
    return redirect()->back()->withInput()->with('error', $e->getMessage());
}
```

---

## Complete Form Template

```blade
@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Create Item</h1>
    
    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <x-form.input name="name" label="Name" :required="true" icon="tag" />
        
        <x-form.textarea name="description" label="Description" :rows="4" />
        
        <x-form.select 
            name="status" 
            label="Status"
            :options="['active' => 'Active', 'inactive' => 'Inactive']"
            :required="true"
        />
        
        <x-form.file name="image" label="Image" :preview="true" />
        
        <x-form.checkbox name="is_featured" label="Featured" />
        
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
```
