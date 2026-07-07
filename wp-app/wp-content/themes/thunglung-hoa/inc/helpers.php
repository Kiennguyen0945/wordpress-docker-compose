<?php
/**
 * Helper Functions
 *
 * SVG icons, UI utilities, and small template helpers.
 * @package ThungLungHoa
 * @since 1.0.0
 */

/**
 * Get cart item count for header icon badge.
 */
function tlh_cart_count() {
    if (class_exists('WooCommerce')) {
        return WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    }
    return 0;
}

/**
 * SVG logo mark (5-petal flower).
 */
function tlh_logo_svg() {
    return '<svg class="logo-mark" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
        <g transform="translate(20,20)">
            <g class="fill-primary" opacity="0.92">
                <path transform="rotate(0)"   d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(72)"  d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(144)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(216)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
                <path transform="rotate(288)" d="M0,0 C-6,-5 -6,-15 0,-19 C6,-15 6,-5 0,0 Z"/>
            </g>
            <circle r="4.2" class="fill-accent"/>
        </g>
    </svg>';
}

/**
 * Search icon SVG.
 */
function tlh_search_icon() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>';
}

/**
 * Cart icon SVG.
 */
function tlh_cart_icon() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21.5 9H6.5"/><circle cx="10" cy="21.5" r="1"/><circle cx="18" cy="21.5" r="1"/></svg>';
}

/**
 * Wave divider SVG.
 */
function tlh_wave_divider() {
    return '<svg class="wave-divider" viewBox="0 0 1200 44" preserveAspectRatio="none"><path d="M0,22 C150,44 350,0 600,20 C850,40 1050,4 1200,22 L1200,44 L0,44 Z"/></svg>';
}
