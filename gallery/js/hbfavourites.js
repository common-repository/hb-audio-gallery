jQuery.fn.exists = function () {
  return jQuery(this).length > 0;
};

var $ = jQuery;
const userId = hb_object.userId;

const favouriteSong = function (id, audioPositionInList) {
  // non registered user
  if (userId == 0) {
    window.open(hb_object.loginUrl, "_blank");
    return;
  }

  const favIcon = jQuery("#audio-" + audioPositionInList);

  // object (svg)
  if(favIcon.is('object')){
    const favAddedIcon = hb_object.pluginUrl + "images/favourite-16.svg";
    const favRemovedIcon = hb_object.pluginUrl + "images/not-favourite_16.svg";

    favIcon.attr("data").includes("favourite-16.svg")
    ? favIcon.attr("data", favRemovedIcon)
    : favIcon.attr("data", favAddedIcon);
  }else{
    // img (png)
    const favAddedIcon = hb_object.pluginUrl + "images/favourite-16.png";
    const favRemovedIcon = hb_object.pluginUrl + "images/not-favourite_16.png";

    favIcon.attr("src").includes("favourite-16.png")
    ? favIcon.attr("src", favRemovedIcon)
    : favIcon.attr("src", favAddedIcon);
  }


  jQuery.ajax({
    url: hb_object.ajaxUrl,

    method: "POST",

    data: {
      action: "hb_favourites",
      audio_id: id,
    },
  });
};
