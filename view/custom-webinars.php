<?php
//security
if (!defined('ABSPATH')) {
    die("You are not allowed to call this page directly.");
}
?>
<div class="custom-webinar-tau">
    <div class="custom-webinar-nav">
        <div class="custom-webinar-title-nav">
            <h2>
                <?php echo esc_html($title); ?>
            </h2>
        </div>
        <div class="custom-webinar-links">
            <ul>
                <?php
                //check if tags is not empty
                if (!empty($tags)) :
                    foreach ($tags as $tag) :
                ?>
                        <li>
                            <a href="javascript:;" data-limit="<?php echo esc_html($limit); ?>" data-tag="<?php echo esc_attr($tag->slug); ?>" onclick="customWebinars(this,event)" class="custom-webinar-link <?php echo in_array($tag->slug, $filter_years) ? 'active' : ''; ?>">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        </li>
                <?php
                    endforeach;
                endif;
                ?>
            </ul>
        </div>
    </div>
    <div class="custom-webinar-tau-container">
        <?php
        $CustomWebinarsTAUshortcode = new CustomWebinarsTAUshortcode();
        echo $CustomWebinarsTAUshortcode->get_webinar_item($webinars);
        ?>
    </div>
</div>