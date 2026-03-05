<?php
session_start();
include 'config.php';

// Secure admin session check
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports - QuickCare Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
        }

        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--dark-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: var(--light-blue);
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: var(--gray-blue);
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--mid-blue);
            border-left: 4px solid var(--light-blue);
            color: white;
        }

        .logout-btn {
            display: block;
            width: 80%;
            margin: 20px auto 0 auto;
            padding: 10px;
            background-color: var(--soft-blue);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }

        /* Dashboard Cards */
        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-left: 8px solid var(--soft-blue);
        }

        .card h3 {
            margin: 0;
            color: var(--dark-blue);
            font-size: 16px;
        }

        .card p {
            margin-top: 10px;
            font-size: 24px;
            color: var(--mid-blue);
            font-weight: bold;
        }

        /* Report Section */
        .report-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            background: white;
            border: 2px solid var(--soft-blue);
            color: var(--mid-blue);
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .tab-btn.active,
        .tab-btn:hover {
            background: var(--soft-blue);
            color: white;
        }

        .report-content {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .report-content.active {
            display: block;
        }

        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-bar input,
        .filter-bar select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-apply {
            padding: 8px 15px;
            background: var(--mid-blue);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-export {
            padding: 8px 15px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: auto;
        }

        .charts-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .chart-box {
            flex: 1;
            min-width: 300px;
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-table th,
        .report-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .report-table th {
            background: var(--light-blue);
            color: var(--dark-blue);
        }

        .report-table tr:hover {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="uploads/logo.JPG" alt="QuickCare Logo"
            style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;"
            onerror="this.src=''; this.style.display='none';">
        <h2>QuickCare</h2>
        <a href="admin.php">Dashboard</a>
        <a href="Admin_appoitment.php">View Appointments</a>
        <a href="Admin_doctor.php">Manage Doctors</a>
        <a href="Admin_recept.php">Manage Receptionist</a>
        <a href="Admin_patient.php">Manage Patients</a>
        <a href="Admin_medicine.php">View Medicine</a>
        <a href="Admin_payment.php">View Payments</a>
        <a href="Admin_feedback.php">View Feedback</a>
        <a href="Admin_report.php" class="active">Reports</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main">
        <?php
        // Fetch Summary Data
        $patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patient_tbl"))['count'];
        $doctors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_tbl"))['count'];
        $appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment_tbl"))['count'];
        $revenue_res = mysqli_query($conn, "SELECT SUM(AMOUNT) as total FROM payment_tbl WHERE STATUS = 'COMPLETED'");
        $revenue = mysqli_fetch_assoc($revenue_res)['total'];

        // Fetch Doctors for dropdown
        $doctors_list = mysqli_query($conn, "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl");
        ?>

        <h2>Reporting Module</h2>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Patients</h3>
                <p><?= $patients ?></p>
            </div>
            <div class="card">
                <h3>Total Doctors</h3>
                <p><?= $doctors ?></p>
            </div>
            <div class="card">
                <h3>Total Appointments</h3>
                <p><?= $appointments ?></p>
            </div>
            <div class="card">
                <h3>Total Revenue</h3>
                <p>₹<?= number_format($revenue ?: 0, 2) ?></p>
            </div>
        </div>

        <!-- Report Tabs -->
        <div class="report-tabs">
            <button class="tab-btn active" onclick="openTab('patient')">Patient Reports</button>
            <button class="tab-btn" onclick="openTab('appointment')">Appointment Reports</button>
            <button class="tab-btn" onclick="openTab('revenue')">Revenue Reports</button>
        </div>

        <!-- Patient Report -->
        <div id="patient" class="report-content active">
            <div class="filter-bar">
                <input type="date" id="pat_start" value="<?= date('Y-m-d', strtotime('-6 months')) ?>">
                <input type="date" id="pat_end" value="<?= date('Y-m-d') ?>">
                <button class="btn-apply" onclick="loadPatientReport()">Apply</button>
                <button class="btn-export" onclick="exportPDF('patient')">Export PDF</button>
            </div>
            <div class="charts-row">
                <div class="chart-box"><canvas id="patBarChart"></canvas></div>
                <div class="chart-box"><canvas id="patPieChart"></canvas></div>
            </div>
            <table class="report-table" id="pat_table">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Appointment Report -->
        <div id="appointment" class="report-content">
            <div class="filter-bar">
                <input type="date" id="app_start" value="<?= date('Y-m-d', strtotime('-6 months')) ?>">
                <input type="date" id="app_end" value="<?= date('Y-m-d') ?>">
                <select id="app_doc">
                    <option value="">All Doctors</option>
                    <?php while ($d = mysqli_fetch_assoc($doctors_list)) { ?>
                        <option value="<?= $d['DOCTOR_ID'] ?>">Dr. <?= $d['FIRST_NAME'] ?>     <?= $d['LAST_NAME'] ?></option>
                    <?php } ?>
                </select>
                <button class="btn-apply" onclick="loadAppointmentReport()">Apply</button>
                <button class="btn-export" onclick="exportPDF('appointment')">Export PDF</button>
            </div>
            <div class="charts-row">
                <div class="chart-box"><canvas id="appLineChart"></canvas></div>
                <div class="chart-box"><canvas id="appBarChart"></canvas></div>
            </div>
            <div class="charts-row" style="justify-content:center;">
                <div class="chart-box" style="max-width: 400px;"><canvas id="appPieChart"></canvas></div>
            </div>
            <table class="report-table" id="app_table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient Name</th>
                        <th>Doctor Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Revenue Report -->
        <div id="revenue" class="report-content">
            <div class="filter-bar">
                <input type="date" id="rev_start" value="<?= date('Y-m-d', strtotime('-6 months')) ?>">
                <input type="date" id="rev_end" value="<?= date('Y-m-d') ?>">
                <button class="btn-apply" onclick="loadRevenueReport()">Apply</button>
                <button class="btn-export" onclick="exportPDF('revenue')">Export PDF</button>
            </div>
            <div class="charts-row">
                <div class="chart-box"><canvas id="revBarChart"></canvas></div>
                <div class="chart-box"><canvas id="revPieChart"></canvas></div>
            </div>
            <table class="report-table" id="rev_table">
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Transaction ID</th>
                        <th>Patient Name</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        // Tab Switching
        function openTab(tabName) {
            document.querySelectorAll('.report-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');

            // Trigger loading if empty
            if (tabName === 'patient') loadPatientReport();
            if (tabName === 'appointment') loadAppointmentReport();
            if (tabName === 'revenue') loadRevenueReport();
        }

        // Chart Instances
        let charts = {};

        function initChart(canvasId, type, data, options) {
            if (charts[canvasId]) {
                charts[canvasId].destroy();
            }
            const ctx = document.getElementById(canvasId).getContext('2d');
            charts[canvasId] = new Chart(ctx, {
                type: type,
                data: data,
                options: options
            });
        }

        // Patient Report
        async function loadPatientReport() {
            let start = document.getElementById('pat_start').value;
            let end = document.getElementById('pat_end').value;

            let res = await fetch(`api_report_patient.php?start_date=${start}&end_date=${end}`);
            let data = await res.json();

            // Bar Chart
            initChart('patBarChart', 'bar', {
                labels: data.monthly_registration.labels,
                datasets: [{
                    label: 'Monthly Registrations',
                    data: data.monthly_registration.data,
                    backgroundColor: '#5790AB'
                }]
            }, { responsive: true });

            // Pie Chart
            let patBgColors = data.gender_distribution.labels.map(l => {
                let g = l.toLowerCase();
                if (g === 'male') return '#3498db';
                if (g === 'female') return '#e74c3c';
                return '#2ecc71';
            });
            initChart('patPieChart', 'pie', {
                labels: data.gender_distribution.labels,
                datasets: [{
                    data: data.gender_distribution.data,
                    backgroundColor: patBgColors
                }]
            }, { responsive: true, plugins: { title: { display: true, text: 'Gender Distribution' } } });

            // Table
            let tbody = document.querySelector('#pat_table tbody');
            tbody.innerHTML = '';
            data.table_data.forEach(row => {
                tbody.innerHTML += `<tr>
            <td>${row.PATIENT_ID}</td>
            <td>${row.FIRST_NAME} ${row.LAST_NAME}</td>
            <td>${row.GENDER || 'N/A'}</td>
            <td>${row.PHONE || ''}</td>
            <td>${row.CREATED_AT ? row.CREATED_AT.substring(0, 10) : ''}</td>
        </tr>`;
            });
        }

        // Appointment Report
        async function loadAppointmentReport() {
            let start = document.getElementById('app_start').value;
            let end = document.getElementById('app_end').value;
            let did = document.getElementById('app_doc').value;

            let res = await fetch(`api_report_appointment.php?start_date=${start}&end_date=${end}&doctor_id=${did}`);
            let data = await res.json();

            // Line Chart
            initChart('appLineChart', 'line', {
                labels: data.daily_trend.labels,
                datasets: [{
                    label: 'Daily Appointments',
                    data: data.daily_trend.data,
                    borderColor: '#5790AB',
                    fill: false
                }]
            }, { responsive: true });

            // Bar Chart
            initChart('appBarChart', 'bar', {
                labels: data.doctor_wise.labels,
                datasets: [{
                    label: 'Appointments by Doctor',
                    data: data.doctor_wise.data,
                    backgroundColor: '#9CCDD8'
                }]
            }, { responsive: true });

            // Pie Chart
            let appBgColors = data.status_distribution.labels.map(l => {
                if (l === 'COMPLETED') return '#2ecc71';
                if (l === 'CANCELLED') return '#e74c3c';
                return '#f1c40f';
            });
            initChart('appPieChart', 'pie', {
                labels: data.status_distribution.labels,
                datasets: [{
                    data: data.status_distribution.data,
                    backgroundColor: appBgColors
                }]
            }, { responsive: true, plugins: { title: { display: true, text: 'Appointment Status' } } });

            // Table
            let tbody = document.querySelector('#app_table tbody');
            tbody.innerHTML = '';
            data.table_data.forEach(row => {
                let statusColor = row.status === 'COMPLETED' ? 'green' : (row.status === 'CANCELLED' ? 'red' : 'orange');
                tbody.innerHTML += `<tr>
            <td>${row.date}</td>
            <td>${row.time}</td>
            <td>${row.patient}</td>
            <td>${row.doctor}</td>
            <td style="color:${statusColor}; font-weight:bold;">${row.status}</td>
        </tr>`;
            });
        }

        // Revenue Report
        async function loadRevenueReport() {
            let start = document.getElementById('rev_start').value;
            let end = document.getElementById('rev_end').value;

            let res = await fetch(`api_report_revenue.php?start_date=${start}&end_date=${end}`);
            let data = await res.json();

            // Bar Chart
            initChart('revBarChart', 'bar', {
                labels: data.monthly_revenue.labels,
                datasets: [{
                    label: 'Monthly Revenue (₹)',
                    data: data.monthly_revenue.data,
                    backgroundColor: '#2ecc71'
                }]
            }, { responsive: true });

            // Pie Chart
            initChart('revPieChart', 'pie', {
                labels: data.payment_method.labels,
                datasets: [{
                    data: data.payment_method.data,
                    backgroundColor: ['#3498db', '#9b59b6', '#f1c40f', '#e67e22']
                }]
            }, { responsive: true, plugins: { title: { display: true, text: 'Payment Methods' } } });

            // Table
            let tbody = document.querySelector('#rev_table tbody');
            tbody.innerHTML = '';
            data.table_data.forEach(row => {
                tbody.innerHTML += `<tr>
            <td>${row.date}</td>
            <td>₹${parseFloat(row.amount).toFixed(2)}</td>
            <td>${row.mode}</td>
            <td>${row.transaction || 'N/A'}</td>
            <td>${row.patient}</td>
        </tr>`;
            });
        }

        // Initial Load
        window.onload = () => {
            loadPatientReport();
        };

        // PDF Export: open printable table-only view for current filters
        function exportPDF(type) {
            let url = 'Admin_report_pdf.php?type=' + encodeURIComponent(type);

            if (type === 'patient') {
                const start = document.getElementById('pat_start').value;
                const end = document.getElementById('pat_end').value;
                url += '&start_date=' + encodeURIComponent(start) +
                    '&end_date=' + encodeURIComponent(end);
            } else if (type === 'appointment') {
                const start = document.getElementById('app_start').value;
                const end = document.getElementById('app_end').value;
                const did = document.getElementById('app_doc').value;
                url += '&start_date=' + encodeURIComponent(start) +
                    '&end_date=' + encodeURIComponent(end) +
                    '&doctor_id=' + encodeURIComponent(did);
            } else if (type === 'revenue') {
                const start = document.getElementById('rev_start').value;
                const end = document.getElementById('rev_end').value;
                url += '&start_date=' + encodeURIComponent(start) +
                    '&end_date=' + encodeURIComponent(end);
            }

            window.open(url, '_blank');
        }
    </script>

</body>

</html>