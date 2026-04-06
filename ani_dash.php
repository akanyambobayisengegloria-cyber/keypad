<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Livestock Management | Auto Tag ID System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

:root {
    --primary: #1a2a3a;
    --primary-dark: #0f1a24;
    --secondary: #2d5a4a;
    --secondary-light: #3a6b5a;
    --accent: #4a7c6b;
    --success: #2d6a4f;
    --warning: #b77e3a;
    --danger: #9b5e5e;
    --info: #3a6b8a;
    --dark: #1a2a2a;
    --light: #e8f0ec;
    --border: #c5d5cd;
    --card-bg: #f5faf7;
    --sidebar-bg: #1a2a2a;
    --sidebar-text: #c5d5cd;
}

body { background: #dce8e2; }

.landing-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #1a2a3a 0%, #0f1a24 100%);
    display: flex;
    flex-direction: column;
}

.landing-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    background: rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
}

.landing-nav .logo { font-size: 24px; font-weight: 700; color: #c5d5cd; }
.landing-nav .logo span { color: #5a8a7a; margin-right: 10px; font-weight: 800; }
.landing-nav button { background: #2d5a4a; color: #e8f0ec; border: none; padding: 12px 30px; border-radius: 40px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 16px; }
.landing-nav button:hover { transform: scale(1.05); background: #3a6b5a; }

.landing-hero { flex: 1; display: flex; align-items: center; justify-content: center; text-align: center; color: #c5d5cd; padding: 50px; flex-direction: column; }
.landing-hero h1 { font-size: 56px; margin-bottom: 20px; }
.landing-hero h1 span { color: #5a8a7a; }
.landing-hero p { font-size: 18px; color: #8aa89a; margin-bottom: 40px; }

.features { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-top: 50px; }
.feature-card { background: rgba(255,255,255,0.08); padding: 25px; border-radius: 20px; width: 250px; text-align: center; backdrop-filter: blur(10px); }
.feature-card h3 { margin-bottom: 10px; color: #7aab9a; }

.login-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1000; justify-content: center; align-items: center; }
.login-modal-content { background: #f5faf7; padding: 40px; border-radius: 32px; width: 420px; animation: fadeInUp 0.4s ease; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.login-modal-content h2 { margin-bottom: 20px; text-align: center; color: #1a2a3a; }
.login-modal-content input, .login-modal-content select { width: 100%; padding: 14px; margin: 10px 0; border: 2px solid #c5d5cd; border-radius: 16px; font-size: 14px; background: white; }
.login-modal-content input:focus, .login-modal-content select:focus { outline: none; border-color: #2d5a4a; }
.login-modal-content button { width: 100%; padding: 14px; background: #2d5a4a; color: white; border: none; border-radius: 40px; font-weight: 700; cursor: pointer; margin-top: 20px; font-size: 16px; }
.login-modal-content button:hover { background: #3a6b5a; }
.close-modal { float: right; font-size: 28px; cursor: pointer; color: #64748b; }
.close-modal:hover { color: #9b5e5e; }
.login-error { background: #f0e6e6; color: #9b5e5e; padding: 12px; border-radius: 12px; margin-top: 15px; text-align: center; font-size: 13px; display: none; border-left: 4px solid #9b5e5e; }
.admin-contact-info { background: #e8f0ec; padding: 12px; border-radius: 12px; margin-top: 15px; font-size: 12px; text-align: center; color: #1a2a3a; border: 1px solid #c5d5cd; }

.app-container { display: none; }
.dashboard-container { display: flex; min-height: 100vh; background: #dce8e2; }

.sidebar { width: 280px; background: #1a2a2a; border-right: 1px solid #2a3a3a; padding: 30px 20px; position: fixed; height: 100vh; overflow-y: auto; }
.sidebar .logo { font-size: 24px; font-weight: 800; margin-bottom: 40px; color: #c5d5cd; }
.sidebar .logo span { color: #5a8a7a; }
.sidebar a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin: 8px 0; border-radius: 12px; color: #8aa89a; text-decoration: none; cursor: pointer; transition: 0.2s; }
.sidebar a:hover, .sidebar a.active { background: #2a3a3a; color: #e8f0ec; }
.user-profile { margin-top: 40px; padding: 20px 0; border-top: 1px solid #2a3a3a; }
.user-profile .avatar { width: 50px; height: 50px; background: #2d5a4a; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-bottom: 12px; font-size: 20px; }
.logout-btn { background: #4a5a5a; color: white; border: none; padding: 10px; border-radius: 12px; width: 100%; cursor: pointer; margin-top: 15px; font-weight: 600; }
.logout-btn:hover { background: #5a6a6a; }

.main-content { margin-left: 280px; flex: 1; padding: 30px 40px; }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 25px; border-radius: 20px; border: 1px solid #c5d5cd; text-align: center; transition: 0.2s; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); background: #f5faf7; }
.stat-card h2 { font-size: 36px; color: #1a2a3a; font-weight: 800; }
.stat-card p { color: #4a6a5a; font-size: 14px; font-weight: 600; margin-top: 8px; }

.content-card { background: white; border-radius: 20px; border: 1px solid #c5d5cd; margin-bottom: 25px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.card-header { padding: 15px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #c5d5cd; background: #f5faf7; }
.card-header .avatar-small { width: 40px; height: 40px; background: #2d5a4a; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; }
.card-body { padding: 20px; }

table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; }
th, td { padding: 12px 16px; text-align: left; font-size: 13px; border-bottom: 1px solid #e0e8e2; }
th { background: #e8f0ec; color: #1a2a3a; font-weight: 600; }

.badge { padding: 4px 12px; border-radius: 30px; color: white; font-size: 11px; font-weight: 600; display: inline-block; }
.badge.admin { background: #2d5a4a; }
.badge.vet { background: #3a6b8a; }
.badge.owner { background: #4a7c6b; }
.badge.pregnant { background: #5a7a6a; }
.badge.healthy { background: #2d6a4f; }
.badge.sick { background: #b77e3a; }
.badge.vaccination { background: #3a6b8a; }

button.edit { background: #3a6b8a; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; margin: 2px; font-weight: 600; font-size: 12px; transition: 0.2s; }
button.edit:hover { background: #2a5a7a; }
button.delete { background: #5a6a5a; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; margin: 2px; font-weight: 600; font-size: 12px; transition: 0.2s; }
button.delete:hover { background: #4a5a4a; }
button.submit { background: #2d5a4a; color: white; padding: 12px; border-radius: 40px; width: 100%; cursor: pointer; border: none; font-weight: 600; transition: 0.2s; }
button.submit:hover { background: #3a6b5a; }

form { background: white; padding: 25px; border-radius: 20px; border: 1px solid #c5d5cd; margin-bottom: 20px; }
input, select, textarea { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #c5d5cd; border-radius: 12px; transition: 0.2s; background: white; }
input:focus, select:focus, textarea:focus { outline: none; border-color: #2d5a4a; box-shadow: 0 0 0 3px rgba(45,90,74,0.1); }
label { font-weight: 600; font-size: 13px; color: #1a2a3a; display: block; margin-top: 8px; }
.hidden { display: none; }

.error-message { background: #f0e6e6; color: #9b5e5e; padding: 12px; border-radius: 12px; margin: 10px 0; font-size: 13px; border-left: 4px solid #9b5e5e; display: flex; align-items: center; gap: 10px; }

.scan-logs-container { background: white; border-radius: 20px; border: 1px solid #c5d5cd; margin-top: 20px; max-height: 400px; overflow-y: auto; }
.scan-log-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 1px solid #e0e8e2; }
.scan-log-item .log-action { font-weight: 600; color: #1a2a3a; }
.scan-log-item .log-time { color: #5a7a6a; font-size: 11px; }

.toast { position: fixed; bottom: 30px; right: 30px; background: #2d5a4a; color: white; padding: 12px 20px; border-radius: 12px; z-index: 2000; animation: slideIn 0.3s ease; font-weight: 500; }
.toast.error { background: #9b5e5e; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.empty-row td { text-align: center; padding: 40px; color: #5a7a6a; }
.auto-tag-info { background: #e8f0ec; padding: 10px 15px; border-radius: 12px; margin-bottom: 15px; font-size: 13px; color: #2d5a4a; border-left: 3px solid #2d5a4a; }

/* NEW STYLES FOR LOG MANAGEMENT */
.log-management-buttons { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.log-action-btn { background: #2d5a4a; color: white; border: none; padding: 8px 16px; border-radius: 40px; cursor: pointer; font-size: 13px; font-weight: 600; transition: 0.2s; }
.log-action-btn:hover { background: #3a6b5a; transform: scale(1.02); }
.log-action-btn.danger { background: #9b5e5e; }
.log-action-btn.danger:hover { background: #8a4e4e; }
.log-action-btn.warning { background: #b77e3a; }
.log-action-btn.warning:hover { background: #a06e30; }
.recover-area { background: #e8f0ec; padding: 15px; border-radius: 16px; margin-bottom: 20px; border-left: 4px solid #2d5a4a; }
</style>
</head>
<body>

<div class="landing-page" id="landingPage">
    <div class="landing-nav">
        <div class="logo"><span>SL</span> Smart Livestock</div>
        <button id="openLoginBtn">Sign In</button>
    </div>
    <div class="landing-hero">
        <div id="landingProjectImageContainer" style="margin-bottom: 20px;">
            <div id="landingImagePreview" style="width: 340px; height: 210px; background: rgba(255,255,255,0.08); border-radius: 28px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #5a8a7a; margin: 0 auto;">
                <span style="color:#8aa89a;">Project Preview Image</span>
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label for="landingImageUpload" style="background: #2d5a4a; padding: 8px 20px; border-radius: 40px; cursor: pointer; font-weight: 600; font-size: 13px; color: white;">Upload Image</label>
            <input type="file" id="landingImageUpload" accept="image/*" style="display: none;">
        </div>
        <h1><span>Smart</span> Livestock Management</h1>
        <p>Efficient animal tracking, health records, and farm analytics</p>
        <div class="features">
            <div class="feature-card"><h3>Animal Tracking</h3><p>Monitor all livestock data</p></div>
            <div class="feature-card"><h3>Health Records</h3><p>Vaccination & disease logs</p></div>
            <div class="feature-card"><h3>Analytics</h3><p>Farm performance insights</p></div>
        </div>
    </div>
</div>

<div class="login-modal" id="loginModal">
    <div class="login-modal-content">
        <span class="close-modal" id="closeModalBtn">&times;</span>
        <h2>Sign In</h2>
        <select id="loginRole">
            <option value="admin">Administrator</option>
            <option value="vet">Veterinarian</option>
            <option value="owner">Owner</option>
        </select>
        <input type="password" id="loginPassword" placeholder="Password">
        <button id="loginBtn">Login</button>
        <div id="loginError" class="login-error"></div>
        <div id="accountHelpMessage" class="admin-contact-info" style="display:none;">
            <strong>No account?</strong><br>Contact Administrator to create your account.<br>Admin Email: admin@livestock.com
        </div>
        <p style="text-align:center; margin-top:15px; font-size:12px; color:#5a7a6a;">Demo: admin123 | vet123 | owner123</p>
    </div>
</div>

<div class="app-container" id="appContainer">
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo"><span>SF</span> SmartFarm</div>
            <a onclick="navigateTo('dashboard')" class="nav-link active" data-page="dashboard">Dashboard</a>
            <a onclick="navigateTo('animals')" class="nav-link" data-page="animals" id="navAnimals">Animals</a>
            <a onclick="navigateTo('registerAnimal')" class="nav-link" data-page="registerAnimal" id="navRegisterAnimal">Add Animal</a>
            <a onclick="navigateTo('healthRecords')" class="nav-link" data-page="healthRecords" id="navHealthRecords">Health Records</a>
            <a onclick="navigateTo('registerHealth')" class="nav-link" data-page="registerHealth" id="navRegisterHealth">Add Health Record</a>
            <a onclick="navigateTo('users')" class="nav-link" data-page="users" id="navUsers">Manage Users</a>
            <a onclick="navigateTo('scanLogs')" class="nav-link" data-page="scanLogs" id="navScanLogs">Activity Logs</a>
            <div class="user-profile">
                <div class="avatar" id="userAvatar">JD</div>
                <div><strong id="userName">John Doe</strong></div>
                <div style="font-size:12px; margin-top:5px;" id="userRoleBadge"></div>
                <button class="logout-btn" id="logoutBtn">Sign Out</button>
            </div>
        </div>

        <div class="main-content">
            <div id="dashboardSection">
                <div class="stats-grid">
                    <div class="stat-card" onclick="navigateTo('animals')"><h2 id="totalAnimals">0</h2><p>Total Animals</p></div>
                    <div class="stat-card" onclick="navigateTo('healthRecords')"><h2 id="totalHealth">0</h2><p>Health Records</p></div>
                    <div class="stat-card" onclick="navigateTo('animals')"><h2 id="sickCount">0</h2><p>Sick Animals</p></div>
                    <div class="stat-card" onclick="navigateTo('users')"><h2 id="totalUsers">0</h2><p>System Users</p></div>
                </div>
                <div class="content-card">
                    <div class="card-header"><div class="avatar-small">R</div><div><strong>Recent Activity</strong><br><span style="font-size:12px;">Latest farm updates</span></div></div>
                    <div class="card-body"><div id="recentActivityList"></div></div>
                </div>
            </div>

            <div id="animalsSection" class="hidden">
                <div class="content-card">
                    <div class="card-header"><div class="avatar-small">A</div><div><strong>All Livestock</strong></div></div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table>
                                <thead><tr><th>Tag ID</th><th>Name</th><th>Type</th><th>Breed</th><th>Sex</th><th>Pregnancy</th><th>Health</th><th>Actions</th></tr></thead>
                                <tbody id="animalsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="registerAnimalSection" class="hidden">
                <form id="registerAnimalForm">
                    <h3>Register New Animal</h3>
                    <div class="auto-tag-info"><strong>Auto Tag ID:</strong> Tag ID will be automatically generated based on animal type (e.g., COW-001, GOAT-001)</div>
                    <div id="animalFormError" class="error-message" style="display:none;"></div>
                    <label>Generated Tag ID</label>
                    <input type="text" id="generatedTagId" readonly style="background:#e8f0ec; font-weight:bold;">
                    <label>Animal Name</label><input type="text" id="animalName" placeholder="Animal Name" required>
                    <label>Animal Type</label><select id="animalType" required><option value="">Select Type</option></select>
                    <label>Breed</label><select id="animalBreed" required><option value="">Select Breed</option></select>
                    <label>Sex</label><select id="animalSex"><option>Male</option><option>Female</option></select>
                    <label>Birth Date</label><input type="date" id="animalBirthdate">
                    <label>Pregnancy</label><select id="animalPregnant"><option value="false">Not Pregnant</option><option value="true">Pregnant</option></select>
                    <label>Health Status</label><select id="animalSick"><option value="false">Healthy</option><option value="true">Sick</option></select>
                    <label>Owner Contact</label><input type="text" id="animalOwnerContact" placeholder="Owner phone or email">
                    <input type="hidden" id="editAnimalId">
                    <button type="submit" class="submit">Save Animal</button>
                </form>
            </div>

            <div id="healthRecordsSection" class="hidden">
                <div class="content-card">
                    <div class="card-header"><div class="avatar-small">H</div><div><strong>Health Records</strong></div></div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table>
                                <thead><tr><th>Animal Tag</th><th>Animal Name</th><th>Type</th><th>Start Date</th><th>End Date</th><th>Next Event</th><th>Vet Name</th><th>Actions</th></tr></thead>
                                <tbody id="healthTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="registerHealthSection" class="hidden">
                <form id="registerHealthForm">
                    <h3>Add Health Record</h3>
                    <div id="healthFormError" class="error-message" style="display:none;"></div>
                    <label>Select Animal</label><select id="healthTagId" required></select>
                    <label>Record Type</label><select id="healthType"><option value="Vaccination">Vaccination</option><option value="Pregnancy Check">Pregnancy Check</option><option value="Disease Treatment">Disease Treatment</option><option value="General Checkup">General Checkup</option><option value="Deworming">Deworming</option></select>
                    <label>Start Date</label><input type="date" id="healthStartDate" required>
                    <label>End Date</label><input type="date" id="healthEndDate">
                    <label>Next Scheduled Event</label><input type="date" id="healthNextEvent">
                    <label>Notes</label><textarea id="healthNotes" rows="2" placeholder="Additional details..."></textarea>
                    <label>Vet Name</label><input type="text" id="healthVetName" placeholder="Veterinarian name">
                    <label>Vet Contact</label><input type="text" id="healthVetContact" placeholder="Phone">
                    <input type="hidden" id="editHealthId">
                    <button type="submit" class="submit">Save Record</button>
                </form>
            </div>

            <div id="usersSection" class="hidden">
                <div class="content-card">
                    <div class="card-header"><div class="avatar-small">U</div><div><strong>User Management (Admin Only)</strong></div></div>
                    <div class="card-body">
                        <form id="registerUserForm"><h4>Create New Account</h4><input type="text" id="newUserName" placeholder="Full Name" required><input type="email" id="newUserEmail" placeholder="Email" required><input type="tel" id="newUserPhone" placeholder="Phone Number" required><select id="newUserRole"><option value="owner">Owner</option><option value="vet">Veterinarian</option><option value="admin">Admin</option></select><input type="password" id="newUserPassword" placeholder="Password"><button type="submit" class="submit">Create Account</button></form>
                        <table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Action</th></tr></thead><tbody id="usersTable"></tbody></table>
                    </div>
                </div>
            </div>

            <div id="scanLogsSection" class="hidden">
                <div class="content-card">
                    <div class="card-header"><div class="avatar-small">L</div><div><strong>System Activity Logs</strong></div></div>
                    <div class="card-body">
                        <!-- NEW: Log Management Controls -->
                        <div class="log-management-buttons">
                            <button class="log-action-btn" id="recoverLastDeletedBtn">↩️ Recover Last Deleted Item</button>
                            <button class="log-action-btn warning" id="deleteLogsOlderThanBtn">🗑️ Delete Logs Older Than (days)</button>
                            <button class="log-action-btn" id="deleteLogsByActionBtn">📋 Delete Logs by Action Type</button>
                            <button class="log-action-btn danger" id="clearAllLogsBtn">⚠️ Clear All Logs</button>
                        </div>
                        <div id="recoverStatus" class="recover-area" style="display:none;"></div>
                        <div id="scanLogsFullList" class="scan-logs-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ==================== ORIGINAL DATA AND FUNCTIONS (UNCHANGED) ====================
const breedDatabase = {
    "Cow": ["Holstein Friesian", "Jersey", "Angus", "Hereford", "Sahiwal", "Ankole", "Simmental", "Charolais", "Limousin", "Brahman", "Gelbvieh"],
    "Goat": ["Boer", "Saanen", "Nubian", "Alpine", "Toggenburg", "Angora", "Kiko", "Spanish Goat", "Pygmy Goat", "LaMancha", "Oberhasli"],
    "Sheep": ["Merino", "Dorper", "Suffolk", "Romney", "Balami", "Blackhead Persian", "Hampshire", "Southdown", "Cheviot", "Lincoln", "Karakul"],
    "Pig": ["Yorkshire", "Landrace", "Duroc", "Berkshire", "Large White", "Hampshire", "Pietrain", "Chester White", "Tamworth", "Gloucestershire Old Spots", "Meishan"],
    "Chicken": ["Broiler", "Layer", "Kuroiler", "Rhode Island Red", "Leghorn", "Sussex", "Orpington", "Australorp", "Plymouth Rock", "Wyandotte", "Brahma"],
    "Rabbit": ["New Zealand White", "California", "Flemish Giant", "Rex", "Dutch", "Lionhead", "Angora", "Himalayan", "Mini Lop", "English Lop", "Havana"],
    "Horse": ["Thoroughbred", "Arabian", "Quarter Horse", "Appaloosa", "Andalusian", "Friesian", "Mustang", "Clydesdale", "Shetland Pony", "Morgan", "Paint Horse"],
    "Donkey": ["Standard Donkey", "Mammoth Donkey", "Mediterranean Miniature", "Poitou Donkey", "Abyssinian Donkey", "American Spotted", "Andalusian Donkey", "Burro", "Somali Wild", "Kiang", "Onager"],
    "Turkey": ["Broad Breasted White", "Broad Breasted Bronze", "Bourbon Red", "Narragansett", "Royal Palm", "Standard Bronze", "Beltsville Small White", "Black Turkey", "Midget White", "Jersey Buff", "Slate Turkey"],
    "Duck": ["Pekin Duck", "Muscovy Duck", "Mallard", "Rouen Duck", "Cayuga Duck", "Indian Runner", "Khaki Campbell", "Welsh Harlequin", "Buff Duck", "Saxony Duck", "Silver Appleyard"],
    "Bee": ["Italian Honey Bee", "Carniolan Honey Bee", "German Honey Bee", "Buckfast Bee", "Russian Honey Bee", "Africanized Bee", "Caucasian Bee", "Cordovan Bee", "Minnesota Hygienic", "Saskatraz Bee", "VSH Bee"]
};

const animalTypes = ["Cow", "Goat", "Sheep", "Pig", "Chicken", "Rabbit", "Horse", "Donkey", "Turkey", "Duck", "Bee"];

let currentUser = null;
let animals = [];
let healthRecords = [];
let systemUsers = [];
let scanLogs = [];

// NEW: Store deleted items for recovery (last 50 deletions)
let deletedItemsStack = []; // each item: { type: 'animal' or 'health', data: object, timestamp, logId }

function generateUniqueTagId(animalType) {
    const upperType = animalType.toUpperCase();
    const existingTags = animals.filter(a => a.animal_type === animalType).map(a => a.tag_id);
    let maxNum = 0;
    for (let tag of existingTags) {
        const match = tag.match(new RegExp(`^${upperType}-(\\d+)$`));
        if (match) {
            maxNum = Math.max(maxNum, parseInt(match[1]));
        }
    }
    const newNum = maxNum + 1;
    return `${upperType}-${String(newNum).padStart(3, '0')}`;
}

function updateGeneratedTagId() {
    const type = document.getElementById('animalType').value;
    const editId = document.getElementById('editAnimalId').value;
    if (editId) {
        const animal = animals.find(a => a.id == editId);
        if (animal) {
            document.getElementById('generatedTagId').value = animal.tag_id;
            return;
        }
    }
    if (type) {
        const newTag = generateUniqueTagId(type);
        document.getElementById('generatedTagId').value = newTag;
    } else {
        document.getElementById('generatedTagId').value = '';
    }
}

function loadDataFromStorage() {
    animals = JSON.parse(localStorage.getItem('livestock_animals') || '[]');
    healthRecords = JSON.parse(localStorage.getItem('livestock_health') || '[]');
    systemUsers = JSON.parse(localStorage.getItem('livestock_users') || '[]');
    scanLogs = JSON.parse(localStorage.getItem('livestock_logs') || '[]');
    const deletedStack = localStorage.getItem('livestock_deleted_stack');
    if (deletedStack) deletedItemsStack = JSON.parse(deletedStack);
    else deletedItemsStack = [];
    
    if (animals.length === 0) {
        animals = [
            { id: 1, tag_id: "COW-001", name: "Bessie", animal_type: "Cow", breed: "Holstein Friesian", sex: "Female", birthdate: "2020-03-15", is_pregnant: true, is_sick: false, owner_contact: "555-0101" },
            { id: 2, tag_id: "COW-002", name: "Daisy", animal_type: "Cow", breed: "Jersey", sex: "Female", birthdate: "2021-07-22", is_pregnant: false, is_sick: false, owner_contact: "555-0102" },
            { id: 3, tag_id: "GOAT-001", name: "Billy", animal_type: "Goat", breed: "Boer", sex: "Male", birthdate: "2022-01-10", is_pregnant: false, is_sick: true, owner_contact: "555-0103" },
            { id: 4, tag_id: "HORSE-001", name: "Thunder", animal_type: "Horse", breed: "Arabian", sex: "Male", birthdate: "2019-06-15", is_pregnant: false, is_sick: false, owner_contact: "555-0104" }
        ];
        saveAnimals();
    }
    
    if (healthRecords.length === 0 && animals.length > 0) {
        healthRecords = [
            { id: 1, animal_tag_id: "COW-001", animal_name: "Bessie", type: "Pregnancy Check", start_date: "2024-01-15", end_date: "", next_event_date: "2024-04-15", notes: "Regular pregnancy checkup", vet_name: "Dr. Smith", vet_contact: "555-0201" },
            { id: 2, animal_tag_id: "COW-002", animal_name: "Daisy", type: "Vaccination", start_date: "2024-02-01", end_date: "", next_event_date: "2024-08-01", notes: "Annual vaccination", vet_name: "Dr. Johnson", vet_contact: "555-0202" }
        ];
        saveHealthRecords();
    }
    
    if (systemUsers.length === 0) {
        systemUsers = [
            { id: 1, name: "Admin User", email: "admin@livestock.com", phone: "+250788000111", role: "admin", password: "admin123", approved: true },
            { id: 2, name: "Dr. Sarah", email: "vet@livestock.com", phone: "+250788000222", role: "vet", password: "vet123", approved: true },
            { id: 3, name: "Peter Owner", email: "owner@livestock.com", phone: "+250788000333", role: "owner", password: "owner123", approved: true }
        ];
        saveUsers();
    }
}

function saveAnimals() { localStorage.setItem('livestock_animals', JSON.stringify(animals)); }
function saveHealthRecords() { localStorage.setItem('livestock_health', JSON.stringify(healthRecords)); }
function saveUsers() { localStorage.setItem('livestock_users', JSON.stringify(systemUsers)); }
function saveLogs() { localStorage.setItem('livestock_logs', JSON.stringify(scanLogs)); }
function saveDeletedStack() { localStorage.setItem('livestock_deleted_stack', JSON.stringify(deletedItemsStack)); }

function addScanLog(action, details) {
    if (currentUser) {
        scanLogs.unshift({ id: Date.now(), user_name: currentUser.name, user_role: currentUser.role, action: action, details: details, created_at: new Date().toLocaleString() });
        if (scanLogs.length > 100) scanLogs.pop();
        saveLogs();
    }
}

function showToast(msg, isErr = false) {
    let t = document.createElement('div');
    t.className = 'toast' + (isErr ? ' error' : '');
    t.innerText = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

function populateAnimalTypes() {
    let typeSelect = document.getElementById('animalType');
    typeSelect.innerHTML = '<option value="">Select Type</option>';
    animalTypes.forEach(type => { let opt = document.createElement('option'); opt.value = type; opt.textContent = type; typeSelect.appendChild(opt); });
}

function populateBreeds() {
    let type = document.getElementById('animalType').value;
    let breedSelect = document.getElementById('animalBreed');
    breedSelect.innerHTML = '<option value="">Select Breed</option>';
    if (type && breedDatabase[type]) {
        breedDatabase[type].forEach(breed => { let opt = document.createElement('option'); opt.value = breed; opt.textContent = breed; breedSelect.appendChild(opt); });
    }
    updateGeneratedTagId();
}

document.getElementById('landingImageUpload').addEventListener('change', function(e) {
    let file = e.target.files[0];
    if (file) { let reader = new FileReader(); reader.onload = function(ev) { document.getElementById('landingImagePreview').innerHTML = `<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`; localStorage.setItem('livestock_landing_image', ev.target.result); }; reader.readAsDataURL(file); }
});
const savedLandingImage = localStorage.getItem('livestock_landing_image');
if (savedLandingImage) document.getElementById('landingImagePreview').innerHTML = `<img src="${savedLandingImage}" style="width:100%; height:100%; object-fit:cover;">`;

function performLogin() {
    let role = document.getElementById('loginRole').value, pwd = document.getElementById('loginPassword').value, errorDiv = document.getElementById('loginError'), helpDiv = document.getElementById('accountHelpMessage');
    errorDiv.style.display = 'none'; helpDiv.style.display = 'none';
    if (!pwd) { errorDiv.innerHTML = 'Please enter password'; errorDiv.style.display = 'block'; return; }
    let found = systemUsers.find(u => u.role === role && u.password === pwd && u.approved);
    if (found) {
        currentUser = found;
        document.getElementById('landingPage').style.display = 'none';
        document.getElementById('appContainer').style.display = 'block';
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('loginPassword').value = '';
        updateUIByRole();
        refreshAllData();
        addScanLog('Login', `${found.name} logged in`);
        showToast(`Welcome, ${found.name}!`);
    } else { errorDiv.innerHTML = 'Invalid credentials!'; errorDiv.style.display = 'block'; helpDiv.style.display = 'block'; }
}

function refreshAllData() {
    updateDashboard();
    renderAnimalsTable();
    renderHealthRecordsTable();
    renderUsersTable();
    renderScanLogs();
    updateHealthTagOptions();
}

function logout() { if (currentUser) addScanLog('Logout', `${currentUser.name} logged out`); currentUser = null; document.getElementById('landingPage').style.display = 'block'; document.getElementById('appContainer').style.display = 'none'; document.getElementById('loginModal').style.display = 'none'; }

function updateUIByRole() {
    let role = currentUser.role, isVet = (role === 'vet');
    document.getElementById('navAnimals').style.display = isVet ? 'none' : 'flex';
    document.getElementById('navRegisterAnimal').style.display = (role === 'admin' || role === 'owner') ? 'flex' : 'none';
    document.getElementById('navRegisterHealth').style.display = (role === 'admin' || role === 'vet') ? 'flex' : 'none';
    document.getElementById('navUsers').style.display = (role === 'admin') ? 'flex' : 'none';
    document.getElementById('navScanLogs').style.display = (role === 'admin') ? 'flex' : 'none';
    document.getElementById('userAvatar').innerHTML = currentUser.name.charAt(0).toUpperCase();
    document.getElementById('userName').innerHTML = currentUser.name;
    let roleNames = { admin: 'Administrator', vet: 'Veterinarian', owner: 'Owner' };
    document.getElementById('userRoleBadge').innerHTML = `<span class="badge ${role}">${roleNames[role]}</span>`;
}

function navigateTo(page) {
    document.querySelectorAll('.main-content > div').forEach(div => div.classList.add('hidden'));
    document.getElementById(page + 'Section').classList.remove('hidden');
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    let activeLink = document.querySelector(`.nav-link[data-page="${page}"]`);
    if (activeLink) activeLink.classList.add('active');
    if (page === 'dashboard') updateDashboard();
    if (page === 'animals' && currentUser.role !== 'vet') renderAnimalsTable();
    if (page === 'healthRecords') renderHealthRecordsTable();
    if (page === 'registerHealth') updateHealthTagOptions();
    if (page === 'users' && currentUser.role === 'admin') renderUsersTable();
    if (page === 'scanLogs' && currentUser.role === 'admin') renderScanLogs();
}

function updateDashboard() {
    document.getElementById('totalAnimals').innerText = animals.length;
    document.getElementById('totalHealth').innerText = healthRecords.length;
    document.getElementById('sickCount').innerText = animals.filter(a => a.is_sick).length;
    document.getElementById('totalUsers').innerText = systemUsers.length;
    let recent = ''; for (let i = 0; i < Math.min(5, scanLogs.length); i++) { recent += `<div style="padding:10px 0; border-bottom:1px solid #e0e8e2;"><strong>${scanLogs[i].action}</strong> - ${scanLogs[i].user_name}<br><span style="font-size:11px; color:#5a7a6a;">${scanLogs[i].created_at}</span></div>`; }
    document.getElementById('recentActivityList').innerHTML = recent || '<p style="text-align:center; padding:20px;">No recent activity</p>';
}

function renderScanLogs() { 
    let c = document.getElementById('scanLogsFullList'); 
    if (!c) return; 
    c.innerHTML = scanLogs.length ? scanLogs.map(l => `<div class="scan-log-item"><div><span class="log-action">${l.action}</span><br><span class="log-time">${l.details}</span></div><div><span class="log-time">${l.user_name} - ${l.created_at}</span></div></div>`).join('') : '<div style="padding:20px;text-align:center;">No logs yet</div>'; 
}

function renderAnimalsTable() {
    if (currentUser && currentUser.role === 'vet') return;
    let tbody = document.getElementById('animalsTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (animals.length === 0) { tbody.innerHTML = '<tr class="empty-row"><td colspan="8">No animals registered. Click "Add Animal" to get started.</tr>'; return; }
    let canEdit = (currentUser && (currentUser.role === 'admin' || currentUser.role === 'owner'));
    let canDelete = (currentUser && currentUser.role === 'admin');
    animals.forEach(animal => { let row = tbody.insertRow(); let btns = ''; if (canEdit) btns += `<button class="edit" onclick="editAnimal(${animal.id})">Edit</button>`; if (canDelete) btns += `<button class="delete" onclick="deleteAnimal(${animal.id})">Delete</button>`; row.innerHTML = `<td><strong>${animal.tag_id}</strong></td><td>${animal.name}</td><td>${animal.animal_type}</td><td>${animal.breed || ''}</td><td>${animal.sex}</td><td><span class="badge ${animal.is_pregnant ? 'pregnant' : 'healthy'}">${animal.is_pregnant ? 'Pregnant' : 'Not Pregnant'}</span></td><td><span class="badge ${animal.is_sick ? 'sick' : 'healthy'}">${animal.is_sick ? 'Sick' : 'Healthy'}</span></td><td>${btns}</td>`; });
}

function renderHealthRecordsTable() {
    let tbody = document.getElementById('healthTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (healthRecords.length === 0) { tbody.innerHTML = '<tr class="empty-row"><td colspan="8">No health records. Click "Add Health Record" to get started.</tr>'; return; }
    let canEdit = (currentUser && (currentUser.role === 'admin' || currentUser.role === 'vet'));
    let canDelete = (currentUser && currentUser.role === 'admin');
    healthRecords.forEach(record => { let row = tbody.insertRow(); let btns = ''; if (canEdit) btns += `<button class="edit" onclick="editHealth(${record.id})">Edit</button>`; if (canDelete) btns += `<button class="delete" onclick="deleteHealth(${record.id})">Delete</button>`; row.innerHTML = `<td><strong>${record.animal_tag_id}</strong></td><td>${record.animal_name || '—'}</td><td><span class="badge vaccination">${record.type}</span></td><td>${record.start_date || '—'}</td><td>${record.end_date || '—'}</td><td>${record.next_event_date || '—'}</td><td>${record.vet_name || '—'}</td><td>${btns}</td>`; });
}

function updateHealthTagOptions() { let sel = document.getElementById('healthTagId'); if (!sel) return; sel.innerHTML = '<option value="">Select Animal</option>'; animals.forEach(animal => { let opt = document.createElement('option'); opt.value = animal.tag_id; opt.textContent = `${animal.name} (${animal.tag_id}) - ${animal.animal_type}`; sel.appendChild(opt); }); }

document.getElementById('registerAnimalForm').onsubmit = function(e) {
    e.preventDefault();
    if (currentUser.role !== 'admin' && currentUser.role !== 'owner') { showToast("Permission denied!", true); return; }
    let sex = document.getElementById('animalSex').value, preg = document.getElementById('animalPregnant').value === 'true';
    if (sex === 'Male' && preg) { document.getElementById('animalFormError').style.display = 'flex'; document.getElementById('animalFormError').innerHTML = 'ERROR: A male animal cannot be pregnant!'; return; }
    let editId = document.getElementById('editAnimalId').value;
    let generatedTag = document.getElementById('generatedTagId').value;
    let animalData = { id: editId ? parseInt(editId) : Date.now(), tag_id: generatedTag, name: document.getElementById('animalName').value.trim(), animal_type: document.getElementById('animalType').value, breed: document.getElementById('animalBreed').value, sex: sex, birthdate: document.getElementById('animalBirthdate').value, is_pregnant: preg, is_sick: document.getElementById('animalSick').value === 'true', owner_contact: document.getElementById('animalOwnerContact').value };
    if (!animalData.name || !animalData.animal_type || !animalData.breed) { showToast("Name, Type and Breed are required", true); return; }
    if (!editId && animals.some(a => a.tag_id === generatedTag)) { showToast("Tag ID already exists!", true); return; }
    document.getElementById('animalFormError').style.display = 'none';
    if (editId) { let index = animals.findIndex(a => a.id == editId); if (index !== -1) animals[index] = animalData; addScanLog('Updated Animal', `${animalData.name} updated`); showToast("Animal updated successfully"); } 
    else { animals.push(animalData); addScanLog('Added Animal', `${animalData.name} registered with tag ${generatedTag}`); showToast("Animal registered successfully"); }
    saveAnimals(); document.getElementById('editAnimalId').value = ''; this.reset(); document.getElementById('animalBreed').innerHTML = '<option value="">Select Breed</option>'; document.getElementById('generatedTagId').value = ''; refreshAllData(); navigateTo('animals');
};

document.getElementById('registerHealthForm').onsubmit = function(e) {
    e.preventDefault();
    if (currentUser.role !== 'admin' && currentUser.role !== 'vet') { showToast("Only Admin or Vet can manage health records", true); return; }
    let selectedTag = document.getElementById('healthTagId').value, selectedAnimal = animals.find(a => a.tag_id === selectedTag);
    let editId = document.getElementById('editHealthId').value;
    let recordData = { id: editId ? parseInt(editId) : Date.now(), animal_tag_id: selectedTag, animal_name: selectedAnimal ? selectedAnimal.name : '', type: document.getElementById('healthType').value, start_date: document.getElementById('healthStartDate').value, end_date: document.getElementById('healthEndDate').value, next_event_date: document.getElementById('healthNextEvent').value, notes: document.getElementById('healthNotes').value, vet_name: document.getElementById('healthVetName').value, vet_contact: document.getElementById('healthVetContact').value };
    if (!recordData.animal_tag_id || !recordData.start_date) { showToast("Animal and Start Date are required", true); return; }
    if (editId) { let index = healthRecords.findIndex(r => r.id == editId); if (index !== -1) healthRecords[index] = recordData; addScanLog('Updated Health', `${recordData.type} record updated`); showToast("Health record updated"); } 
    else { healthRecords.push(recordData); addScanLog('Added Health', `${recordData.type} record added for ${recordData.animal_tag_id}`); showToast("Health record saved"); }
    saveHealthRecords(); document.getElementById('editHealthId').value = ''; this.reset(); refreshAllData(); navigateTo('healthRecords');
};

document.getElementById('registerUserForm').onsubmit = function(e) { e.preventDefault(); if (currentUser.role !== 'admin') { showToast("Only Admin can create accounts!", true); return; } let newUser = { id: Date.now(), name: document.getElementById('newUserName').value, email: document.getElementById('newUserEmail').value, phone: document.getElementById('newUserPhone').value, role: document.getElementById('newUserRole').value, password: document.getElementById('newUserPassword').value || 'temp123', approved: true }; systemUsers.push(newUser); saveUsers(); addScanLog('Created User', `Account for ${newUser.name} created`); showToast(`Account created for ${newUser.name}`); renderUsersTable(); this.reset(); };
function renderUsersTable() { let tbody = document.getElementById('usersTable'); if (!tbody) return; tbody.innerHTML = ''; systemUsers.forEach(user => { let row = tbody.insertRow(); let delBtn = (currentUser?.role === 'admin' && user.role !== 'admin') ? `<button class="delete" onclick="deleteUser(${user.id})">Delete</button>` : '—'; row.innerHTML = `<td><strong>${user.name}</strong></td><td>${user.email}</td><td>${user.phone || ''}</td><td><span class="badge ${user.role}">${user.role}</span></td><td>${delBtn}</td>`; }); }
function deleteUser(id) { if (confirm('Delete this user?')) { systemUsers = systemUsers.filter(u => u.id !== id); saveUsers(); renderUsersTable(); showToast('User deleted'); } }

// MODIFIED: deleteAnimal now stores deleted item in stack
function editAnimal(id) { let animal = animals.find(a => a.id === id); if (!animal) return; document.getElementById('editAnimalId').value = animal.id; document.getElementById('generatedTagId').value = animal.tag_id; document.getElementById('animalName').value = animal.name; document.getElementById('animalType').value = animal.animal_type; populateBreeds(); setTimeout(() => document.getElementById('animalBreed').value = animal.breed, 50); document.getElementById('animalSex').value = animal.sex; document.getElementById('animalBirthdate').value = animal.birthdate || ''; document.getElementById('animalPregnant').value = animal.is_pregnant ? 'true' : 'false'; document.getElementById('animalSick').value = animal.is_sick ? 'true' : 'false'; document.getElementById('animalOwnerContact').value = animal.owner_contact || ''; navigateTo('registerAnimal'); }

function deleteAnimal(id) { 
    if (currentUser.role !== 'admin') { showToast("Only Admin can delete animals", true); return; } 
    let animal = animals.find(a => a.id === id); 
    if (animal && confirm(`Delete ${animal.name}? This will also delete all health records.`)) {
        // Store in deleted stack for recovery
        deletedItemsStack.unshift({ type: 'animal', data: JSON.parse(JSON.stringify(animal)), timestamp: new Date().toISOString(), logId: Date.now() });
        if (deletedItemsStack.length > 50) deletedItemsStack.pop();
        saveDeletedStack();
        
        animals = animals.filter(a => a.id !== id); 
        healthRecords = healthRecords.filter(h => h.animal_tag_id !== animal.tag_id); 
        saveAnimals(); 
        saveHealthRecords(); 
        addScanLog('Deleted Animal', `${animal.tag_id} removed`); 
        refreshAllData(); 
        showToast('Animal deleted. You can recover from Activity Logs if needed.');
    } 
}

// MODIFIED: deleteHealth now stores deleted item
function editHealth(id) { let record = healthRecords.find(r => r.id === id); if (!record) return; document.getElementById('editHealthId').value = record.id; document.getElementById('healthTagId').value = record.animal_tag_id; document.getElementById('healthType').value = record.type; document.getElementById('healthStartDate').value = record.start_date || ''; document.getElementById('healthEndDate').value = record.end_date || ''; document.getElementById('healthNextEvent').value = record.next_event_date || ''; document.getElementById('healthNotes').value = record.notes || ''; document.getElementById('healthVetName').value = record.vet_name || ''; document.getElementById('healthVetContact').value = record.vet_contact || ''; navigateTo('registerHealth'); }

function deleteHealth(id) { 
    if (currentUser.role !== 'admin') { showToast("Only Admin can delete health records", true); return; } 
    let record = healthRecords.find(r => r.id === id);
    if (record && confirm('Delete this health record?')) {
        deletedItemsStack.unshift({ type: 'health', data: JSON.parse(JSON.stringify(record)), timestamp: new Date().toISOString(), logId: Date.now() });
        if (deletedItemsStack.length > 50) deletedItemsStack.pop();
        saveDeletedStack();
        
        healthRecords = healthRecords.filter(r => r.id !== id); 
        saveHealthRecords(); 
        addScanLog('Deleted Health', `Health record for ${record.animal_tag_id} removed`); 
        refreshAllData(); 
        showToast('Health record deleted. You can recover from Activity Logs.');
    } 
}

// ==================== NEW FUNCTIONS FOR LOG MANAGEMENT ====================
function recoverLastDeleted() {
    if (!currentUser || currentUser.role !== 'admin') {
        showToast("Only Admin can recover deleted items", true);
        return;
    }
    if (deletedItemsStack.length === 0) {
        showToast("No deleted items available to recover", true);
        document.getElementById('recoverStatus').style.display = 'none';
        return;
    }
    const lastDeleted = deletedItemsStack[0];
    if (lastDeleted.type === 'animal') {
        const animalExists = animals.some(a => a.id === lastDeleted.data.id);
        if (animalExists) {
            showToast("Animal already exists, cannot recover duplicate", true);
            deletedItemsStack.shift();
            saveDeletedStack();
            recoverLastDeleted();
            return;
        }
        animals.push(lastDeleted.data);
        saveAnimals();
        addScanLog('Recovered Animal', `Restored ${lastDeleted.data.name} (${lastDeleted.data.tag_id})`);
        showToast(`Recovered animal: ${lastDeleted.data.name}`);
    } else if (lastDeleted.type === 'health') {
        const healthExists = healthRecords.some(h => h.id === lastDeleted.data.id);
        if (healthExists) {
            showToast("Health record already exists", true);
            deletedItemsStack.shift();
            saveDeletedStack();
            recoverLastDeleted();
            return;
        }
        healthRecords.push(lastDeleted.data);
        saveHealthRecords();
        addScanLog('Recovered Health Record', `Restored ${lastDeleted.data.type} for ${lastDeleted.data.animal_tag_id}`);
        showToast(`Recovered health record: ${lastDeleted.data.type}`);
    }
    deletedItemsStack.shift();
    saveDeletedStack();
    refreshAllData();
    renderScanLogs();
    
    const statusDiv = document.getElementById('recoverStatus');
    statusDiv.style.display = 'block';
    statusDiv.innerHTML = `<strong>✅ Recovery successful!</strong> Last deleted item has been restored.`;
    setTimeout(() => { statusDiv.style.display = 'none'; }, 5000);
}

function deleteLogsOlderThanDays() {
    if (!currentUser || currentUser.role !== 'admin') {
        showToast("Only Admin can delete logs", true);
        return;
    }
    let days = prompt("Delete logs older than how many days? (Enter number, e.g., 7)", "7");
    if (days === null) return;
    days = parseInt(days);
    if (isNaN(days) || days < 0) {
        showToast("Please enter a valid number of days", true);
        return;
    }
    const now = new Date();
    const cutoff = new Date(now.setDate(now.getDate() - days));
    const originalCount = scanLogs.length;
    scanLogs = scanLogs.filter(log => {
        const logDate = new Date(log.created_at);
        return logDate >= cutoff;
    });
    if (scanLogs.length === originalCount) {
        showToast(`No logs older than ${days} days found.`, false);
    } else {
        saveLogs();
        addScanLog('Bulk Log Deletion', `Deleted ${originalCount - scanLogs.length} logs older than ${days} days`);
        renderScanLogs();
        showToast(`Deleted ${originalCount - scanLogs.length} old logs.`);
    }
}

function deleteLogsByAction() {
    if (!currentUser || currentUser.role !== 'admin') {
        showToast("Only Admin can delete logs", true);
        return;
    }
    const actions = [...new Set(scanLogs.map(l => l.action))];
    if (actions.length === 0) {
        showToast("No logs to filter", true);
        return;
    }
    let actionList = actions.join(", ");
    let actionToDelete = prompt(`Enter the exact action type to delete all logs with that action.\nAvailable actions:\n${actionList}\n\nExample: "Deleted Animal"`, "");
    if (!actionToDelete) return;
    const originalCount = scanLogs.length;
    scanLogs = scanLogs.filter(log => log.action !== actionToDelete);
    if (scanLogs.length === originalCount) {
        showToast(`No logs found with action "${actionToDelete}"`, true);
    } else {
        saveLogs();
        addScanLog('Bulk Log Deletion by Action', `Deleted ${originalCount - scanLogs.length} logs with action "${actionToDelete}"`);
        renderScanLogs();
        showToast(`Deleted ${originalCount - scanLogs.length} logs with action "${actionToDelete}"`);
    }
}

function clearAllLogs() {
    if (!currentUser || currentUser.role !== 'admin') {
        showToast("Only Admin can clear all logs", true);
        return;
    }
    if (confirm("⚠️ WARNING: This will delete ALL activity logs permanently. This action cannot be undone. Are you sure?")) {
        if (confirm("Type 'DELETE ALL LOGS' to confirm:")) {
            const confirmation = prompt(`Type "DELETE ALL LOGS" to confirm:`);
            if (confirmation === "DELETE ALL LOGS") {
                const deletedCount = scanLogs.length;
                scanLogs = [];
                saveLogs();
                addScanLog('Cleared All Logs', `Admin deleted all ${deletedCount} logs`);
                renderScanLogs();
                showToast(`Deleted all ${deletedCount} logs.`);
            } else {
                showToast("Confirmation text did not match. Deletion cancelled.");
            }
        }
    }
}

// Attach new event listeners after DOM loads
document.getElementById('recoverLastDeletedBtn').addEventListener('click', recoverLastDeleted);
document.getElementById('deleteLogsOlderThanBtn').addEventListener('click', deleteLogsOlderThanDays);
document.getElementById('deleteLogsByActionBtn').addEventListener('click', deleteLogsByAction);
document.getElementById('clearAllLogsBtn').addEventListener('click', clearAllLogs);

document.getElementById('openLoginBtn').onclick = () => document.getElementById('loginModal').style.display = 'flex';
document.getElementById('closeModalBtn').onclick = () => document.getElementById('loginModal').style.display = 'none';
document.getElementById('loginBtn').onclick = performLogin;
document.getElementById('logoutBtn').onclick = logout;
window.onclick = e => { if (e.target == document.getElementById('loginModal')) document.getElementById('loginModal').style.display = 'none'; };
document.getElementById('loginPassword').onkeypress = e => { if (e.key === 'Enter') performLogin(); };
document.getElementById('animalType').addEventListener('change', function() { populateBreeds(); updateGeneratedTagId(); });

populateAnimalTypes();
loadDataFromStorage();
</script>
</body>
</html>