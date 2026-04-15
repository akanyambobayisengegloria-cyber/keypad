<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendly - Smart Attendance System | Connected to Database</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-header h1 {
            font-size: 28px;
            font-weight: bold;
        }

        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            margin: 5px 15px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            color: rgba(255,255,255,0.85);
            font-size: 16px;
            font-weight: 500;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.25);
            color: white;
        }

        .connection-status-sidebar {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 8px;
        }

        .status-badge.online {
            background: #4CAF50;
            color: white;
        }

        .status-badge.offline {
            background: #f44336;
            color: white;
        }

        .status-badge.syncing {
            background: #ff9800;
            color: white;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 25px 35px;
            background: #f0f2f5;
            min-height: 100vh;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .sync-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .sync-btn:hover {
            background: #218838;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e8e8e8;
        }

        .card h2 {
            margin-bottom: 20px;
            color: #333;
            border-left: 4px solid #667eea;
            padding-left: 15px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-primary, .btn-secondary, .btn-danger, .btn-small, .btn-success {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        .live-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
        }

        .data-table {
            overflow-x: auto;
            margin-top: 20px;
        }

        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background: #f5f5f5;
            font-weight: bold;
            color: #555;
        }

        .data-table tr:hover {
            background: #f9f9f9;
        }

        .status-present { color: #4CAF50; font-weight: bold; }
        .status-late { color: #ff9800; font-weight: bold; }
        .status-absent { color: #f44336; font-weight: bold; }
        .status-active { color: #28a745; font-weight: bold; }
        .status-completed { color: #6c757d; font-weight: bold; }
        .status-scheduled { color: #5100ff; font-weight: bold; }

        .sessions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .session-card {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #667eea;
        }

        .session-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .session-card p {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .session-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .active-session-info {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-bar select,
        .filter-bar input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .trade-badge {
            display: inline-block;
            background: #e9ecef;
            color: #667eea;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            margin-left: 8px;
        }

        .level-badge {
            display: inline-block;
            background: #764ba2;
            color: white;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar-header h1, .sidebar-header p, .nav-item span { display: none; }
            .nav-item { justify-content: center; padding: 14px 0; }
            .main-content { margin-left: 80px; padding: 15px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Attendly</h1>
            <p>Smart Attendance | Database Connected</p>
        </div>
        <div class="sidebar-nav">
            <div class="nav-item" data-tab="dashboard"><span>Dashboard</span></div>
            <div class="nav-item" data-tab="sessions"><span>Sessions</span></div>
            <div class="nav-item" data-tab="live"><span>Live View</span></div>
            <div class="nav-item" data-tab="students"><span>Students</span></div>
            <div class="nav-item" data-tab="reports"><span>Reports</span></div>
        </div>
        <div class="connection-status-sidebar">
            <div>API Status</div>
            <span id="api-status-sidebar" class="status-badge offline">Offline</span>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div class="page-title" id="page-title">Dashboard</div>
            <button class="sync-btn" onclick="syncAllData()">Sync with Database</button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content">
            <div class="card">
                <h2>System Overview</h2>
                <div class="live-stats">
                    <div class="stat-card"><h3>Total Students</h3><p id="total-students">0</p></div>
                    <div class="stat-card"><h3>Total Sessions</h3><p id="total-sessions">0</p></div>
                    <div class="stat-card"><h3>Today's Attendance</h3><p id="today-attendance">0</p></div>
                    <div class="stat-card"><h3>Total Records</h3><p id="total-records">0</p></div>
                </div>
                <canvas id="dashboard-chart" width="400" height="200" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Sessions Tab -->
        <div id="sessions" class="tab-content">
            <div class="card">
                <h2>Create New Session</h2>
                <form id="session-form">
                    <div class="form-group"><label>Session Title:</label><input type="text" id="session-title" required placeholder="e.g., Monday Morning Lecture"></div>
                    <div class="form-row">
                        <div class="form-group"><label>Date:</label><input type="date" id="session-date" required></div>
                        <div class="form-group"><label>Start Time:</label><input type="time" id="session-start" required></div>
                        <div class="form-group"><label>End Time:</label><input type="time" id="session-end" required></div>
                    </div>
                    <div class="form-group"><label>Select Participants:</label><select id="participants-select" multiple size="5"></select></div>
                    <button type="submit" class="btn-primary">Create Session</button>
                </form>
            </div>
            <div class="card">
                <h2>Session Records Table</h2>
                <div class="filter-bar"><input type="text" id="session-search" placeholder="Search by title..." onkeyup="filterSessionTable()"><select id="session-status-filter" onchange="filterSessionTable()"><option value="">All Status</option><option value="scheduled">Scheduled</option><option value="active">Active</option><option value="completed">Completed</option></select><button onclick="exportSessionTable()" class="btn-secondary">Export CSV</button></div>
                <div id="session-records-table" class="data-table"></div>
            </div>
            <div class="card"><h2>Session Cards View</h2><div id="sessions-list" class="sessions-grid"></div></div>
        </div>

        <!-- Live View Tab -->
        <div id="live" class="tab-content">
            <div class="card">
                <h2>Live Attendance Feed</h2>
                <div class="live-stats">
                    <div class="stat-card"><h3>Present</h3><p id="present-count">0</p></div>
                    <div class="stat-card"><h3>Late</h3><p id="late-count">0</p></div>
                    <div class="stat-card"><h3>Absent</h3><p id="absent-count">0</p></div>
                    <div class="stat-card"><h3>Total Expected</h3><p id="total-expected">0</p></div>
                </div>
                <div class="active-session-info"><strong>Active Session:</strong> <span id="active-session-name">None</span><button onclick="refreshLiveAttendance()" class="btn-small btn-secondary">Refresh</button></div>
                <div id="live-attendance-table" class="data-table"></div>
            </div>
        </div>

        <!-- Students Tab -->
        <div id="students" class="tab-content">
            <div class="card">
                <h2>Add New RTB Student</h2>
                <form id="student-form">
                    <div class="form-row">
                        <div class="form-group"><label>Card ID (RFID):</label><input type="text" id="student-card-id" required placeholder="Scan Card ID"></div>
                        <div class="form-group"><label>Full Name:</label><input type="text" id="student-name" required placeholder="Full name"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Select Trade (RTB):</label>
                            <select id="student-trade" required>
                                <option value="">-- Select Trade --</option>
                                <option value="Software Development">Software Development</option>
                                <option value="Networking & Cybersecurity">Networking & Cybersecurity</option>
                                <option value="Data Science">Data Science</option>
                                <option value="Cloud Computing">Cloud Computing</option>
                                <option value="Multimedia & Design">Multimedia & Design</option>
                                <option value="IT Support">IT Support</option>
                                <option value="Embedded Systems">Embedded Systems</option>
                                <option value="Digital Marketing">Digital Marketing</option>
                                <option value="Mobile App Development">Mobile App Development</option>
                                <option value="DevOps Engineering">DevOps Engineering</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Level:</label>
                            <select id="student-level" required>
                                <option value="">-- Select Level --</option>
                                <option value="L3">L3 (Year 1)</option>
                                <option value="L4">L4 (Year 2)</option>
                                <option value="L5">L5 (Year 3)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Gender:</label><select id="student-gender"><option>Male</option><option>Female</option><option>Other</option></select></div>
                        <div class="form-group"><label>Fingerprint ID:</label><input type="number" id="student-fingerprint" placeholder="1-255"></div>
                    </div>
                    <div class="form-group"><label>Email:</label><input type="email" id="student-email" placeholder="student@rtb.rw"></div>
                    <button type="submit" class="btn-primary">Add Student</button>
                </form>
            </div>
            <div class="card">
                <h2>RTB Student List</h2>
                <div class="filter-bar">
                    <input type="text" id="search-student" placeholder="Search by name, trade, or level..." onkeyup="searchStudents()">
                    <select id="filter-trade" onchange="searchStudents()"><option value="">All Trades</option></select>
                    <select id="filter-level" onchange="searchStudents()"><option value="">All Levels</option></select>
                    <button onclick="exportStudentsCSV()" class="btn-secondary">Export CSV</button>
                </div>
                <div id="students-table" class="data-table"></div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="reports" class="tab-content">
            <div class="card">
                <h2>Attendance Reports</h2>
                <div class="filter-bar">
                    <select id="report-session-filter"><option value="">All Sessions</option></select>
                    <select id="report-user-filter"><option value="">All Students</option></select>
                    <input type="date" id="report-date-filter">
                    <button onclick="generateReport()" class="btn-primary">Generate</button>
                    <button onclick="exportReport()" class="btn-secondary">Export CSV</button>
                </div>
                <canvas id="attendance-chart" width="400" height="200" style="max-height: 300px;"></canvas>
                <div id="report-results" class="data-table"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // ==================== API CONFIGURATION ====================
    const API_BASE_URL = 'http://localhost:5000/api';
    
    let currentSession = null;
    let studentsData = [];
    let sessionRecords = [];
    let attendanceData = [];

    // ==================== API HELPER FUNCTIONS ====================
    async function apiRequest(endpoint, method = 'GET', data = null) {
        const options = {
            method,
            headers: { 'Content-Type': 'application/json' }
        };
        if (data) options.body = JSON.stringify(data);
        
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // ==================== DATABASE SYNC FUNCTIONS ====================
    async function syncAllData() {
        showNotification('Syncing with database...', 'info');
        document.getElementById('api-status-sidebar').textContent = 'Syncing...';
        document.getElementById('api-status-sidebar').className = 'status-badge syncing';
        
        try {
            await Promise.all([
                fetchStudentsFromDB(),
                fetchSessionsFromDB(),
                fetchAttendanceFromDB(),
                fetchDashboardStats()
            ]);
            document.getElementById('api-status-sidebar').textContent = 'Online';
            document.getElementById('api-status-sidebar').className = 'status-badge online';
            showNotification('All data synced with database!', 'success');
        } catch (error) {
            document.getElementById('api-status-sidebar').textContent = 'Offline';
            document.getElementById('api-status-sidebar').className = 'status-badge offline';
            showNotification('Failed to connect to database server!', 'error');
        }
    }

    async function fetchStudentsFromDB() {
        try {
            studentsData = await apiRequest('/students');
            localStorage.setItem('enrolled_users', JSON.stringify(studentsData));
            loadStudents();
            return studentsData;
        } catch (error) {
            console.error('Fetch students failed:', error);
            const local = localStorage.getItem('enrolled_users');
            if (local) studentsData = JSON.parse(local);
            loadStudents();
            return studentsData;
        }
    }

    async function fetchSessionsFromDB() {
        try {
            sessionRecords = await apiRequest('/sessions');
            sessionRecords.forEach(s => {
                if (typeof s.participants === 'string') {
                    s.participants = JSON.parse(s.participants);
                }
            });
            localStorage.setItem('session_records', JSON.stringify(sessionRecords));
            renderSessionTable();
            renderSessionCards();
            return sessionRecords;
        } catch (error) {
            console.error('Fetch sessions failed:', error);
            const local = localStorage.getItem('session_records');
            if (local) sessionRecords = JSON.parse(local);
            renderSessionTable();
            renderSessionCards();
            return sessionRecords;
        }
    }

    async function fetchAttendanceFromDB() {
        try {
            attendanceData = await apiRequest('/attendance');
            localStorage.setItem('attendance_records', JSON.stringify(attendanceData));
            refreshLiveAttendance();
            updateDashboardStats();
            return attendanceData;
        } catch (error) {
            console.error('Fetch attendance failed:', error);
            const local = localStorage.getItem('attendance_records');
            if (local) attendanceData = JSON.parse(local);
            refreshLiveAttendance();
            updateDashboardStats();
            return attendanceData;
        }
    }

    async function fetchDashboardStats() {
        try {
            const stats = await apiRequest('/data');
            document.getElementById('total-students').textContent = stats.totalStudents || 0;
            document.getElementById('total-sessions').textContent = stats.totalSessions || 0;
            document.getElementById('total-records').textContent = stats.totalAttendance || 0;
            document.getElementById('today-attendance').textContent = stats.todayAttendance || 0;
            
            const present = stats.statusStats?.find(s => s.status === 'present')?.count || 0;
            const late = stats.statusStats?.find(s => s.status === 'late')?.count || 0;
            const absent = (stats.totalAttendance || 0) - present - late;
            const ctx = document.getElementById('dashboard-chart').getContext('2d');
            if (window.dashChart) window.dashChart.destroy();
            window.dashChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: ['Present', 'Late', 'Absent'], datasets: [{ data: [present, late, absent], backgroundColor: ['#4CAF50', '#ff9800', '#f44336'] }] }
            });
        } catch (error) {
            console.error('Fetch stats failed:', error);
        }
    }

    // ==================== CRUD OPERATIONS WITH DATABASE ====================
    async function addStudentToDB(student) {
        try {
            await apiRequest('/students', 'POST', student);
            await fetchStudentsFromDB();
            showNotification(`Student ${student.name} added to database!`, 'success');
            return true;
        } catch (error) {
            showNotification('Failed to add student to database!', 'error');
            return false;
        }
    }

    async function updateStudentInDB(id, student) {
        try {
            await apiRequest(`/students/${id}`, 'PUT', student);
            await fetchStudentsFromDB();
            showNotification('Student updated in database!', 'success');
            return true;
        } catch (error) {
            showNotification('Failed to update student!', 'error');
            return false;
        }
    }

    async function deleteStudentFromDB(id) {
        try {
            await apiRequest(`/students/${id}`, 'DELETE');
            await fetchStudentsFromDB();
            await fetchAttendanceFromDB();
            showNotification('Student deleted from database!', 'success');
            return true;
        } catch (error) {
            showNotification('Failed to delete student!', 'error');
            return false;
        }
    }

    async function createSessionInDB(session) {
        try {
            await apiRequest('/sessions', 'POST', session);
            await fetchSessionsFromDB();
            showNotification(`Session "${session.title}" created in database!`, 'success');
            return true;
        } catch (error) {
            showNotification('Failed to create session!', 'error');
            return false;
        }
    }

    async function updateSessionInDB(id, session) {
        try {
            await apiRequest(`/sessions/${id}`, 'PUT', session);
            await fetchSessionsFromDB();
            showNotification('Session updated in database!', 'success');
            return true;
        } catch (error) {
            showNotification('Failed to update session!', 'error');
            return false;
        }
    }

    async function deleteSessionFromDB(id) {
        try {
            await apiRequest(`/sessions/${id}`, 'DELETE');
            await fetchSessionsFromDB();
            showNotification('Session deleted from database!', 'success');
            return true;
        } catch (error) {
            showNotification('Failed to delete session!', 'error');
            return false;
        }
    }

    async function addAttendanceToDB(record) {
        try {
            await apiRequest('/attendance', 'POST', record);
            await fetchAttendanceFromDB();
            showNotification('Attendance recorded in database!', 'success');
            return true;
        } catch (error) {
            showNotification('Failed to record attendance!', 'error');
            return false;
        }
    }

    // ==================== UI HANDLERS ====================
    function loadStudents() {
        const students = studentsData.length ? studentsData : JSON.parse(localStorage.getItem('enrolled_users') || '[]');
        const participantsSelect = document.getElementById('participants-select');
        const reportUserFilter = document.getElementById('report-user-filter');
        const studentsTable = document.getElementById('students-table');
        const filterTrade = document.getElementById('filter-trade');
        const filterLevel = document.getElementById('filter-level');
        
        document.getElementById('total-students').textContent = students.length;
        
        if (filterTrade) {
            const uniqueTrades = [...new Set(students.map(s => s.trade).filter(t => t))];
            filterTrade.innerHTML = '<option value="">All Trades</option>' + uniqueTrades.map(t => `<option value="${t}">${t}</option>`).join('');
        }
        if (filterLevel) {
            filterLevel.innerHTML = '<option value="">All Levels</option><option value="L3">L3</option><option value="L4">L4</option><option value="L5">L5</option>';
        }
        
        if (students.length === 0) {
            studentsTable.innerHTML = '<p>No students enrolled.</p>';
            if (participantsSelect) participantsSelect.innerHTML = '<option value="">No students</option>';
            if (reportUserFilter) reportUserFilter.innerHTML = '<option value="">All Students</option>';
            return;
        }
        
        let html = '<table class="data-table"><thead><tr><th>Card ID</th><th>Full Name</th><th>Trade / Option</th><th>Level</th><th>Gender</th><th>Fingerprint</th><th>Action</th></tr></thead><tbody>';
        students.forEach(s => {
            html += `<tr>
                        <td><strong>${escapeHtml(s.rfidCard)}</strong></td>
                        <td>${escapeHtml(s.name)}</td>
                        <td><span class="trade-badge">${escapeHtml(s.trade || s.option)}</span></td>
                        <td><span class="level-badge">${escapeHtml(s.level || s.class)}</span></td>
                        <td>${escapeHtml(s.gender || '-')}</td>
                        <td>${s.fingerprintId || '-'}</td>
                        <td><button onclick="deleteStudent('${s.id}')" class="btn-small btn-danger">Delete</button></td>
                    </tr>`;
        });
        html += '</tbody></table>';
        studentsTable.innerHTML = html;
        
        if (participantsSelect) participantsSelect.innerHTML = students.map(s => `<option value="${s.rfidCard}">${escapeHtml(s.name)} (${s.trade} - ${s.level})</option>`).join('');
        if (reportUserFilter) reportUserFilter.innerHTML = '<option value="">All Students</option>' + students.map(s => `<option value="${s.id}">${escapeHtml(s.name)}</option>`).join('');
    }

    function renderSessionTable() {
        const container = document.getElementById('session-records-table');
        if (!sessionRecords.length) { 
            if (container) container.innerHTML = '<p style="text-align:center;padding:20px;">No session records.</p>'; 
            return; 
        }
        let html = '<table class="data-table"><thead><tr><th>#</th><th>Title</th><th>Date</th><th>Start</th><th>End</th><th>Participants</th><th>Present</th><th>Late</th><th>Absent</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        sessionRecords.forEach((rec, idx) => {
            const presentCount = attendanceData.filter(a => a.sessionId === rec.id && a.status === 'present').length;
            const lateCount = attendanceData.filter(a => a.sessionId === rec.id && a.status === 'late').length;
            const partCount = rec.participants?.length || 0;
            html += `<tr>
                        <td>${idx + 1}</td>
                        <td><strong>${escapeHtml(rec.title)}</strong></td>
                        <td>${rec.date}</td>
                        <td>${rec.startTime}</td>
                        <td>${rec.endTime}</td>
                        <td>${partCount}</td>
                        <td class="status-present">${presentCount}</td>
                        <td class="status-late">${lateCount}</td>
                        <td class="status-absent">${partCount - presentCount - lateCount}</td>
                        <td class="${rec.status === 'active' ? 'status-active' : rec.status === 'completed' ? 'status-completed' : 'status-scheduled'}">${rec.status}</td>
                        <td>
                            <button onclick="activateSession('${rec.id}')" class="btn-small btn-success">Activate</button>
                            <button onclick="deleteSessionRecord('${rec.id}')" class="btn-small btn-danger">Delete</button>
                        </td>
                    </tr>`;
        });
        html += '</tbody></table>';
        container.innerHTML = html;
        document.getElementById('total-sessions').textContent = sessionRecords.length;
    }

    function renderSessionCards() {
        const container = document.getElementById('sessions-list');
        if (!sessionRecords.length) { if (container) container.innerHTML = '<p>No sessions</p>'; return; }
        container.innerHTML = sessionRecords.map(s => `
            <div class="session-card">
                <h3>${escapeHtml(s.title)} <span class="level-badge">${s.status}</span></h3>
                <p>Date: ${s.date} | Time: ${s.startTime} - ${s.endTime}</p>
                <p>Participants: ${s.participants?.length || 0}</p>
                <div class="session-actions">
                    <button onclick="activateSession('${s.id}')" class="btn-small btn-success">Activate</button>
                    <button onclick="deleteSessionRecord('${s.id}')" class="btn-small btn-danger">Delete</button>
                </div>
            </div>
        `).join('');
    }

    function refreshLiveAttendance() {
        if (!currentSession) {
            document.getElementById('live-attendance-table').innerHTML = '<p>No active session. Activate a session from Sessions tab.</p>';
            document.getElementById('present-count').textContent = '0';
            document.getElementById('late-count').textContent = '0';
            document.getElementById('absent-count').textContent = '0';
            document.getElementById('total-expected').textContent = '0';
            return;
        }
        
        const participants = currentSession.participants || [];
        const total = participants.length;
        const students = studentsData.length ? studentsData : JSON.parse(localStorage.getItem('enrolled_users') || '[]');
        const records = attendanceData.filter(a => a.sessionId === currentSession.id);
        const present = records.filter(r => r.status === 'present').length;
        const late = records.filter(r => r.status === 'late').length;
        
        document.getElementById('present-count').textContent = present;
        document.getElementById('late-count').textContent = late;
        document.getElementById('absent-count').textContent = total - (present + late);
        document.getElementById('total-expected').textContent = total;
        
        if (total === 0) { 
            document.getElementById('live-attendance-table').innerHTML = '<p>No participants in this session.</p>'; 
            return; 
        }
        
        let html = '<table class="data-table"><thead><tr><th>Name</th><th>Trade</th><th>Level</th><th>Status</th><th>Time</th></tr></thead><tbody>';
        participants.forEach(pid => {
            const student = students.find(s => s.rfidCard === pid);
            const record = records.find(r => r.studentId === student?.id);
            const status = record ? (record.status === 'late' ? 'Late' : 'Present') : 'Absent';
            const cls = record ? (record.status === 'late' ? 'status-late' : 'status-present') : 'status-absent';
            html += `<tr>
                        <td>${student ? student.name : pid}</td>
                        <td>${student ? student.trade : '-'}</td>
                        <td>${student ? student.level : '-'}</td>
                        <td class="${cls}">${status}</td>
                        <td>${record ? new Date(record.timestamp).toLocaleTimeString() : '-'}</td>
                    </tr>`;
        });
        html += '</tbody></tr>';
        document.getElementById('live-attendance-table').innerHTML = html;
    }

    function generateReport() {
        let filtered = [...attendanceData];
        const sessionId = document.getElementById('report-session-filter')?.value;
        const userId = document.getElementById('report-user-filter')?.value;
        const date = document.getElementById('report-date-filter')?.value;
        
        if (sessionId) filtered = filtered.filter(r => r.sessionId === sessionId);
        if (userId) filtered = filtered.filter(r => r.studentId === userId);
        if (date) filtered = filtered.filter(r => r.timestamp && r.timestamp.startsWith(date));
        
        const students = studentsData.length ? studentsData : JSON.parse(localStorage.getItem('enrolled_users') || '[]');
        const sessions = sessionRecords;
        
        if (!filtered.length) { 
            document.getElementById('report-results').innerHTML = '<p>No records found</p>'; 
            return; 
        }
        
        let html = '<table class="data-table"><thead><tr><th>Student</th><th>Trade</th><th>Level</th><th>Session</th><th>Status</th><th>Time</th></tr></thead><tbody>';
        filtered.forEach(r => {
            const student = students.find(s => s.id === r.studentId);
            const session = sessions.find(s => s.id === r.sessionId);
            html += `<tr>
                        <td>${student ? student.name : 'Unknown'}</td>
                        <td><span class="trade-badge">${student ? student.trade : '-'}</span></td>
                        <td><span class="level-badge">${student ? student.level : '-'}</span></td>
                        <td>${session ? session.title : 'Unknown'}</td>
                        <td class="${r.status === 'present' ? 'status-present' : r.status === 'late' ? 'status-late' : 'status-absent'}">${r.status}</td>
                        <td>${new Date(r.timestamp).toLocaleString()}</td>
                    </tr>`;
        });
        html += '</tbody></tr>';
        document.getElementById('report-results').innerHTML = html;
        
        const p = filtered.filter(r => r.status === 'present').length;
        const l = filtered.filter(r => r.status === 'late').length;
        const ctx = document.getElementById('attendance-chart').getContext('2d');
        if (window.reportChart) window.reportChart.destroy();
        window.reportChart = new Chart(ctx, {
            type: 'pie',
            data: { labels: ['Present', 'Late', 'Absent'], datasets: [{ data: [p, l, filtered.length - p - l], backgroundColor: ['#4CAF50', '#ff9800', '#f44336'] }] }
        });
    }

    function updateDashboardStats() {
        fetchDashboardStats();
    }

    // ==================== FORM HANDLERS ====================
    document.getElementById('student-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const student = {
            id: 'student_' + Date.now(),
            name: document.getElementById('student-name').value,
            rfidCard: document.getElementById('student-card-id').value,
            trade: document.getElementById('student-trade').value,
            level: document.getElementById('student-level').value,
            gender: document.getElementById('student-gender').value,
            fingerprintId: parseInt(document.getElementById('student-fingerprint').value) || 0,
            email: document.getElementById('student-email').value,
            createdAt: new Date().toISOString()
        };
        
        if (!student.name || !student.rfidCard || !student.trade || !student.level) {
            showNotification('Please fill all required fields!', 'error');
            return;
        }
        
        await addStudentToDB(student);
        document.getElementById('student-form').reset();
        loadStudents();
    });

    document.getElementById('session-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const session = {
            id: 'sess_' + Date.now(),
            title: document.getElementById('session-title').value,
            date: document.getElementById('session-date').value,
            startTime: document.getElementById('session-start').value,
            endTime: document.getElementById('session-end').value,
            participants: Array.from(document.getElementById('participants-select').selectedOptions).map(opt => opt.value),
            status: 'scheduled',
            createdAt: new Date().toISOString()
        };
        
        if (!session.title) {
            showNotification('Please enter session title!', 'error');
            return;
        }
        
        await createSessionInDB(session);
        document.getElementById('session-title').value = '';
        document.getElementById('participants-select').selectedIndex = -1;
        renderSessionTable();
        renderSessionCards();
    });

    // ==================== GLOBAL FUNCTIONS ====================
    window.deleteStudent = async (id) => {
        if (confirm('Delete this student? Related attendance records will also be deleted.')) {
            await deleteStudentFromDB(id);
            loadStudents();
        }
    };

    window.deleteSessionRecord = async (id) => {
        if (confirm('Delete this session?')) {
            await deleteSessionFromDB(id);
            if (currentSession && currentSession.id === id) currentSession = null;
            renderSessionTable();
            renderSessionCards();
        }
    };

    window.activateSession = async (id) => {
        const sess = sessionRecords.find(s => s.id === id);
        if (sess) {
            sessionRecords.forEach(s => { if (s.status === 'active') s.status = 'completed'; });
            sess.status = 'active';
            await updateSessionInDB(id, sess);
            currentSession = sess;
            document.getElementById('active-session-name').textContent = sess.title;
            showNotification(`Session "${sess.title}" activated!`, 'success');
            refreshLiveAttendance();
            renderSessionTable();
            renderSessionCards();
        }
    };

    // Helper functions
    function filterSessionTable() { renderSessionTable(); }
    function searchStudents() { loadStudents(); }
    function exportSessionTable() {
        let csv = 'Title,Date,Start,End,Status\n';
        sessionRecords.forEach(s => csv += `"${s.title}",${s.date},${s.startTime},${s.endTime},${s.status}\n`);
        downloadCSV(csv, 'sessions.csv');
    }
    function exportStudentsCSV() {
        let csv = 'Name,Card ID,Trade,Level,Gender,Email\n';
        studentsData.forEach(s => csv += `"${s.name}","${s.rfidCard}","${s.trade}","${s.level}","${s.gender}","${s.email}"\n`);
        downloadCSV(csv, 'students.csv');
    }
    function exportReport() {
        let csv = 'Student,Session,Status,Timestamp\n';
        attendanceData.forEach(r => {
            const student = studentsData.find(s => s.id === r.studentId);
            const session = sessionRecords.find(s => s.id === r.sessionId);
            csv += `"${student?.name || 'Unknown'}","${session?.title || 'Unknown'}","${r.status}","${r.timestamp}"\n`;
        });
        downloadCSV(csv, 'report.csv');
    }
    function downloadCSV(csv, filename) {
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        a.click();
        URL.revokeObjectURL(a.href);
        showNotification('Exported!', 'success');
    }
    
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        const sessionDate = document.getElementById('session-date');
        if (sessionDate) sessionDate.value = today;
        const sessionStart = document.getElementById('session-start');
        if (sessionStart) sessionStart.value = '09:00';
        const sessionEnd = document.getElementById('session-end');
        if (sessionEnd) sessionEnd.value = '12:00';
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
    }
    
    function showNotification(msg, type) {
        const n = document.createElement('div');
        n.textContent = msg;
        n.style.cssText = `position:fixed; bottom:20px; right:20px; background:${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'}; color:white; padding:12px 20px; border-radius:8px; z-index:1000;`;
        document.body.appendChild(n);
        setTimeout(() => n.remove(), 3000);
    }
    
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        const tab = document.getElementById(tabName);
        if (tab) tab.classList.add('active');
        if (tabName === 'reports') generateReport();
        if (tabName === 'live') refreshLiveAttendance();
        if (tabName === 'students') loadStudents();
        if (tabName === 'sessions') { renderSessionCards(); renderSessionTable(); }
        if (tabName === 'dashboard') updateDashboardStats();
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        setDefaultDates();
        syncAllData();
        
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                showTab(tabName);
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                const titles = { dashboard: 'Dashboard', sessions: 'Sessions', live: 'Live View', students: 'Students', reports: 'Reports' };
                document.getElementById('page-title').textContent = titles[tabName] || 'Dashboard';
            });
        });
        
        document.querySelector('.nav-item[data-tab="dashboard"]').classList.add('active');
        showTab('dashboard');
        setInterval(refreshLiveAttendance, 5000);
    });
</script>
</body>
</html>