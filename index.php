<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentFlow - Modern Rental Management</title>
    <link rel="icon" type="image/png" href="icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="include/css/style.css">
    <style>
        /* Minimum Width Warning Overlay */
        .min-width-warning {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .min-width-warning-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .min-width-warning-icon {
            font-size: 64px;
            color: #667eea;
            margin-bottom: 20px;
        }

        .min-width-warning h2 {
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .min-width-warning p {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .min-width-warning .current-width {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 15px;
        }

        /* Hide main content when width is insufficient */
        @media (max-width: 1079px) {
            .min-width-warning {
                display: flex !important;
            }
            
            body > *:not(.min-width-warning) {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Minimum Width Warning -->
    <div class="min-width-warning">
        <div class="min-width-warning-content">
            <div class="min-width-warning-icon">
                <i class="fas fa-desktop"></i>
            </div>
            <h2>Screen Too Small</h2>
            <p>RentFlow requires a minimum screen width for the best experience.</p>
            <p><strong>Please use a desktop, laptop or rotate your device to landscape mode.</strong></p>
            <div class="current-width">
                Current Width: <span id="currentWidth">---</span>px
            </div>
            <p style="margin-top: 15px; font-size: 14px; color: #999;">
                Minimum required: 1080px
            </p>
        </div>
    </div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building"></i> RentFlow
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a></li>
                            <li><a class="dropdown-item" href="owner/login.php"><i class="fas fa-user-tie"></i> Owner</a></li>
                            <li><a class="dropdown-item" href="tenant/login.php"><i class="fas fa-user"></i> Tenant</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-modern btn-sm ms-2" href="owner/register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>The Future of<br>Rental Management</h1>
            <p>Transform your property management experience with cutting-edge technology and intuitive User Experience</p>
            <div class="hero-buttons">
                <a href="#pricing" class="btn btn-modern btn-lg me-3">Get Started</a>
                <a href="#features" class="btn btn-outline-modern btn-lg">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="modern-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Powerful Features</h2>
                <p>Everything you need to manage your properties efficiently</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 fade-in">
                    <div class="glass-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h5>Centralized Dashboard</h5>
                        <p>Manage all properties, tenants and financials from one powerful interface</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in">
                    <div class="glass-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5>Automated Billing</h5>
                        <p>Generate invoices automatically and accept online payments with ease</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in">
                    <div class="glass-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h5>Maintenance Hub</h5>
                        <p>Track payment, assign notice, rules and keep everything running smoothly</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="modern-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Simple Pricing</h2>
                <p>Choose the perfect plan for your RentFlow</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 fade-in">
                    <div class="pricing-card">
                        <h5>Starter</h5>
                        <div class="price"><span style="font-size: 0.6em;">BDT</span> 99<span>/mo</span></div>
                        <ul>
                            <li><i class="fas fa-check"></i> Up to 10 Properties</li>
                            <li><i class="fas fa-check"></i> Tenant Portal</li>
                            <li><i class="fas fa-check"></i> Online Payments</li>
                            <li style="opacity: 0.4;"><i class="fas fa-times"></i> Maintenance Requests</li>
                            <li style="opacity: 0.4;"><i class="fas fa-times"></i> Custom Domain</li>
                        </ul>
                        <a href="owner/register.php" class="btn btn-modern w-100">Choose Plan</a>
                    </div>
                </div>
                <div class="col-lg-4 fade-in">
                    <div class="pricing-card popular">
                        <h5>Pro</h5>
                        <div class="price"><span style="font-size: 0.6em;">BDT</span> 299<span>/mo</span></div>
                        <ul>
                            <li><i class="fas fa-check"></i> Up to 50 Properties</li>
                            <li><i class="fas fa-check"></i> Tenant Portal</li>
                            <li><i class="fas fa-check"></i> Online Payments</li>
                            <li><i class="fas fa-check"></i> Maintenance Requests</li>
                            <li style="opacity: 0.6;"><i class="fas fa-times"></i> Custom Domain</li>
                        </ul>
                        <a href="owner/register.php" class="btn btn-outline-modern w-100" style="border-color: white;">Choose Plan</a>
                    </div>
                </div>
                <div class="col-lg-4 fade-in">
                    <div class="pricing-card">
                        <h5>Enterprise</h5>
                        <div class="price"><span style="font-size: 0.6em;">BDT</span> 499<span>/mo</span></div>
                        <ul>
                            <li><i class="fas fa-check"></i> Unlimited Properties</li>
                            <li><i class="fas fa-check"></i> Tenant Portal</li>
                            <li><i class="fas fa-check"></i> Online Payments</li>
                            <li><i class="fas fa-check"></i> Maintenance Requests</li>
                            <li><i class="fas fa-check"></i> Custom Domain</li>
                        </ul>
                        <a href="owner/register.php" class="btn btn-modern w-100">Choose Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Get In Touch</h2>
                <p>Have questions? We'd love to hear from you</p>
            </div>
            <div class="row justify-content-center fade-in">
                <div class="col-lg-8">
                    <form class="contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Your Name">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Your Email">
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-modern btn-lg">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
	<footer>
		<div class="container">
			<p>
				<a href="https://www.linkedin.com/in/mdanikbiswas/" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: inherit;">
					Developed by MD ANIK BISWAS
				</a>
				&nbsp;|&nbsp; 
				&copy; 2025 RentFlow. All Rights Reserved.
			</p>
		</div>
	</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="include/js/script.js"></script>
    <script>
        // Update current width display
        function updateWidthDisplay() {
            const widthElement = document.getElementById('currentWidth');
            if (widthElement) {
                widthElement.textContent = window.innerWidth;
            }
        }

        // Update on load and resize
        window.addEventListener('load', updateWidthDisplay);
        window.addEventListener('resize', updateWidthDisplay);
    </script>
</body>
</html>