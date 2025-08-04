<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="primary" class="site-main" role="main">
    <div class="content-area">
        <div class="main-content">
            <section class="error-404 not-found">
                <div class="error-content text-center">
                    <div class="error-number">
                        <span class="error-404-title">404</span>
                    </div>
                    
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'oltreblocksy'); ?></h1>
                    </header>

                    <div class="page-content">
                        <p class="mb-4"><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'oltreblocksy'); ?></p>
                        
                        <div class="error-actions">
                            <div class="search-section mb-4">
                                <?php get_search_form(); ?>
                            </div>
                            
                            <div class="action-buttons">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                                    <?php echo oltreblocksy_get_svg_icon('arrow-left', 20); ?>
                                    <?php esc_html_e('Back to Homepage', 'oltreblocksy'); ?>
                                </a>
                                
                                <button onclick="history.back()" class="btn btn-secondary btn-lg">
                                    <?php esc_html_e('Go Back', 'oltreblocksy'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Show recent posts if available
                    $recent_posts = wp_get_recent_posts(array(
                        'numberposts' => 3,
                        'post_status' => 'publish'
                    ));
                    
                    if (!empty($recent_posts)) :
                    ?>
                        <div class="recent-posts-section mt-5">
                            <h2 class="section-title"><?php esc_html_e('Recent Posts', 'oltreblocksy'); ?></h2>
                            <div class="recent-posts-grid">
                                <?php foreach ($recent_posts as $post) : ?>
                                    <div class="recent-post-item card">
                                        <?php if (has_post_thumbnail($post['ID'])) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php echo esc_url(get_permalink($post['ID'])); ?>">
                                                    <?php echo get_the_post_thumbnail($post['ID'], 'thumbnail', array('loading' => 'lazy')); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="card-body">
                                            <h3 class="card-title">
                                                <a href="<?php echo esc_url(get_permalink($post['ID'])); ?>">
                                                    <?php echo esc_html($post['post_title']); ?>
                                                </a>
                                            </h3>
                                            
                                            <div class="post-meta text-muted">
                                                <time datetime="<?php echo esc_attr(get_the_date('c', $post['ID'])); ?>">
                                                    <?php echo esc_html(get_the_date('', $post['ID'])); ?>
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Show popular categories if available
                    $popular_categories = get_categories(array(
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'number' => 6,
                        'hide_empty' => true
                    ));
                    
                    if (!empty($popular_categories)) :
                    ?>
                        <div class="popular-categories-section mt-5">
                            <h2 class="section-title"><?php esc_html_e('Popular Categories', 'oltreblocksy'); ?></h2>
                            <div class="categories-list">
                                <?php foreach ($popular_categories as $category) : ?>
                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="badge badge-secondary">
                                        <?php echo esc_html($category->name); ?>
                                        <span class="count">(<?php echo esc_html($category->count); ?>)</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<style>
.error-404 {
    padding: 3rem 0;
    min-height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-content {
    max-width: 600px;
    margin: 0 auto;
}

.error-404-title {
    font-size: clamp(4rem, 8vw, 8rem);
    font-weight: 900;
    color: var(--wp--preset--color--blue-600);
    opacity: 0.1;
    display: block;
    line-height: 1;
}

.error-404 .page-title {
    font-size: var(--font-size-2xl);
    margin-bottom: 1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.recent-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.recent-post-item .post-thumbnail img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.categories-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    margin-top: 1rem;
}

.categories-list .badge {
    text-decoration: none;
    transition: all var(--transition-fast);
}

.categories-list .badge:hover {
    transform: translateY(-1px);
}

.count {
    opacity: 0.7;
    font-weight: normal;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .recent-posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>