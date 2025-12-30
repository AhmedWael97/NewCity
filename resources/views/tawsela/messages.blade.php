@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-comments"></i> الرسائل والمحادثات</h2>
    
    <div class="row">
        <!-- Conversations List -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">المحادثات</h5>
                </div>
                <div class="card-body p-0" id="conversationsList" style="max-height: 600px; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-light" id="chatHeader">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2" id="chatHeaderContent">
                            <i class="fas fa-comments fa-2x text-muted"></i>
                            <span class="text-muted">اختر محادثة لبدء المراسلة</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body" id="chatMessages" style="height: 450px; overflow-y: auto;">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comment-slash fa-3x mb-3"></i>
                        <p>لا توجد محادثة محددة</p>
                    </div>
                </div>
                
                <div class="card-footer" id="messageForm" style="display: none;">
                    <form id="sendMessageForm" class="d-flex gap-2">
                        <input type="hidden" id="currentRideId">
                        <input type="hidden" id="currentReceiverId">
                        <input type="hidden" id="currentRequestId">
                        
                        <input type="text" name="message" id="messageInput" class="form-control" placeholder="اكتب رسالتك..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> إرسال
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.conversation-item {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: all 0.2s;
}

.conversation-item:hover {
    background: #f8f9fa;
}

.conversation-item.active {
    background: #e7f3ff;
    border-right: 3px solid #007bff;
}

.conversation-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.unread-badge {
    background: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 2px 8px;
    font-size: 0.75rem;
}

.message-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 10px;
}

.message-sent {
    background: #007bff;
    color: white;
    margin-right: auto;
    border-bottom-left-radius: 5px;
}

.message-received {
    background: #e9ecef;
    color: #000;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 5px;
}
</style>
@endpush

@push('scripts')
<script>
let conversations = [];
let currentConversation = null;
let messages = [];
let refreshInterval = null;

// Load conversations
async function loadConversations() {
    try {
        const response = await fetch('/api/v1/tawsela/conversations', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            conversations = data.data;
            renderConversations();
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
    }
}

function renderConversations() {
    const container = document.getElementById('conversationsList');
    
    if (conversations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>لا توجد محادثات بعد</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = conversations.map(conv => `
        <div class="conversation-item ${currentConversation && currentConversation.other_user_id === conv.other_user_id ? 'active' : ''}" 
             onclick="selectConversation(${conv.other_user_id}, ${conv.ride_id}, ${conv.request_id || 'null'})">
            <div class="d-flex align-items-start gap-3">
                <img src="${conv.other_user.avatar || '/images/default-avatar.png'}" alt="${conv.other_user.name}" class="conversation-avatar">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1">${conv.other_user.name}</h6>
                        ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
                    </div>
                    <small class="text-muted d-block mb-1">
                        <i class="fas fa-route"></i>
                        ${conv.ride.start_address.substring(0, 30)}... → ${conv.ride.destination_address.substring(0, 30)}...
                    </small>
                    <small class="text-muted">${conv.last_message.message.substring(0, 40)}...</small>
                </div>
            </div>
        </div>
    `).join('');
}

// Select conversation
async function selectConversation(userId, rideId, requestId) {
    currentConversation = conversations.find(c => c.other_user_id === userId && c.ride_id === rideId);
    
    document.getElementById('currentRideId').value = rideId;
    document.getElementById('currentReceiverId').value = userId;
    document.getElementById('currentRequestId').value = requestId || '';
    
    // Update header
    document.getElementById('chatHeaderContent').innerHTML = `
        <img src="${currentConversation.other_user.avatar || '/images/default-avatar.png'}" 
             alt="${currentConversation.other_user.name}" 
             class="conversation-avatar">
        <div>
            <h6 class="mb-0">${currentConversation.other_user.name}</h6>
            <small class="text-muted">
                <i class="fas fa-route"></i> ${currentConversation.ride.start_address.substring(0, 40)}...
            </small>
        </div>
    `;
    
    // Show message form
    document.getElementById('messageForm').style.display = 'block';
    
    // Load messages
    await loadMessages(rideId, userId);
    
    // Render conversations to update active state
    renderConversations();
}

// Load messages
async function loadMessages(rideId, userId) {
    try {
        const response = await fetch(`/api/v1/tawsela/messages?ride_id=${rideId}&user_id=${userId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            messages = data.data.data.reverse(); // Reverse to show oldest first
            renderMessages();
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

function renderMessages() {
    const container = document.getElementById('chatMessages');
    const currentUserId = {{ auth()->id() ?? 'null' }};
    
    if (messages.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-comment-dots fa-3x mb-3"></i>
                <p>لا توجد رسائل بعد. ابدأ المحادثة!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = messages.map(msg => {
        const isSent = msg.sender_id === currentUserId;
        return `
            <div class="d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'} mb-3">
                <div class="message-bubble ${isSent ? 'message-sent' : 'message-received'}">
                    <div>${msg.message}</div>
                    <div class="message-time ${isSent ? 'text-white-50' : ''}">
                        ${new Date(msg.created_at).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' })}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function scrollToBottom() {
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}

// Send message
document.getElementById('sendMessageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    const data = {
        ride_id: parseInt(document.getElementById('currentRideId').value),
        receiver_id: parseInt(document.getElementById('currentReceiverId').value),
        message: message
    };
    
    const requestId = document.getElementById('currentRequestId').value;
    if (requestId) {
        data.request_id = parseInt(requestId);
    }
    
    try {
        const response = await fetch('/api/v1/tawsela/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            messageInput.value = '';
            await loadMessages(data.ride_id, data.receiver_id);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('حدث خطأ في إرسال الرسالة');
    }
});

// Auto-refresh messages
function startAutoRefresh() {
    refreshInterval = setInterval(async () => {
        if (currentConversation) {
            const rideId = parseInt(document.getElementById('currentRideId').value);
            const userId = parseInt(document.getElementById('currentReceiverId').value);
            await loadMessages(rideId, userId);
        }
        await loadConversations();
    }, 5000); // Refresh every 5 seconds
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
    startAutoRefresh();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
@endpush
@endsection
