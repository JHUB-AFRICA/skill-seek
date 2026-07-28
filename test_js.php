<!DOCTYPE html>
<html>
<head>
    <title>JavaScript Test</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container" style="padding: 40px;">
        <h1>🧪 JavaScript Test Page</h1>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin: 20px 0;">
            <button class="btn btn-primary" onclick="showToast('Success message!', 'success')">
                Show Success Toast
            </button>
            <button class="btn btn-danger" onclick="showToast('Error message!', 'error')">
                Show Error Toast
            </button>
            <button class="btn btn-warning" onclick="showToast('Warning message!', 'warning')">
                Show Warning Toast
            </button>
            <button class="btn btn-info" onclick="openModal('testModal')">
                Open Modal
            </button>
            <button class="btn btn-secondary" onclick="confirmAction('Are you sure?', function() { alert('Confirmed!'); })">
                Confirm Action
            </button>
        </div>
        
        <div class="card" style="max-width: 400px;">
            <div class="card-header">
                <h3 class="card-title">Form Test</h3>
            </div>
            <div class="card-body">
                <form data-validate>
                    <div class="form-group">
                        <label>Name <span class="required">*</span></label>
                        <input type="text" class="form-control" required placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" class="form-control" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" class="form-control" required placeholder="Min 6 characters">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>
        </div>
        
        <div class="alert alert-success" style="margin-top: 20px;">
            This alert will auto-dismiss in 5 seconds
        </div>
        
        <div class="alert alert-error" style="margin-top: 10px;">
            This is an error alert
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal" id="testModal">
        <div class="modal-content">
            <h2>Modal Test</h2>
            <p>This is a test modal window. Click outside or press ESC to close.</p>
            <button class="btn btn-primary" onclick="closeModal('testModal')">Close</button>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>