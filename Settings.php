<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade - Settings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --dark-bg: #1a1a1a;
            --dark-secondary: #2d2d2d;
        }

        .settings-nav .nav-link {
            color: #333;
            padding: 1rem;
            border-radius: 0;
            border-left: 3px solid transparent;
        }

        .settings-nav .nav-link.active {
            background-color: #f8f9fa;
            border-left: 3px solid var(--primary-color);
            color: var(--primary-color);
        }

        .dark-mode {
            background-color: var(--dark-bg);
            color: #fff;
        }

        .dark-mode .card {
            background-color: var(--dark-secondary);
            border-color: #404040;
        }

        .dark-mode .settings-nav .nav-link {
            color: #fff;
        }

        .dark-mode .settings-nav .nav-link.active {
            background-color: var(--dark-secondary);
        }

        .avatar-upload {
            position: relative;
            max-width: 200px;
        }

        .avatar-upload img {
            width: 100%;
            height: auto;
            border-radius: 50%;
        }

        .avatar-upload .upload-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            padding: 0.5rem;
            text-align: center;
            color: white;
            cursor: pointer;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .security-badge {
            padding: 0.5rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .security-badge.success {
            background-color: #d4edda;
            color: #155724;
        }

        .security-badge.warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .preference-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">CarTrade</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="homepage.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Buy Car</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Sell Car</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="your-cars.php">Your Cars</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 settings-nav">
                <div class="nav flex-column nav-pills">
                    <a class="nav-link active" href="#profile" data-bs-toggle="pill">
                        <i class="fas fa-user me-2"></i>Profile</a>
                    <a class="nav-link" href="#notifications" data-bs-toggle="pill">
                        <i class="fas fa-bell me-2"></i>Notifications</a>
                    <a class="nav-link" href="#security" data-bs-toggle="pill">
                        <i class="fas fa-shield-alt me-2"></i>Security</a>
                    <a class="nav-link" href="#preferences" data-bs-toggle="pill">
                        <i class="fas fa-cog me-2"></i>Preferences</a>
                    <a class="nav-link" href="#billing" data-bs-toggle="pill">
                        <i class="fas fa-credit-card me-2"></i>Billing </a>
                    <a class="nav-link" href="#privacy" data-bs-toggle="pill">
                        <i class="fas fa-lock me-2"></i>Privacy</a>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="tab-content py-4">
                    <div class="tab-pane fade show active" id="profile">
                        <h2 class="mb-4">Profile Settings</h2>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="avatar-upload mb-3">
                                            <img src="/api/placeholder/200/200" alt="Profile Picture">
                                            <div class="upload-overlay">
                                                <i class="fas fa-camera"></i> Change Photo
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <form>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">First Name</label>
                                                    <input type="text" class="form-control" value="John">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="john@example.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="tel" class="form-control" value="+1 234 567 890">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bio</label>
                                                <textarea class="form-control" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="notifications">
                        <h2 class="mb-4">Notification Settings</h2>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">Email Notifications</h5>
                                <div class="notification-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">New Messages</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Car Listing Updates</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Marketing Emails</label>
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4">Push Notifications</h5>
                                <div class="notification-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Transaction Updates</label>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Price Alerts</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="security">
                        <h2 class="mb-4">Security Settings</h2>
                        <div class="card">
                            <div class="card-body">
                                <div class="security-badge success mb-4">
                                    <i class="fas fa-check-circle"></i> Your account is secure
                                </div>

                                <h5>Change Password</h5>
                                <form class="mb-4">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </form>

                                <h5>Two-Factor Authentication</h5>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Enable 2FA</label>
                                    </div>
                                </div>

                                <h5>Login History</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Device</th>
                                                <th>Location</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Chrome on Windows</td>
                                                <td>New York, USA</td>
                                                <td>2024-02-13 10:30 AM</td>
                                            </tr>
                                            <tr>
                                                <td>Safari on iPhone</td>
                                                <td>New York, USA</td>
                                                <td>2024-02-12 3:45 PM</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="preferences">
                        <h2 class="mb-4">Preferences</h2>
                        <div class="card">
                            <div class="card-body">
                                <div class="preference-item">
                                    <h5>Language</h5>
                                    <select class="form-select">
                                        <option>English</option>
                                        <option>Spanish</option>
                                        <option>French</option>
                                    </select>
                                </div>

                                <div class="preference-item">
                                    <h5>Theme</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" checked>
                                        <label class="form-check-label">Light Mode</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme">
                                        <label class="form-check-label">Dark Mode</label>
                                    </div>
                                </div>

                                <div class="preference-item">
                                    <h5>Currency</h5>
                                    <select class="form-select">
                                        <option>USD ($)</option>
                                        <option>EUR (€)</option>
                                        <option>GBP (£)</option>
                                    </select>
                                </div>

                                <div class="preference-item">
                                    <h5>Distance Unit</h5>
                                    <select class="form-select">
                                        <option>Miles</option>
                                        <option>Kilometers</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="billing">
                        <h2 class="mb-4">Billing Settings</h2>
                        <div class="card">
                            <div class="card-body">
                                <h5>Payment Methods</h5>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fab fa-cc-visa fa-2x me-2"></i>
                                    <span>Visa ending in 4242</span>
                                    <button class="btn btn-sm btn-outline-danger ms-auto">Remove</button>
                                </div>

                                <button class="btn btn-primary mb-4">Add Payment Method</button>

                                <h5>Billing History</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2024-02-13</td>
                                                <td>Premium Listing</td>
                                                <td>$49.99</td>
                                                <td><span class="badge bg-success">Paid</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="privacy">
                        <h2 class="mb-4">Privacy Settings</h2>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4">
                                    <h5>Profile Visibility</h5>
                                    <select class="form-select">
                                        <option>Public</option>
                                        <option>Private</option>
                                        <option>Friends Only</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <h5>Data Sharing</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Share data with partners</label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5>Cookie Policy</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Accept cookies</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>