function togglePlaylistMenu(playlist) {
  jQuery("#hbag-list-toggle-btn").toggleClass("hbag-list-open hbag-list-close");
  
  jQuery(`#${playlist}`).slideToggle();
}


function disableButton(self) {}
function temprorayDisableAudioClick() {}