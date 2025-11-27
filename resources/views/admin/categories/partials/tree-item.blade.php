@php
    $levelClass = 'level-' . min($level, 4);
@endphp

<div class="tree-item {{ $levelClass }}" data-category-id="{{ $category->id }}">
    <div class="category-info">
        <div class="d-flex align-items-center">
            <input type="checkbox" 
                   name="category_ids[]" 
                   value="{{ $category->id }}" 
                   class="form-check-input category-checkbox"
                   id="category-{{ $category->id }}">
            @if($level > 0)
                <i class="fas fa-level-up-alt text-muted me-2" style="transform: rotate(90deg);"></i>
            @else
                <i class="fas fa-folder text-primary me-2"></i>
            @endif
            <div>
                <div class="category-name">{{ $category->name }}</div>
                @if($category->description)
                    <small class="text-muted">{{ Str::limit($category->description, 60) }}</small>
                @endif
            </div>
        </div>
        
        <div class="category-meta">
            <span class="badge-shops">
                <i class="fas fa-store"></i> {{ $category->shops_count }} متجر
            </span>
            
            @if($category->children->count() > 0)
                <span class="badge-children">
                    <i class="fas fa-sitemap"></i> {{ $category->children->count() }} فرعي
                </span>
            @endif
            
            <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                {{ $category->status === 'active' ? 'نشط' : 'غير نشط' }}
            </span>
            
            <div class="tree-actions">
                <a href="{{ route('admin.categories.edit', $category) }}" 
                   class="btn btn-sm btn-primary" 
                   title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="{{ route('admin.categories.show', $category) }}" 
                   class="btn btn-sm btn-info" 
                   title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" 
                      method="POST" 
                      class="d-inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟\n\nتنبيه: سيتم حذف جميع التصنيفات الفرعية أيضاً!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-sm btn-danger" 
                            title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($category->children->count() > 0)
    @foreach($category->children as $child)
        @include('admin.categories.partials.tree-item', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
