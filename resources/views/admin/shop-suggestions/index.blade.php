@extends('layouts.admin')

@section('title', 'اقتراحات المتاجر')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-lightbulb"></i> اقتراحات المتاجر
        </h1>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.shop-suggestions.index') }}">
                        الكل <span class="badge bg-secondary">{{ $statusCounts['all'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('admin.shop-suggestions.index', ['status' => 'pending']) }}">
                        قيد الانتظار <span class="badge bg-warning">{{ $statusCounts['pending'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('admin.shop-suggestions.index', ['status' => 'approved']) }}">
                        موافق عليها <span class="badge bg-success">{{ $statusCounts['approved'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('admin.shop-suggestions.index', ['status' => 'rejected']) }}">
                        مرفوضة <span class="badge bg-danger">{{ $statusCounts['rejected'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('admin.shop-suggestions.index', ['status' => 'completed']) }}">
                        مكتملة <span class="badge bg-info">{{ $statusCounts['completed'] }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if($suggestions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم</th>
                                <th>اسم المتجر</th>
                                <th>المدينة</th>
                                <th>الفئة</th>
                                <th>اقترحه</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suggestions as $suggestion)
                                <tr>
                                    <td>{{ $suggestion->id }}</td>
                                    <td>
                                        <strong>{{ $suggestion->shop_name }}</strong>
                                        @if($suggestion->phone)
                                            <br><small class="text-muted"><i class="fas fa-phone"></i> {{ $suggestion->phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $suggestion->city->name ?? '-' }}</td>
                                    <td>{{ $suggestion->category->name ?? '-' }}</td>
                                    <td>
                                        {{ $suggestion->suggested_by_name }}
                                        <br><small class="text-muted">{{ $suggestion->suggested_by_phone }}</small>
                                    </td>
                                    <td>{{ $suggestion->created_at->diffForHumans() }}</td>
                                    <td>
                                        @switch($suggestion->status)
                                            @case('pending')
                                                <span class="badge bg-warning">قيد الانتظار</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success">موافق عليه</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">مكتمل</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.shop-suggestions.show', $suggestion) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($suggestion->status == 'pending')
                                            <form action="{{ route('admin.shop-suggestions.update-status', $suggestion) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success" 
                                                        title="موافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.shop-suggestions.update-status', $suggestion) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="رفض">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.shop-suggestions.destroy', $suggestion) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الاقتراح؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suggestions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">لا توجد اقتراحات</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
