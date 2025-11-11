@props(['shop', 'userRating' => null])

<div class="rating-form-container" data-shop-id="{{ $shop->id }}">
    @auth
        @if ($userRating)
            <!-- User has already rated - show their rating with edit option -->
            <div class="existing-rating" id="existing-rating">
                <div class="rating-header">
                    <h4 class="form-title">تقييمك لهذا المتجر</h4>
                    <button type="button" class="btn btn-sm btn-outline-primary edit-rating-btn">
                        <i class="fas fa-edit"></i> تعديل التقييم
                    </button>
                </div>
                
                <div class="rating-display">
                    <x-rating.display :rating="$userRating->rating" />
                    @if ($userRating->comment)
                        <div class="user-comment">
                            <strong>تعليقك:</strong> {{ $userRating->comment }}
                        </div>
                    @endif
                    <div class="rating-date">
                        تم التقييم في {{ $userRating->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Rating form (hidden if user has already rated) -->
        <div class="rating-form" id="rating-form" style="{{ $userRating ? 'display: none;' : '' }}">
            <div class="form-header">
                <h4 class="form-title">
                    {{ $userRating ? 'تعديل تقييمك' : 'قيم هذا المتجر' }}
                </h4>
                @if ($userRating)
                    <button type="button" class="btn btn-sm btn-secondary cancel-edit-btn">إلغاء</button>
                @endif
            </div>
            
            <form id="submit-rating-form" class="rating-submission-form">
                @csrf
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                
                <div class="form-group mb-3">
                    <label class="form-label">التقييم *</label>
                    <x-rating.input 
                        name="rating" 
                        :value="$userRating ? $userRating->rating : 0" 
                        :required="true" 
                        size="lg" 
                    />
                </div>
                
                <div class="form-group mb-3">
                    <label for="comment" class="form-label">تعليقك (اختياري)</label>
                    <textarea id="comment" 
                              name="comment" 
                              class="form-control" 
                              rows="4" 
                              placeholder="شاركنا رأيك في هذا المتجر..."
                              maxlength="1000">{{ $userRating ? $userRating->comment : '' }}</textarea>
                    <small class="form-text text-muted">حد أقصى 1000 حرف</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <span class="btn-text">{{ $userRating ? 'تحديث التقييم' : 'إرسال التقييم' }}</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> جاري الإرسال...
                        </span>
                    </button>
                    
                    @if ($userRating)
                        <button type="button" class="btn btn-danger" id="delete-rating-btn">
                            <i class="fas fa-trash"></i> حذف التقييم
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @else
        <!-- Not authenticated - show login prompt -->
        <div class="login-prompt">
            <div class="prompt-content">
                <h4>قيم هذا المتجر</h4>
                <p>يجب تسجيل الدخول أولاً لتتمكن من تقييم المتاجر</p>
                <div class="prompt-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">إنشاء حساب جديد</a>
                </div>
            </div>
        </div>
    @endauth
</div>

<style>
.rating-form-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
    margin-top: 24px;
}

.rating-header, .form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.form-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.existing-rating {
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.rating-display {
    margin-top: 12px;
}

.user-comment {
    margin-top: 12px;
    padding: 12px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.rating-date {
    margin-top: 8px;
    font-size: 0.875rem;
    color: #666;
}

.rating-submission-form .form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    padding: 12px;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn {
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-outline-primary {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline-primary:hover {
    background: #667eea;
    color: white;
}

.login-prompt {
    text-align: center;
    padding: 40px 20px;
}

.prompt-content h4 {
    color: #333;
    margin-bottom: 12px;
}

.prompt-content p {
    color: #666;
    margin-bottom: 24px;
}

.prompt-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-loading {
    display: none;
}

.btn.loading .btn-text {
    display: none;
}

.btn.loading .btn-loading {
    display: inline;
}

@media (max-width: 768px) {
    .rating-form-container {
        padding: 16px;
    }
    
    .rating-header, .form-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .prompt-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.rating-form-container');
    const shopId = container.dataset.shopId;
    const ratingForm = document.getElementById('rating-form');
    const existingRating = document.getElementById('existing-rating');
    const submitForm = document.getElementById('submit-rating-form');
    const editBtn = document.querySelector('.edit-rating-btn');
    const cancelBtn = document.querySelector('.cancel-edit-btn');
    const deleteBtn = document.getElementById('delete-rating-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    // Edit rating
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            existingRating.style.display = 'none';
            ratingForm.style.display = 'block';
        });
    }
    
    // Cancel edit
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            ratingForm.style.display = 'none';
            existingRating.style.display = 'block';
        });
    }
    
    // Submit rating
    if (submitForm) {
        submitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const ratingValue = formData.get('rating');
            
            if (!ratingValue || ratingValue == '0') {
                alert('يرجى اختيار تقييم');
                return;
            }
            
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            const isUpdate = existingRating !== null;
            const url = isUpdate ? `/ratings/${existingRating.dataset.ratingId}` : '/ratings';
            const method = isUpdate ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    shop_id: shopId,
                    rating: parseInt(ratingValue),
                    comment: formData.get('comment')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showMessage(data.message, 'success');
                    
                    // Reload page to show updated rating
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message || 'حدث خطأ أثناء إرسال التقييم', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('حدث خطأ أثناء إرسال التقييم', 'error');
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
    }
    
    // Delete rating
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من حذف تقييمك؟')) {
                const ratingId = existingRating.dataset.ratingId;
                
                fetch(`/ratings/${ratingId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showMessage(data.message || 'حدث خطأ أثناء حذف التقييم', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('حدث خطأ أثناء حذف التقييم', 'error');
                });
            }
        });
    }
    
    function showMessage(message, type) {
        // Simple toast notification (you can replace with your preferred notification system)
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} toast-message`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>