<?php
//check if webinars is not empty
if (!empty($webinars)) :
    foreach ($webinars as $webinar) :
        //get post thumbnail full
        $thumbnail = get_the_post_thumbnail_url($webinar->ID, 'full');
        //get taxonomy speakers
        $speakers = get_the_terms($webinar->ID, 'speakers');
        //scheduled_date
        $webinar_date = get_field("scheduled_date", $webinar->ID);
        //format date to March 23, 2022, NOON ET
        $formatted = date("F j, Y, g:i a", strtotime($webinar_date));
        //short_description
        $short_description = get_field("short_description", $webinar->ID);
        //permalink
        $permalink = get_permalink($webinar->ID);
?>
        <div class="custom-webinar-tau-item">
            <a href="<?php echo esc_url($permalink); ?>">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="">
                <h3 class="custom-webinar-title">
                    <?php echo esc_html($webinar->post_title); ?>
                </h3>
                <p class="custom-webinar-speaker">
                    Speaker:
                    <?php
                    //check if speakers is not empty
                    if (!empty($speakers)) :
                        foreach ($speakers as $speaker) :
                    ?>
                            <a href="<?php echo esc_url(get_term_link($speaker->term_id)); ?>">
                                <span>
                                    <?php echo esc_html($speaker->name); ?>
                                </span>
                            </a>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </p>
                <p class="custom-webinar-position">
                    <?php echo esc_html($short_description); ?>
                </p>
                <p class="custom-webinar-date">
                    <?php echo esc_html($formatted); ?>
                </p>
            </a>
        </div>
    <?php
    endforeach;
else :
    ?>
    <div class="custom-webinar-tau-item">
        <p>
            No Webinars Found
        </p>
    </div>
<?php
endif;
