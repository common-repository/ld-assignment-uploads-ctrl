<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Available Variables:
 * $post                    : (object) Assignment post object
 * $allowed_file_types        : (string) Assignment upload error title
 * $allowed_file_size        : (string)  Assignment upload error message
 * $max_number_of_uploads : (string) back link to lesson/topic where upload was performed *
 */
?>
<table id="leardash_upload_assignment">
    <tr>
        <div class="ldauc-label"><?php echo __( 'Upload Assignment', 'ldauc' ) ?></div>
    </tr>
    <tr>
        <td>
            <div class="ldauc-values">
                <div class="ldauc-types"><?php echo $allowed_file_types ?></div>
                <div class="ldauc-size"><?php echo $allowed_file_size ?></div>
                <div class="ldauc-number"><?php echo $max_number_of_uploads ?></div>

                <form name="uploadfile" class="ldauc-form" id="uploadfile_form" method="POST"
                      enctype="multipart/form-data" action="" accept-charset="utf-8">
                    <input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles"/>
                    <input type="hidden" value="<?php echo $post -> ID ?>" name="post"/>
                    <input type="hidden" name="uploadfile"
                           value="<?php echo wp_create_nonce( 'uploadfile_' . get_current_user_id() . '_' . $post -> ID ) ?>"/>
                    <input class="button-primary" type="submit" id="uploadfile_btn"
                           value="<?php echo __( 'Upload', 'ldauc' ) ?>"/>
                </form>
            </div>
        </td>
    </tr>
</table>