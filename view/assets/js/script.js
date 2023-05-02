let customWebinars = (elem, e) => {
  jQuery(document).ready(function ($) {
    //prevent default action
    e.preventDefault();
    //get slug
    let slug = $(elem).attr("data-tag");
    //limit
    let limit = $(elem).attr("data-limit");
    //ajax
    $.ajax({
      type: "GET",
      url: customwebinars_props.ajaxurl,
      data: {
        action: "custom-webinars-tau-get-tag",
        nonce: customwebinars_props.nonce,
        tag: slug,
        limit: limit
      },
      beforeSend: function () {
        //block .custom-webinar-tau-container
        $(".custom-webinar-tau-container").block({
          message: "<i class='fa fa-spinner fa-spin'></i> Loading...",
          overlayCSS: {
            background: "#fff",
            opacity: 0.6,
            cursor: "wait"
          },
          css: {
            border: 0,
            padding: 0,
            backgroundColor: "transparent"
          }
        });
      },
      dataType: "json",
      success: function (response) {
        //unblock .custom-webinar-tau-container
        $(".custom-webinar-tau-container").unblock();
        //check response code 200
        if (response.code == 200) {
          //append response
          $(".custom-webinar-tau-container").html(response.html);
          //remove all active .custom-webinar-link
          $(".custom-webinar-link").each(function (index, element) {
            $(element).removeClass("active");
          });
          //add active class to clicked element
          $(elem).addClass("active");
        } else {
          //izitoast error
          iziToast.error({
            title: "Error",
            message: response.message,
            position: "topRight"
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //unblock .custom-webinar-tau-container
        $(".custom-webinar-tau-container").unblock();
        //izitoast error
        iziToast.error({
          title: "Error",
          message: "Something went wrong, please try again later.",
          position: "topRight"
        });
      }
    });
  });
};
