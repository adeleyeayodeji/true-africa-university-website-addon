<?php
//security
if (!defined('ABSPATH')) {
    die("You are not allowed to call this page directly.");
}
?>
<div>
    <div>
        <div class="custom-webinar-action">
            <label for="webinar_thumbnail" class="custom_webinar_label">Webinar thumnail</label>
            <p style="margin: 0px;">
                <a href="javascript:;" class="button" id="select_media_webinar_thumbnail" onclick="selectMediaWebinarThumbnail(this,event)">Select Media</a>
            </p>
        </div>
        <input type="text" name="custom_webinar_thumbnail" id="webinar_thumbnail" value="<?php echo esc_attr(get_post_meta($post->ID, 'custom_webinar_thumbnail', true)); ?>" placeholder="Enter webinar thumbnail" style="width: 100%;    margin-bottom: 10px;">
    </div>
</div>