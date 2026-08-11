<?php
// ============================================
// SkillSeek - Chat System
// File: api/chat.php
// Description: Real-time messaging between users
// ============================================

// Include configuration
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];

// ============================================
// HANDLE API REQUESTS
// ============================================

// Get messages (AJAX)
if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
    header('Content-Type: application/json');
    
    $receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
    $job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : null;
    
    if ($receiver_id <= 0) {
        echo json_encode(['error' => 'Invalid receiver']);
        exit();
    }
    
    // Get messages between users
    $sql = "
        SELECT m.*, u.full_name as sender_name 
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
    ";
    $params = [$user_id, $receiver_id, $receiver_id, $user_id];
    
    if ($job_id) {
        $sql .= " AND m.job_id = ?";
        $params[] = $job_id;
    }
    
    $sql .= " ORDER BY m.created_at ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();
    
    // Mark messages as read
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET is_read = 1, read_at = NOW() 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ");
    $stmt->execute([$receiver_id, $user_id]);
    
    echo json_encode($messages);
    exit();
}

// Send message (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    header('Content-Type: application/json');
    
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $job_id = isset($_POST['job_id']) && !empty($_POST['job_id']) ? intval($_POST['job_id']) : null;
    
    if ($receiver_id <= 0 || empty($message)) {
        echo json_encode(['error' => 'Invalid message data']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, job_id) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $receiver_id, $message, $job_id]);
        $message_id = $pdo->lastInsertId();
        
        // Create notification for receiver
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, link) 
            VALUES (?, 'message', ?, ?, ?)
        ");
        $notification_title = 'New Message';
        $notification_message = $user_name . ' sent you a message';
        $notification_link = '/api/chat.php?user=' . $user_id;
        $stmt->execute([$receiver_id, $notification_title, $notification_message, $notification_link]);
        
        echo json_encode([
            'success' => true,
            'message_id' => $message_id,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Error sending message: ' . $e->getMessage()]);
    }
    exit();
}

// Get contacts (AJAX)
if (isset($_GET['action']) && $_GET['action'] === 'get_contacts') {
    header('Content-Type: application/json');
    
    // Get users that the current user has chatted with
    $sql = "
        SELECT DISTINCT 
            u.id,
            u.full_name,
            u.role,
            u.profile_pic,
            (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count,
            (SELECT MAX(created_at) FROM messages WHERE (sender_id = ? AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = ?)) as last_message_time
        FROM users u
        WHERE u.id != ?
        AND EXISTS (
            SELECT 1 FROM messages 
            WHERE (sender_id = ? AND receiver_id = u.id) 
            OR (sender_id = u.id AND receiver_id = ?)
        )
        ORDER BY last_message_time DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
    $contacts = $stmt->fetchAll();
    
    // Also get employers/students who the user has job relationships with
    // If employer, get students who applied to their jobs
    if ($user_role === 'employer') {
        $sql = "
            SELECT DISTINCT 
                u.id,
                u.full_name,
                u.role,
                u.profile_pic,
                0 as unread_count,
                NULL as last_message_time
            FROM users u
            JOIN applications a ON u.id = a.student_id
            JOIN jobs j ON a.job_id = j.id
            WHERE j.employer_id = ?
            AND u.id NOT IN (SELECT DISTINCT 
                CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END 
                FROM messages 
                WHERE sender_id = ? OR receiver_id = ?
            )
            AND u.id != ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
        $more_contacts = $stmt->fetchAll();
        $contacts = array_merge($contacts, $more_contacts);
    }
    
    // If student, get employers who posted jobs they applied to
    if ($user_role === 'student') {
        $sql = "
            SELECT DISTINCT 
                u.id,
                u.full_name,
                u.role,
                u.profile_pic,
                0 as unread_count,
                NULL as last_message_time
            FROM users u
            JOIN jobs j ON u.id = j.employer_id
            JOIN applications a ON j.id = a.job_id
            WHERE a.student_id = ?
            AND u.id NOT IN (SELECT DISTINCT 
                CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END 
                FROM messages 
                WHERE sender_id = ? OR receiver_id = ?
            )
            AND u.id != ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
        $more_contacts = $stmt->fetchAll();
        $contacts = array_merge($contacts, $more_contacts);
    }
    
    // Remove duplicates
    $unique_contacts = [];
    $ids = [];
    foreach ($contacts as $contact) {
        if (!in_array($contact['id'], $ids)) {
            $ids[] = $contact['id'];
            $unique_contacts[] = $contact;
        }
    }
    
    echo json_encode($unique_contacts);
    exit();
}

// Get unread count (AJAX)
if (isset($_GET['action']) && $_GET['action'] === 'get_unread') {
    header('Content-Type: application/json');
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $count = $stmt->fetch()['count'];
    
    echo json_encode(['unread' => $count]);
    exit();
}

// ============================================
// GET CONTACT FOR CHAT (if specified)
// ============================================
$contact_id = isset($_GET['user']) && is_numeric($_GET['user']) ? intval($_GET['user']) : 0;
$contact_name = '';

if ($contact_id > 0) {
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$contact_id]);
    $contact = $stmt->fetch();
    if ($contact) {
        $contact_name = $contact['full_name'];
    }
}

// Get job ID for context
$job_id = isset($_GET['job']) && is_numeric($_GET['job']) ? intval($_GET['job']) : null;

// Set page title
$page_title = 'Messages - SkillSeek';

// Include header
include '../includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT
     ============================================================ -->
<div class="dashboard-container">
    
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-profile">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h3><?php echo htmlspecialchars($user_name); ?></h3>
            <span class="role-badge <?php echo $user_role; ?>"><?php echo ucfirst($user_role); ?></span>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <?php if ($user_role === 'employer'): ?>
                    <li><a href="../employer/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="../employer/post_job.php"><i class="fas fa-plus-circle"></i> Post Job</a></li>
                    <li><a href="../employer/my_jobs.php"><i class="fas fa-briefcase"></i> My Jobs</a></li>
                    <li><a href="../employer/applications.php"><i class="fas fa-users"></i> Applications</a></li>
                <?php else: ?>
                    <li><a href="../student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="../student/available_jobs.php"><i class="fas fa-search"></i> Find Jobs</a></li>
                    <li><a href="../student/my_applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                    <li><a href="../student/saved_jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a></li>
                <?php endif; ?>
                <li class="active"><a href="chat.php"><i class="fas fa-comment-dots"></i> Messages</a></li>
                <li><a href="../profile.php"><i class="fas fa-user-edit"></i> Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="dashboard-main chat-page">
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Messages</h1>
            <p>Chat with employers and students</p>
        </div>
        
        <!-- Chat Container -->
        <div class="chat-container">
            
            <!-- Contacts Sidebar -->
            <div class="chat-contacts">
                <div class="chat-contacts-header">
                    <h3><i class="fas fa-users"></i> Contacts</h3>
                    <span class="contact-count" id="contactCount">0</span>
                </div>
                <div class="chat-contacts-list" id="contactsList">
                    <div class="loading-contacts">Loading contacts...</div>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="chat-area-container">
                <?php if ($contact_id > 0): ?>
                    <div class="chat-header-info">
                        <div class="chat-user-info">
                            <i class="fas fa-user-circle" style="font-size: 32px; color: #4F46E5;"></i>
                            <div>
                                <h4><?php echo htmlspecialchars($contact_name); ?></h4>
                                <span class="chat-user-role"><?php echo $contact_id > 0 ? 'Online' : ''; ?></span>
                            </div>
                        </div>
                        <?php if ($job_id): ?>
                            <div class="chat-job-context">
                                <i class="fas fa-briefcase"></i>
                                <span>Regarding: Job #<?php echo $job_id; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-messages-container" id="chatMessagesContainer">
                        <div class="chat-messages" id="chatMessages">
                            <div class="loading-messages">Loading messages...</div>
                        </div>
                    </div>
                    
                    <div class="chat-input-container">
                        <form id="chatForm" onsubmit="sendMessage(); return false;">
                            <input type="hidden" id="receiverId" value="<?php echo $contact_id; ?>">
                            <input type="hidden" id="jobId" value="<?php echo $job_id; ?>">
                            <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="chat-empty-state">
                        <i class="fas fa-comment-dots"></i>
                        <h3>Select a conversation</h3>
                        <p>Choose a contact from the left to start messaging</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
        
    </main>
</div>

<!-- ============================================================
     CHAT JAVASCRIPT
     ============================================================ -->
<script>
// ============================================
// CHAT SYSTEM
// ============================================

let currentReceiverId = <?php echo $contact_id; ?>;
let currentJobId = <?php echo $job_id ?: 'null'; ?>;
let userId = <?php echo $user_id; ?>;
let pollInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    loadContacts();
    
    if (currentReceiverId > 0) {
        loadMessages(currentReceiverId, currentJobId);
        startPolling();
    }
});

// ============================================
// LOAD CONTACTS
// ============================================
function loadContacts() {
    fetch('?action=get_contacts')
        .then(response => response.json())
        .then(contacts => {
            const list = document.getElementById('contactsList');
            const count = document.getElementById('contactCount');
            
            if (contacts.length === 0) {
                list.innerHTML = `
                    <div class="no-contacts">
                        <i class="fas fa-users"></i>
                        <p>No contacts yet</p>
                        <small>Start conversations with employers or students</small>
                    </div>
                `;
                count.textContent = '0';
                return;
            }
            
            count.textContent = contacts.length;
            
            list.innerHTML = contacts.map(contact => `
                <a href="?user=${contact.id}" class="contact-item ${contact.id === currentReceiverId ? 'active' : ''}">
                    <div class="contact-avatar">
                        <i class="fas fa-user-circle"></i>
                        ${contact.unread_count > 0 ? `<span class="unread-badge">${contact.unread_count}</span>` : ''}
                    </div>
                    <div class="contact-info">
                        <strong>${escapeHtml(contact.full_name)}</strong>
                        <small>${contact.role}</small>
                    </div>
                </a>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading contacts:', error);
            document.getElementById('contactsList').innerHTML = '<div class="error">Error loading contacts</div>';
        });
}

// ============================================
// LOAD MESSAGES
// ============================================
function loadMessages(receiverId, jobId) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '<div class="loading-messages">Loading messages...</div>';
    
    let url = `?action=get_messages&receiver_id=${receiverId}`;
    if (jobId) {
        url += `&job_id=${jobId}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(messages => {
            if (messages.error) {
                container.innerHTML = `<div class="error-message">${messages.error}</div>`;
                return;
            }
            
            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="no-messages">
                        <i class="fas fa-comment"></i>
                        <p>No messages yet</p>
                        <small>Start the conversation!</small>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = messages.map(msg => `
                <div class="message ${msg.sender_id == userId ? 'sent' : 'received'}">
                    <div class="message-content">${escapeHtml(msg.message)}</div>
                    <div class="message-time">${formatTime(msg.created_at)}</div>
                </div>
            `).join('');
            
            // Scroll to bottom
            const containerDiv = document.getElementById('chatMessagesContainer');
            containerDiv.scrollTop = containerDiv.scrollHeight;
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            container.innerHTML = '<div class="error-message">Error loading messages</div>';
        });
}

// ============================================
// SEND MESSAGE
// ============================================
function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    const receiverId = document.getElementById('receiverId').value;
    const jobId = document.getElementById('jobId').value;
    
    if (!message || !receiverId) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('receiver_id', receiverId);
    formData.append('message', message);
    if (jobId) {
        formData.append('job_id', jobId);
    }
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages(parseInt(receiverId), jobId ? parseInt(jobId) : null);
        } else {
            alert(data.error || 'Error sending message');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message. Please try again.');
    });
}

// ============================================
// START POLLING FOR NEW MESSAGES
// ============================================
function startPolling() {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
    
    pollInterval = setInterval(() => {
        if (currentReceiverId > 0) {
            loadMessages(currentReceiverId, currentJobId);
        }
    }, 3000);
}

function stopPolling() {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Send message on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('messageInput');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});

// Stop polling when leaving page
window.addEventListener('beforeunload', function() {
    stopPolling();
});

// ============================================
// UPDATE UNREAD COUNT IN HEADER
// ============================================
function updateUnreadCount() {
    fetch('?action=get_unread')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.badge-notification');
            if (badge) {
                if (data.unread > 0) {
                    badge.textContent = data.unread;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching unread count:', error));
}

// Update unread count every 10 seconds
setInterval(updateUnreadCount, 10000);
</script>

<style>
/* ============================================================
   CHAT PAGE STYLES
   ============================================================ */

.chat-page .dashboard-main {
    padding: 16px 24px;
}

.chat-container {
    display: flex;
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    overflow: hidden;
    height: 600px;
}

/* ---- Contacts Sidebar ---- */
.chat-contacts {
    width: 320px;
    border-right: 1px solid #E2E8F0;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.chat-contacts-header {
    padding: 16px 20px;
    border-bottom: 1px solid #E2E8F0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #F8FAFC;
}

.chat-contacts-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
}

.chat-contacts-header h3 i {
    color: #4F46E5;
    margin-right: 8px;
}

.contact-count {
    background: #EEF2FF;
    color: #4F46E5;
    padding: 2px 10px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
}

.chat-contacts-list {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #0F172A;
    transition: all 0.2s ease;
    position: relative;
}

.contact-item:hover {
    background: #F8FAFC;
}

.contact-item.active {
    background: #EEF2FF;
}

.contact-avatar {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #94A3B8;
    flex-shrink: 0;
}

.unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #EF4444;
    color: #FFFFFF;
    font-size: 10px;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #FFFFFF;
}

.contact-info strong {
    display: block;
    font-size: 14px;
    font-weight: 600;
}

.contact-info small {
    font-size: 12px;
    color: #94A3B8;
}

/* ---- Chat Area ---- */
.chat-area-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #F8FAFC;
}

.chat-header-info {
    padding: 12px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid #E2E8F0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-user-info h4 {
    font-size: 16px;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
}

.chat-user-role {
    font-size: 12px;
    color: #22C55E;
}

.chat-job-context {
    font-size: 13px;
    color: #64748B;
    background: #F1F5F9;
    padding: 4px 12px;
    border-radius: 9999px;
}

.chat-job-context i {
    color: #4F46E5;
    margin-right: 4px;
}

/* ---- Messages ---- */
.chat-messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 16px 20px;
}

.chat-messages {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-height: 100%;
}

.message {
    max-width: 70%;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.message.sent {
    background: #4F46E5;
    color: #FFFFFF;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

.message.received {
    background: #FFFFFF;
    color: #0F172A;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.message-time {
    font-size: 10px;
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
}

.message.sent .message-time {
    color: rgba(255,255,255,0.8);
}

.message.received .message-time {
    color: #94A3B8;
}

/* ---- Chat Input ---- */
.chat-input-container {
    padding: 12px 20px;
    background: #FFFFFF;
    border-top: 1px solid #E2E8F0;
}

#chatForm {
    display: flex;
    gap: 12px;
}

#chatForm input[type="text"] {
    flex: 1;
    padding: 10px 16px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s ease;
}

#chatForm input[type="text"]:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

#chatForm button {
    padding: 10px 20px;
    flex-shrink: 0;
}

/* ---- Empty States ---- */
.chat-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    padding: 40px;
}

.chat-empty-state i {
    font-size: 48px;
    color: #CBD5E1;
    margin-bottom: 16px;
}

.chat-empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 4px;
}

.chat-empty-state p {
    font-size: 14px;
    color: #94A3B8;
}

.loading-contacts,
.no-contacts,
.error {
    text-align: center;
    padding: 40px 20px;
    color: #94A3B8;
}

.no-contacts i {
    font-size: 32px;
    color: #CBD5E1;
    display: block;
    margin-bottom: 8px;
}

.loading-messages,
.no-messages {
    text-align: center;
    padding: 40px 20px;
    color: #94A3B8;
}

.no-messages i {
    font-size: 32px;
    color: #CBD5E1;
    display: block;
    margin-bottom: 8px;
}

.error-message {
    text-align: center;
    padding: 20px;
    color: #EF4444;
}

/* ---- Responsive ---- */
@media (max-width: 768px) {
    .chat-container {
        flex-direction: column;
        height: 500px;
    }
    
    .chat-contacts {
        width: 100%;
        height: 200px;
        border-right: none;
        border-bottom: 1px solid #E2E8F0;
    }
    
    .chat-area-container {
        flex: 1;
        min-height: 300px;
    }
    
    .chat-header-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .message {
        max-width: 85%;
    }
}

@media (max-width: 480px) {
    .chat-contacts {
        height: 150px;
    }
    
    .chat-messages-container {
        padding: 12px;
    }
    
    .chat-input-container {
        padding: 10px 12px;
    }
    
    #chatForm input[type="text"] {
        font-size: 13px;
        padding: 8px 12px;
    }
    
    #chatForm button {
        padding: 8px 14px;
        font-size: 13px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>