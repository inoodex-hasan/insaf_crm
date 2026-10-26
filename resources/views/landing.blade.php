<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM System - Customer Relationship Management</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/landing_page.css') }}">
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <!-- Logo -->
            <a href="#home" class="logo">
                <i class="fas fa-cube"></i> 
                <span>CRM</span> System
            </a>

            <!-- Navigation Links -->
            <ul class="nav-links" id="navLinks">
                <li><a href="#home" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#about"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="#features"><i class="fas fa-th-large"></i> Features</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                <!-- Mobile Login / Dashboard -->
                <li class="mobile-login">
                    @auth
                        <a href="{{ route('tyro-dashboard.index') }}" class="login-btn-mobile"><i class="fas fa-th-large"></i> Dashboard</a>
                    @else
                        <a href="{{ route('tyro-login.login') }}" class="login-btn-mobile"><i class="fas fa-user"></i> Login</a>
                    @endauth
                </li>
            </ul>

            <!-- Right Side (Desktop) -->
            <div class="nav-right">
                @auth
                    <a href="{{ route('tyro-dashboard.index') }}" class="login-btn"><i class="fas fa-th-large"></i> Dashboard</a>
                @else
                    <a href="{{ route('tyro-login.login') }}" class="login-btn"><i class="fas fa-user"></i> Login</a>
                @endauth
            </div>

            <!-- Hamburger Menu (Mobile) -->
            <button class="hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section id="home" class="hero">
        <!-- Background Shapes -->
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>

        <div class="hero-container">
            <div class="hero-content">
                <div class="badge">
                    <i class="fas fa-rocket"></i> CRM Solution 2026
                </div>
                <h1>
                    Transform Your <br>
                    <span class="gradient-text">Business</span> 
                    with Smart CRM
                </h1>
                <p class="hero-description">
                    Manage leads, track sales, automate marketing, and deliver exceptional customer support — all from a single, powerful platform.
                </p>
                <div class="btn-group">
                    <a href="#about" class="btn-primary">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                    <a href="#contact" class="btn-outline">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                </div>
                <div class="trust-badge">
                    <div class="trust-item">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>30+</strong> Countries</span>
                    </div>
                    <div class="trust-divider"></div>
                    <div class="trust-item">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>10K+</strong> Users</span>
                    </div>
                    <div class="trust-divider"></div>
                    <div class="trust-item">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>99.9%</strong> Uptime</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="image-wrapper">
                    <div class="image-glow"></div>
                    <img src="{{ asset('assets/images/landing/images (2).jpg') }}" alt="CRM Dashboard Illustration">
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT SECTION ===== -->
    <section id="about" class="section">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-info-circle"></i> About Us</span>
            <h2>Who We <span>Are</span></h2>
            <p>We are a leading CRM solution provider dedicated to helping businesses streamline their operations and grow exponentially.</p>
        </div>
        <div class="about-container">
            <div class="about-content">
                <div class="about-text">
                    <h3>Empowering Businesses with <span>Intelligent CRM</span></h3>
                    <p>At CRM System, we believe in the power of meaningful customer relationships. Our platform is designed to help you understand your customers better, engage with them effectively, and drive sustainable business growth.</p>
                    <p>With over 10,000+ users across 30+ countries, we have established ourselves as a trusted partner for businesses of all sizes — from startups to enterprises.</p>
                    <div class="about-stats">
                        <div class="about-stat">
                            <span class="about-number">99.9%</span>
                            <span class="about-label">Uptime</span>
                        </div>
                        <div class="about-stat">
                            <span class="about-number">4.9/5</span>
                            <span class="about-label">User Rating</span>
                        </div>
                        <div class="about-stat">
                            <span class="about-number">10K+</span>
                            <span class="about-label">Active Users</span>
                        </div>
                        <div class="about-stat">
                            <span class="about-number">30+</span>
                            <span class="about-label">Countries</span>
                        </div>
                    </div>
                    <a href="#contact" class="btn-primary">Get Started</a>
                </div>
                <div class="about-image">
                    <img src="{{ asset('assets/images/landing/images (1).jpg') }}" alt="About Us">
                </div>
            </div>
        </div>
    </section>

    <!-- ===== WHY USE CRM SECTION ===== -->
    <section id="why-crm" class="section">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-question-circle"></i> Why CRM</span>
            <h2>Why Use <span>CRM</span></h2>
            <p>Discover how a CRM system can transform your business operations and drive growth.</p>
        </div>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-user-friends"></i></div>
                <h3>Better Customer Relationships</h3>
                <p>Build stronger, more personalized relationships with your customers through data-driven insights and timely engagement.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Increased Sales</h3>
                <p>Track leads, manage pipelines, and close more deals with automated workflows and intelligent sales tools.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-clock"></i></div>
                <h3>Save Time & Resources</h3>
                <p>Automate repetitive tasks, streamline operations, and allow your team to focus on what matters most — your customers.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-database"></i></div>
                <h3>Centralized Data</h3>
                <p>Keep all customer data in one place, ensuring easy access, better collaboration, and data-driven decision making.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-headset"></i></div>
                <h3>Better Customer Support</h3>
                <p>Deliver exceptional support with a unified view of customer history, preferences, and past interactions.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Data-Driven Decisions</h3>
                <p>Make informed business decisions with real-time analytics, reports, and actionable insights.</p>
            </div>
        </div>
    </section>
    <!-- ===== BENEFITS SECTION ===== -->
    <section id="benefits" class="section">
        <div class="benefits-bg-shapes">
            <div class="benefits-blob blob-1"></div>
            <div class="benefits-blob blob-2"></div>
        </div>
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-gem"></i> Benefits</span>
            <h2>Key <span>Benefits</span> of CRM</h2>
            <p>Discover how our CRM solution can transform your business with these key benefits.</p>
        </div>

        <div class="benefits-grid">
            <!-- Benefit 1: Increased Productivity -->
            <div class="benefit-card">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/images.jpg') }}" alt="Increased Productivity">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">01</span>
                    <h3>Increased Productivity</h3>
                    <p>Automate routine tasks and streamline workflows to boost team efficiency by up to 40%.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> Task Automation</li>
                        <li><i class="fas fa-check-circle"></i> Workflow Optimization</li>
                        <li><i class="fas fa-check-circle"></i> Time Management</li>
                    </ul>
                </div>
            </div>

            <!-- Benefit 2: Enterprise Architecture -->
            <div class="benefit-card reverse">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/01-Enterprise-Architecture.webp') }}" alt="Enterprise Architecture">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">02</span>
                    <h3>Enterprise Architecture</h3>
                    <p>Build a scalable and robust enterprise architecture that supports your business growth and digital transformation.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> Scalable Infrastructure</li>
                        <li><i class="fas fa-check-circle"></i> Cloud-Native Solutions</li>
                        <li><i class="fas fa-check-circle"></i> Microservices Architecture</li>
                        <li><i class="fas fa-check-circle"></i> API-First Design</li>
                    </ul>
                </div>
            </div>

            <!-- Benefit 3: Security & Reliability -->
            <div class="benefit-card">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/03-Security-and-reliability.webp') }}" alt="Security and Reliability">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">03</span>
                    <h3>Security & Reliability</h3>
                    <p>Financial-grade security ensures protection of customer data and transactions with high-availability infrastructure.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> End-to-end encryption</li>
                        <li><i class="fas fa-check-circle"></i> Secure authentication</li>
                        <li><i class="fas fa-check-circle"></i> Fraud protection</li>
                        <li><i class="fas fa-check-circle"></i> High-availability</li>
                    </ul>
                </div>
            </div>

            <!-- Benefit 4: Flexible Deployment -->
            <div class="benefit-card reverse">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/04-Flexible-Deployment-Options.webp') }}" alt="Flexible Deployment Options">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">04</span>
                    <h3>Flexible Deployment</h3>
                    <p>Choose the deployment model that best suits your business needs with flexible options across cloud, on-premise, and hybrid.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> Cloud Deployment</li>
                        <li><i class="fas fa-check-circle"></i> On-premise Solutions</li>
                        <li><i class="fas fa-check-circle"></i> Hybrid Architecture</li>
                        <li><i class="fas fa-check-circle"></i> Multi-Cloud Support</li>
                    </ul>
                </div>
            </div>

            <!-- Benefit 5: Why iBank23 -->
            <div class="benefit-card">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/05-Why-iBank23.webp') }}" alt="Why iBank23">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">05</span>
                    <h3>Why iBank23</h3>
                    <p>Experience the power of iBank23 with comprehensive banking solutions designed to streamline your financial operations.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> Digital Banking</li>
                        <li><i class="fas fa-check-circle"></i> Financial Automation</li>
                        <li><i class="fas fa-check-circle"></i> Seamless Integrations</li>
                        <li><i class="fas fa-check-circle"></i> Customer-Centric Design</li>
                    </ul>
                </div>
            </div>

            <!-- Benefit 6: Operational Control -->
            <div class="benefit-card reverse">
                <div class="benefit-img-wrap">
                    <div class="benefit-img-bg"></div>
                    <div class="benefit-image">
                        <img src="{{ asset('assets/images/landing/02-Operational-Control-Compliance.webp') }}" alt="Operational Control & Compliance">
                    </div>
                </div>
                <div class="benefit-content">
                    <span class="benefit-num">06</span>
                    <h3>Operational Control</h3>
                    <p>Maintain complete control over your operations with built-in compliance frameworks and real-time monitoring.</p>
                    <ul class="benefit-features">
                        <li><i class="fas fa-check-circle"></i> Real-time Monitoring</li>
                        <li><i class="fas fa-check-circle"></i> Compliance Automation</li>
                        <li><i class="fas fa-check-circle"></i> Audit Trails</li>
                        <li><i class="fas fa-check-circle"></i> Risk Management</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== FEATURES SECTION ===== -->
    <section id="features" class="section">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-th-large"></i> Features</span>
            <h2>All-in-One <span>CRM Platform</span></h2>
            <p>Everything you need to manage your entire business operation efficiently.</p>
        </div>
        <div class="feature-grid">
            <div class="card">
                <div class="icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Dashboard</h3>
                <p>Real-time insights with customizable widgets and KPI tracking.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-edit"></i></div>
                <h3>Editor</h3>
                <p>Create and manage content, emails, and templates effortlessly.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-database"></i></div>
                <h3>Data Collection</h3>
                <p>Centralized lead and customer data management with advanced filtering.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-bullhorn"></i></div>
                <h3>Digital Marketing</h3>
                <p>Email campaigns, social media management, and analytics.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <h3>Documents</h3>
                <p>Secure document storage, sharing, and e-signature support.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-handshake"></i></div>
                <h3>Consulting</h3>
                <p>Manage client consultations, appointments, and follow-ups.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-file-upload"></i></div>
                <h3>Application</h3>
                <p>Streamlined application processing with automated workflows.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
                <h3>Invoice</h3>
                <p>Generate, send, and track invoices with payment reminders.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-check-double"></i></div>
                <h3>VFS Checklist</h3>
                <p>Visa and document submission checklists for compliance.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-coins"></i></div>
                <h3>My Commissions</h3>
                <p>Track team commissions, bonuses, and performance incentives.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-calculator"></i></div>
                <h3>Accounting</h3>
                <p>Full accounting suite with expense tracking and financial reports.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-credit-card"></i></div>
                <h3>Payments</h3>
                <p>Secure payment processing with multiple gateway integrations.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-cogs"></i></div>
                <h3>Operations</h3>
                <p>Streamline daily workflows and team collaboration.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-percent"></i></div>
                <h3>Commissions</h3>
                <p>Automated commission calculations and payout management.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-user-shield"></i></div>
                <h3>Administration</h3>
                <p>Role-based access control and user management.</p>
            </div>
            <div class="card">
                <div class="icon"><i class="fas fa-sliders-h"></i></div>
                <h3>Settings</h3>
                <p>Customize your CRM with flexible configuration options.</p>
            </div>
        </div>
    </section>

    <!-- ===== CONTACT SECTION ===== -->
    <section id="contact" class="section">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-envelope"></i> Contact</span>
            <h2>Get In <span>Touch</span></h2>
            <p>Have questions? We'd love to hear from you. Reach out to us today.</p>
        </div>

        <div class="contact-container">
            <!-- Contact Form -->
            <div class="contact-form">
                <h3>Send Us <span>a Message</span></h3>
                <form>
                    <div class="form-group">
                        <input type="text" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Subject">
                    </div>
                    <div class="form-group">
                        <textarea placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>
    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h3><i class="fas fa-cube"></i> CRM System</h3>
                <p>Empowering businesses with intelligent CRM solutions to manage leads, sales, and customer support efficiently.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#why-crm">Why CRM</a></li>
                    <li><a href="#benefits">Benefits</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Us</h4>
                <p><i class="fas fa-envelope"></i> support@crmsystem.com</p>
                <p><i class="fas fa-phone"></i> +880 1234 567890</p>
                <p><i class="fas fa-map-marker-alt"></i> Dhaka, Bangladesh</p>
                <p><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 CRM System. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- ===== JAVASCRIPT FOR MOBILE MENU ===== -->
    <script>
        // Toggle mobile menu
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            const hamburger = document.getElementById('hamburger');
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
        }

        // Close menu when clicking a link (mobile)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navLinks').classList.remove('active');
                document.getElementById('hamburger').classList.remove('active');
            });
        });

        // Close menu when clicking outside (mobile)
        document.addEventListener('click', (e) => {
            const nav = document.getElementById('navbar');
            const hamburger = document.getElementById('hamburger');
            if (!nav.contains(e.target)) {
                document.getElementById('navLinks').classList.remove('active');
                hamburger.classList.remove('active');
            }
        });

        // Active link highlight on scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-links a:not(.login-btn-mobile)');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>