<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SSFO eLog</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="../assets/js/main.js" defer></script>
</head>

<body>
    <!-- Admin Navbar -->
    <nav class="admin-navbar">
        <div class="logo">
            <button id="sidebar-toggle" aria-label="Toggle Sidebar">☰</button>
            <img src="../images/logos.png" alt="SSFO logo" height="40">
            <h1>eLog Admin</h1>
        </div>
        <div class="admin-title">Dashboard</div>
        <div class="user-menu">
            <button class="btn btn-outline" style="color: white; border-color: white;"
                onclick="window.location.href='../user/index.php'">Logout</button>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Navigation</h2>
        </div>
        <nav>
            <ul>
                <li><a href="index.php" class="nav-link active">Dashboard</a></li>

                <li class="nav-section-title">Requests</li>
                <li><a href="#" class="nav-link">Educational</a></li>
                <li><a href="#" class="nav-link">Medical</a></li>
                <li><a href="#" class="nav-link">Burial</a></li>
                <li><a href="#" class="nav-link">Employment</a></li>
                <li><a href="#" class="nav-link">Transportation</a></li>

                <li class="nav-section-title">System</li>
                <li><a href="#" class="nav-link">Reports</a></li>
                <li><a href="#" class="nav-link">Settings</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="dashboard-header animate-fade-up">
            <h1>Overview</h1>
            <p>Welcome back, Administrator. Here is today's summary.</p>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards animate-fade-up">
            <div class="card">
                <h3>Total Requests</h3>
                <p class="metric">1,248</p>
            </div>
            <div class="card">
                <h3>Pending Review</h3>
                <p class="metric" style="color: #d97706;">42</p>
            </div>
            <div class="card">
                <h3>Approved Today</h3>
                <p class="metric" style="color: #059669;">18</p>
            </div>
            <div class="card">
                <h3>Rejected</h3>
                <p class="metric" style="color: #dc2626;">5</p>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="status-summary animate-fade-up">
            <h2>Request Status Distribution</h2>
            <div class="status-cards">
                <div class="status-card pending">
                    <h4>Pending</h4>
                    <div class="bar" style="width: 35%;"></div>
                    <span>35%</span>
                </div>
                <div class="status-card approved">
                    <h4>Approved</h4>
                    <div class="bar" style="width: 55%;"></div>
                    <span>55%</span>
                </div>
                <div class="status-card rejected">
                    <h4>Rejected</h4>
                    <div class="bar" style="width: 10%;"></div>
                    <span>10%</span>
                </div>
            </div>
        </div>

        <!-- Recent Requests Table -->
        <div class="recent-requests animate-fade-up">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h2 style="margin: 0;">Recent Requests</h2>
                <div class="search-form" style="margin: 0; width: 300px;">
                    <input type="text" placeholder="Search requests...">
                </div>
            </div>

            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1024</td>
                            <td>Juan Dela Cruz</td>
                            <td>Medical</td>
                            <td>Jan 20, 2026</td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td>
                                <button class="btn view" aria-label="View Details">View</button>
                                <button class="btn approve" aria-label="Approve">Approve</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#1023</td>
                            <td>Maria Clara</td>
                            <td>Educational</td>
                            <td>Jan 19, 2026</td>
                            <td><span class="status-badge approved">Approved</span></td>
                            <td>
                                <button class="btn view" aria-label="View Details">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#1022</td>
                            <td>Jose Rizal</td>
                            <td>Burial</td>
                            <td>Jan 18, 2026</td>
                            <td><span class="status-badge rejected">Rejected</span></td>
                            <td>
                                <button class="btn view" aria-label="View Details">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>