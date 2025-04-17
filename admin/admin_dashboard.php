<?php
// Add this at the top of admin_dashboard.php
$base_url = '/food-management-system';

session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            padding: 20px;
            position: fixed;
        }
        .sidebar h3 {
            color: white;
            text-align: center;
        }
        .sidebar .nav-link {
            color: white;
            padding: 10px;
            display: block;
        }
        .sidebar .nav-link:hover, .nav-link.active {
            background-color: #495057;
            border-radius: 5px;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>NGO Admin Panel</h3>
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a href="#" class="nav-link active" onclick="loadContent('manage_donations.php')">Manage Donations</a>
                    <a href="#" class="nav-link tab-link" data-tab="manage_waste_centers.php">Manage Waste Centers</a>
                    <a class="nav-link" href="#" id="payments-link">
                        <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                        Payments
                    </a>
                </div>
            </div>
        </nav>
        <a href="logout.php" class="btn btn-danger mt-4 d-block">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Content Area for Dynamic Tabs -->
        <div id="tab-content">
            <?php include("manage_donations.php"); ?>
        </div>
    </div>

    <script>
        // First, let's check if jQuery is loaded
        if (typeof jQuery == 'undefined') {
            alert('jQuery is not loaded!');
        }

        $(document).ready(function() {
            console.log("Document ready");
            
            // Test click on any nav-link
            $('.nav-link').click(function() {
                console.log("Nav link clicked");
            });

            // Handle tab links
            $('.tab-link').click(function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                
                var page = $(this).data('tab');
                $('#tab-content').load(page);
            });

            // Handle payments link
            $("#payments-link").click(function(e) {
                e.preventDefault();
                console.log("Payments link clicked");
                
                // Remove active class from other links and add to payments
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                
                // Show loading in your content div
                $('#tab-content').html('<div class="text-center mt-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                
                $.ajax({
                    url: 'load_payments.php',
                    type: 'GET',
                    success: function(response) {
                        $('#tab-content').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        $('#tab-content').html('<div class="alert alert-danger m-3">Error loading payments data: ' + error + '</div>');
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Show manage donations by default
            showContent('manage_donations');
        });

        function showContent(section) {
            // Hide all content sections
            const allSections = document.querySelectorAll('.content-section');
            allSections.forEach(section => {
                section.style.display = 'none';
            });

            // Remove active class from all nav links
            const allLinks = document.querySelectorAll('.sidebar a');
            allLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Show selected section
            const selectedSection = document.getElementById(section);
            if(selectedSection) {
                selectedSection.style.display = 'block';
            }

            // Add active class to clicked link
            const activeLink = document.querySelector(`.sidebar a[data-section="${section}"]`);
            if(activeLink) {
                activeLink.classList.add('active');
            }

            // If it's manage donations section, make sure to show pending section
            if(section === 'manage_donations') {
                if(typeof showSection === 'function') {
                    showSection('pending');
                }
            }
        }

        // Add this to your existing JavaScript, don't replace anything
        document.addEventListener('DOMContentLoaded', function() {
            // Get the manage donations link
            const manageDonationsLink = document.querySelector('a:contains("Manage Donations")');
            
            if(manageDonationsLink) {
                manageDonationsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Hide waste centers content
                    const wasteCentersContent = document.querySelector('table:has(th:contains("Username"))').closest('div');
                    if(wasteCentersContent) {
                        wasteCentersContent.style.display = 'none';
                    }
                    
                    // Hide payments content if it exists
                    const paymentsContent = document.querySelector('.payments-content');
                    if(paymentsContent) {
                        paymentsContent.style.display = 'none';
                    }
                    
                    // Show donations content
                    const donationsContent = document.querySelector('table:has(th:contains("Donor Name"))').closest('div');
                    if(donationsContent) {
                        donationsContent.style.display = 'block';
                    }
                });
            }
        });
    </script>

</body>
</html>