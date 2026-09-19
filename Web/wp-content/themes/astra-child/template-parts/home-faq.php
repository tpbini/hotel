<?php
/**
 * The Cochin - Homepage FAQ Section
 *
 * Implements the Frequently Asked Questions accordion section:
 * - Background: bottombg.jpg artwork at the bottom over #F5EFE3
 * - Font: Poppins (Questions, Answers), Architects Daughter (Badge)
 * - Smooth interactive accordion with accessibility support
 */

if (!defined('ABSPATH')) {
    exit;
}

$faq = the_cochin_get_faq_data();

if (function_exists('get_field')) {
    $acf_badge = get_field('faq_badge');
    if (!empty($acf_badge)) {
        $faq['badge'] = $acf_badge;
    }
    $acf_title = get_field('faq_title');
    if (!empty($acf_title)) {
        $faq['title'] = $acf_title;
    }
    $acf_art = get_field('faq_bottom_art');
    if (!empty($acf_art)) {
        $faq['bottom_bg'] = $acf_art;
    }

    $acf_items = array();
    for ($i = 1; $i <= 8; $i++) {
        $q = get_field('faq_' . $i . '_question');
        $a = get_field('faq_' . $i . '_answer');
        if (!empty(trim($q ?? ''))) {
            $acf_items[] = array(
                'question' => trim($q),
                'answer'   => trim($a ?? ''),
            );
        }
    }
    if (!empty($acf_items)) {
        $faq['items'] = $acf_items;
    }
}

if (!empty($args['badge'])) {
    $faq['badge'] = $args['badge'];
}
if (!empty($args['title'])) {
    $faq['title'] = $args['title'];
}
if (!empty($args['items']) && is_array($args['items'])) {
    $faq['items'] = $args['items'];
}
?>

<section class="cochin-faq-section" id="faq">
    <div class="cochin-faq-container">
        <!-- Section Header -->
        <div class="cochin-faq-header">
            <div class="cochin-faq-badge">
                <span class="cochin-badge-line" aria-hidden="true"></span>
                <span class="cochin-badge-text"><?php echo esc_html($faq['badge']); ?></span>
                <span class="cochin-badge-line" aria-hidden="true"></span>
            </div>
            <h2 class="cochin-faq-title"><?php echo esc_html($faq['title']); ?></h2>
        </div>

        <!-- FAQ Accordion List -->
        <div class="cochin-faq-accordion" id="cochinFaqAccordion">
            <?php foreach ($faq['items'] as $index => $item) : 
                $is_open = ($index === 0);
                $item_id = 'faq-item-' . ($index + 1);
                $content_id = 'faq-content-' . ($index + 1);
            ?>
                <div class="cochin-faq-card <?php echo $is_open ? 'is-active' : ''; ?>" id="<?php echo esc_attr($item_id); ?>">
                    <button class="cochin-faq-question" type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($content_id); ?>">
                        <span class="cochin-question-text"><?php echo esc_html($item['question']); ?></span>
                        <span class="cochin-faq-toggle-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19" class="toggle-line-v"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </span>
                    </button>
                    <div class="cochin-faq-answer-wrap" id="<?php echo esc_attr($content_id); ?>" role="region" aria-labelledby="<?php echo esc_attr($item_id); ?>" <?php echo $is_open ? '' : 'style="max-height: 0; opacity: 0;"'; ?>>
                        <div class="cochin-faq-answer">
                            <p><?php echo nl2br(esc_html($item['answer'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Kerala Line Art Illustration at Bottom -->
    <div class="cochin-faq-bottom-art" aria-hidden="true">
        <img src="<?php echo esc_url($faq['bottom_bg']); ?>" alt="" class="cochin-faq-bottom-img" loading="lazy">
    </div>
</section>
