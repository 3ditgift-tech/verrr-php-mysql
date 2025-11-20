<?php
require_once __DIR__ . '/../includes/config.php';

// Check database connection before showing page
requireDatabase();

$pageTitle = __('site_name') . ' | ' . __('hero_subtitle');
include __DIR__ . '/../templates/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo __('hero_title'); ?></h1>
            <h2><?php echo __('hero_subtitle'); ?></h2>
            <p class="hero-subtitle"><?php echo __('hero_description'); ?></p>
            <div class="hero-buttons">
                <a href="apply.php" class="btn btn-primary btn-lg"><?php echo __('apply_now'); ?></a>
                <a href="track.php" class="btn btn-secondary btn-lg"><?php echo __('track_application'); ?></a>
            </div>
        </div>
    </div>
</section>

<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title"><?php echo __('why_choose'); ?></h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3><?php echo __('multi_currency'); ?></h3>
                <p><?php echo __('multi_currency_desc'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3><?php echo __('instant_transfers'); ?></h3>
                <p><?php echo __('instant_transfers_desc'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3><?php echo __('real_time_analytics'); ?></h3>
                <p><?php echo __('real_time_analytics_desc'); ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3><?php echo __('bank_security'); ?></h3>
                <p><?php echo __('bank_security_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<section id="process" class="process-section">
    <div class="container">
        <h2 class="section-title"><?php echo __('application_process'); ?></h2>
        <div class="process-steps">
            <div class="process-step">
                <div class="step-number">1</div>
                <h3><?php echo __('step_1_title'); ?></h3>
                <p><?php echo __('step_1_desc'); ?></p>
            </div>
            <div class="process-step">
                <div class="step-number">2</div>
                <h3><?php echo __('step_2_title'); ?></h3>
                <p><?php echo __('step_2_desc'); ?></p>
            </div>
            <div class="process-step">
                <div class="step-number">3</div>
                <h3><?php echo __('step_3_title'); ?></h3>
                <p><?php echo __('step_3_desc'); ?></p>
            </div>
        </div>
    </div>
</section>

<section id="testimonials" class="testimonials-section">
    <div class="container">
        <h2 class="section-title"><?php echo __('what_clients_say'); ?></h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"<?php echo __('testimonial_1'); ?>"</p>
                <p class="testimonial-author">- <?php echo __('testimonial_1_author'); ?></p>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"<?php echo __('testimonial_2'); ?>"</p>
                <p class="testimonial-author">- <?php echo __('testimonial_2_author'); ?></p>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="faq-section">
    <div class="container">
        <h2 class="section-title"><?php echo __('faq_title'); ?></h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3><?php echo __('faq_q1'); ?></h3>
                <p><?php echo __('faq_a1'); ?></p>
            </div>
            <div class="faq-item">
                <h3><?php echo __('faq_q2'); ?></h3>
                <p><?php echo __('faq_a2'); ?></p>
            </div>
            <div class="faq-item">
                <h3><?php echo __('faq_q3'); ?></h3>
                <p><?php echo __('faq_a3'); ?></p>
            </div>
            <div class="faq-item">
                <h3><?php echo __('faq_q4'); ?></h3>
                <p><?php echo __('faq_a4'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2><?php echo __('ready_to_start'); ?></h2>
        <p><?php echo __('join_thousands'); ?></p>
        <a href="apply.php" class="btn btn-primary btn-lg"><?php echo __('apply_free_account'); ?></a>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>