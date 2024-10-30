<div class="hbw_container">

    <script>

    jQuery(document).ready(function(){



        jQuery.noConflict();

        

        jQuery("#jquery_jplayer_s<?=$args['aid']?>").jPlayer({

            ready: function () {

                jQuery(this).jPlayer("setMedia", {

                    mp3:"<?=$args['audio']['audioURL']?>"

                })

        <?php

        if ($args['autoplay'] == "yes"):

        ?>

            jPlayer("play");

        <?php

        else:

        ?>

            ;

        <?php

        endif;

        ?>

            },

            swfPath: "http://www.jplayer.org/latest/js/Jplayer.swf",

            supplied: "mp3",

            wmode: "window",

            smoothPlayBar: true,

            keyEnabled: true,

            cssSelectorAncestor: "#jp_container_s<?=$args['aid']?>",

        });

    });

</script>

<?php

if($args['view'] == 'list'):                           

?>

    <div id="jquery_jplayer_s<?=$args['aid']?>" class="jp-jplayer"></div>

    <div id="jp_container_s<?=$args['aid']?>" class="jp-audio hbag-single-list hbag-single">

        <!-- <div class="jp-type-single"> -->

            <div class="jp-gui jp-interface" style="background-color: <?=$args['theme_color']?> !important">

                <div class="hbgallery_image image">
                    <img src="<?=$args['audio']['picture'] ? $args['audio']['picture'] : (has_post_thumbnail($args['audio']['gid']) ? get_the_post_thumbnail_url($args['audio']['gid']) : HBAG_URLPATH . 'images/default.png')?>" class="hbag-image" />
                </div>

                <div class="play-section">

                    <ul class="jp-controls ">

                        <li class="jp-play" onClick="clickPlayButton('single', <?=$args['aid']?>)"><a href="javascript:;" tabindex="1"><object data="<?=HBAG_URLPATH?>images/play.svg"></object></a></li>

                        <li class="jp-pause"><a href="javascript:;" tabindex="1"><object data="<?=HBAG_URLPATH?>images/pause.svg"></object></a></li>

                        <!-- <li class="jp-stop"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/stop.svg"></a></li> -->

                        <div class="hbag-vol-bar">

                         <li class="jp-mute"><a href="javascript:;" tabindex="1"><object data="<?=HBAG_URLPATH?>images/unmute.svg"></object></a></li>

                         <li class="jp-unmute"><a href="javascript:;" tabindex="1"><object data="<?=HBAG_URLPATH?>images/mute.svg"></object></a></li>

                         <li class="hb-volume_bar"><div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div></li>

                         <li class="jp-volume-max"><a href="javascript:;" tabindex="1"><object data="<?=HBAG_URLPATH?>images/maxvol.svg"></object></a></li>

                        </div>

                    </ul>

                    <div class="jp-progress">

                        <div class="jp-seek-bar">

                            <div class="jp-play-bar"></div>

                        </div>

                    </div>

                    <div class="jp-time-holder jp-time-holder-grid-slider">

                        <div class="jp-current-time"></div>

                        <div class="jp-duration"></div>

                        <ul class="jp-toggles">

                            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>

                            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>

                        </ul>

                    </div>

                    <div class="jp-title play-list">

                        <p><?=$args['audio']['title']?></p>

                        <p class="hbgallery_artist"><?=esc_html(get_post_meta($args['audio']['gid'], 'gallery_author', true))?></p>

                        <!-- </div> -->

                    </div>

                </div>

            </div>

            <div class="jp-no-solution">

                <span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.

            </div>

        <!-- </div> -->

    </div>

<?php

elseif ($args['view'] == 'grid'):

?>

    <div class="single-player">                

        <div id="jquery_jplayer_s<?=$args['aid']?>" class="jp-jplayer"></div>

        <div id="jp_container_s<?=$args['aid']?>" class="jp-audio hbag-single">

            <!-- <div class="jp-type-single"> -->

            <div class="jp-gui jp-interface">

                <div class="hbag-image-center">

                    <img src=<?=$args['audio']['picture']?> class="hbag-image">

                </div>

                <ul class="jp-controls">

                    <li class="jp-play"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/play.svg"></a></li>

                    <li class="jp-pause"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/pause.svg"></a></li>

                    <!-- <li class="jp-stop"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/stop.png"></a></li> -->

                    <div class="hbag-vol-bar">

                     <li class="jp-mute"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/unmute.svg"></a></li>

                     <li class="jp-unmute"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/mute.svg"></a></li>

                     <li class="hb-volume_bar"><div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div></li>

                     <li class="jp-volume-max"><a href="javascript:;" tabindex="1"><img src="<?=HBAG_URLPATH?>images/maxvol.svg"></a></li>

                    </div>
                </ul>

                <div class="jp-progress">

                    <div class="jp-seek-bar">

                        <div class="jp-play-bar"></div>

                    </div>

                </div>

                <div class="jp-time-holder jp-time-holder-grid-slider">

                    <div class="jp-current-time"></div>

                    <div class="jp-duration"></div>

                    <ul class="jp-toggles">

                        <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>

                        <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>

                    </ul>

                </div>

                <div class="jp-title">

                    <p><?=esc_html($args['audio']['title'])?></p> 

                    <p class="hbgallery_artist"><?=esc_html(get_post_meta($args['audio']['gid'], 'gallery_author', true))?></p>

                    <!-- </div> -->

                </div>

            </div>
        </div>

            <div class="jp-no-solution">

                <span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.

            </div>

        </div>
    

<?php

endif;

?>

</div>