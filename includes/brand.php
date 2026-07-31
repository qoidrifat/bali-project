<?php

if (!function_exists('brand_mark_svg')) {
    function brand_mark_svg($class = 'brand-mark__svg')
    {
        $class = htmlspecialchars((string) $class, ENT_QUOTES, 'UTF-8');

        return '<svg class="' . $class . '" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">' .
            '<circle class="brand-mark__sun" cx="14.6" cy="14.4" r="4.1"/>' .
            '<path class="brand-mark__route" d="M31.4 7.2C25.3 12.7 24.8 21.2 31.4 33.7C38 21.2 37.5 12.7 31.4 7.2Z"/>' .
            '<circle class="brand-mark__pin" cx="31.4" cy="17.6" r="2.6"/>' .
            '<path class="brand-mark__ridge" d="M8.8 27.6C13.4 20.9 18.8 20.9 23.6 27.6C27.8 22.5 32.4 22.3 37.9 27.6"/>' .
            '<path class="brand-mark__wave" d="M8.2 34.5C14.6 30.4 19.9 38.2 26.1 34.3C31.3 31 35.5 35.1 39.8 32.1"/>' .
            '</svg>';
    }
}

if (!function_exists('brand_mark')) {
    function brand_mark($class = 'nav__brand-mark')
    {
        $classes = trim((string) $class . ' brand-mark');
        $classes = htmlspecialchars($classes, ENT_QUOTES, 'UTF-8');

        return '<span class="' . $classes . '" aria-hidden="true">' . brand_mark_svg() . '</span>';
    }
}

if (!function_exists('brand_wordmark')) {
    function brand_wordmark($accentColor = 'var(--brand-500)')
    {
        $accentColor = htmlspecialchars((string) $accentColor, ENT_QUOTES, 'UTF-8');

        return 'Bali<span style="color:' . $accentColor . '">Paradise</span>';
    }
}
