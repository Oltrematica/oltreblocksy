<?php
/**
 * Search form template
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

$unique_id = oltreblocksy_generate_id('search');
$search_text = __('Search', 'oltreblocksy');
$placeholder = __('Search...', 'oltreblocksy');
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="form-group">
        <label for="<?php echo esc_attr($unique_id); ?>" class="sr-only"><?php echo esc_html($search_text); ?></label>
        <div class="search-input-group">
            <input 
                type="search" 
                id="<?php echo esc_attr($unique_id); ?>" 
                class="form-control search-field" 
                placeholder="<?php echo esc_attr($placeholder); ?>" 
                value="<?php echo get_search_query(); ?>" 
                name="s" 
                required
                autocomplete="off"
                aria-describedby="<?php echo esc_attr($unique_id); ?>-description"
            >
            <button type="submit" class="btn btn-primary search-submit" aria-label="<?php echo esc_attr($search_text); ?>">
                <?php echo oltreblocksy_get_svg_icon('search', 20); ?>
                <span class="sr-only"><?php echo esc_html($search_text); ?></span>
            </button>
        </div>
        <div id="<?php echo esc_attr($unique_id); ?>-description" class="sr-only">
            <?php esc_html_e('Press Enter to search', 'oltreblocksy'); ?>
        </div>
    </div>
</form>