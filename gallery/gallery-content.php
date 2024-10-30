<?php
require_once 'gallery-function.php';

function wp_get_gallery_list_content($audioList, $audio_columns, $hidden_columns)
{

    $return_str = '';
    $counter    = 0;

    if (is_array($audioList) && !empty($audioList)) {
        foreach ($audioList as $audio) {
            if (empty($audio["picture"])) {
                $audio["picture"] = HBAG_URLPATH .'/images/default.png';
            }
            $counter++;
            $aid       = (int) $audio['aid'];

            $return_str .= '<tr id="audio-' . $aid . '" class="iedit"  valign="top">';
            foreach ($audio_columns as $audio_column_key) {
                $audio_column_key = strtolower($audio_column_key);
                $class = "class='$audio_column_key column-$audio_column_key'";

                $style = '';
                if (in_array($audio_column_key, $hidden_columns))
                    $style = ' style="display:none;"';

                $attributes = $class . $style;

                switch ($audio_column_key) {
                    case 'cb':
                        $attributes = 'class="column-cb check-column"' . $style;
                        $return_str .= '<th ' . $attributes . ' scope="row"><input name="doaction[]" type="checkbox" value="' . $aid . '" /></th>';
                        break;
                    case 'id':
                        $return_str .= '<td ' . $attributes . ' style="">' . $aid;
                        $return_str .= '<input type="hidden" name="aid[]" value="' . $aid . '" />';
                        $return_str .= '</td>';
                        break;
                    case 'filename':
                        $attributes = 'class="title column-filename column-title"' . $style;
                        $return_str .= '<td ' . $attributes . '>';
                        $return_str .= '<strong><a href="' . esc_url($audio['audioURL']) . '" class="thickbox" title="' . esc_attr($audio['filename']) . '">';
                        $return_str .= esc_attr($audio['filename']);
                        $return_str .= '</a></strong>';
                        $return_str .= '</td>';
                        break;
                    case 'title':
                        $return_str .= '<td ' . $attributes . '>';
                        $return_str .= '<input name="title[' . $aid . ']" type="text" style="width:95%; margin-bottom: 2px;" value="' . stripslashes($audio['title']) . '" />';
                        $return_str .= '</td>';
                        break;
                    case 'artist':
                        $return_str .= '<td ' . $attributes . '>';
                        $return_str .= '<input name="artist[' . $aid . ']" type="text" style="width:95%; margin-bottom: 2px;" value="' . stripslashes($audio['artist']) . '" />';
                        $return_str .= '</td>';
                        break;
                    case 'orders':
                        if ($audio['orders'] == 0) {
                            $return_str .= '<td ' . $attributes . '>';
                            $return_str .= '<input name="orders[' . $aid . ']" type="text" style="width:40%; margin-bottom: 2px;" value="" />';
                            $return_str .= '</td>';
                        } else {
                            $return_str .= '<td ' . $attributes . '>';
                            $return_str .= '<input name="orders[' . $aid . ']" type="text" style="width:40%; margin-bottom: 2px;" value="' . stripslashes($audio['orders']) . '" />';
                            $return_str .= '</td>';
                        }
                        break;  //  On upload order blank 	
                    case 'picture':
                        $return_str .= '<td $attributes >
                                        <div class="img-relative">
                                            <form method="post" action="' . HBAG_URLPATH . '/lib/upload_picture.php" enctype="multipart/form-data" id="picUploadForm' . $aid . '" target="uploadTarget">
                                                <input type="file" name="picture" id="fileInput' . $aid . '" style="display:none" />
                                                <input type="hidden" name="audio_id" value="' . $aid . '">
                                            </form>
                                            <iframe id="uploadTarget" name="uploadTarget" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
                                            <!-- Image update link -->

                                            <a class="editLink" onclick="clickeditem(this)" id=' . $aid . ' href="javascript:void(0);"><img class="edit-icon" src="' . HBAG_URLPATH . '/images/edit.svg" />
                                                <!-- Profile image -->
                                                <img src="' . esc_attr($audio["picture"]) . '" class="thumbnail" id="imagePreview' . $aid . '"></a>
                                        </div>
                                    </td>';
                        break;
                    default:
                        $return_str .= '<td ' . $attributes  . '>' . do_action('ngg_manage_image_custom_column', $audio_column_key, $aid) . '</td>';
                        break;
                }
            }
            $return_str .= '</tr>';
        }
    }

    return $return_str;
}
