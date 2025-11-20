<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'VERCUL Business - €500 Bonus Offer';
include __DIR__ . '/../templates/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Get Your VERCUL Business Account</h1>
            <h2>Plus €500 Welcome Bonus</h2>
            <p class="hero-subtitle">Join thousands of European businesses banking with VERCUL</p>
            <div class="hero-buttons">
                <a href="apply.php" class="btn btn-primary btn-lg">Apply Now</a>
                <a href="track.php" class="btn btn-secondary btn-lg">Track Application</a>
            </div>
        </div>
    </div>
</section>

<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title">Why Choose VERCUL Business?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Multi-Currency Accounts</h3>
                <p>Hold and manage multiple currencies with competitive exchange rates</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Instant Transfers</h3>
                <p>Lightning-fast domestic and international payments</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-Time Analytics</h3>
                <p>Track expenses and manage cash flow with powerful insights</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Bank-Grade Security</h3>
                <p>Your funds are protected with advanced security measures</p>
            </div>
        </div>
    </div>
</section>

<section id="process" class="process-section">
    <div class="container">
        <h2 class="section-title">Simple Application Process</h2>
        <div class="process-steps">
            <div class="process-step">
                <div class="step-number">1</div>
                <h3>Submit Application</h3>
                <p>Fill out the online form with your business details</p>
            </div>
            <div class="process-step">
                <div class="step-number">2</div>
                <h3>Document Review</h3>
                <p>Our team reviews your application within 24-48 hours</p>
            </div>
            <div class="process-step">
                <div class="step-number">3</div>
                <h3>Get Approved</h3>
                <p>Receive your account details and €500 bonus</p>
            </div>
        </div>
    </div>
</section>

<section id="testimonials" class="testimonials-section">
    <div class="container">
        <h2 class="section-title">What Our Clients Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"VERCUL has transformed how we manage our international payments. The platform is intuitive and the support is exceptional."</p>
                <p class="testimonial-author">- Sarah M., Tech Startup CEO</p>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"Best business banking solution we've used. The multi-currency feature alone has saved us thousands in fees."</p>
                <p class="testimonial-author">- James K., Import/Export Director</p>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="faq-section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3>How long does approval take?</h3>
                <p>Most applications are reviewed within 24-48 hours. Complex cases may take up to 5 business days.</p>
            </div>
            <div class="faq-item">
                <h3>What documents do I need?</h3>
                <p>You'll need business registration documents, proof of address, and identification for company directors.</p>
            </div>
            <div class="faq-item">
                <h3>Is there a monthly fee?</h3>
                <p>VERCUL Business accounts have no monthly fees for the first year. After that, it's just €9.99/month.</p>
            </div>
            <div class="faq-item">
                <h3>Which countries are eligible?</h3>
                <p>We currently serve businesses registered in the UK, Ireland, France, Germany, Spain, Italy, Netherlands, and Belgium.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of businesses already banking with VERCUL</p>
        <a href="apply.php" class="btn btn-primary btn-lg">Apply for Free Account</a>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>