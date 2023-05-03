//select media #select_media_webinar_thumbnail
let selectMediaWebinarThumbnail = (elem, e) => {
  jQuery(document).ready(function ($) {
    //prevent default action
    e.preventDefault();
    //open media image only
    let frame = wp.media({
      title: "Select or Upload Media",
      button: {
        text: "Select Media"
      },
      multiple: false,
      library: {
        type: "image"
      }
    });
    //select media
    frame.on("select", function () {
      //get media attachment
      let attachment = frame.state().get("selection").first().toJSON();
      //set media url to #webinar_thumbnail_url
      $("input[name='custom_webinar_thumbnail']").val(attachment.url);
    });

    //open frame
    frame.open();
  });
};
