<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Available Variables:
 * $title        : (string) Assignment upload error title
 * $message        : (string)  Assignment upload error message
 * $back_link : (string) back link to lesson/topic where the upload was performed *
 */
get_header();
?>
    <div class="ldauc wrap">
        <div id="primary" class="site-content page-full-width">
            <div id="content" role="main">

                <article id="ldauc-message" class="ldauc-message">
                    <header class="header">
                        <h2 class="title">
                            <span><?php echo $title; ?></span>
                        </h2>
                    </header>

                    <div class="entry-content">
                        <p class="message-p">    <?php echo $message; ?> !</p>
						<?php echo $back_link; ?>

                    </div><!-- .entry-content -->
                </article><!-- #ldauc-message -->

            </div><!-- #content -->
        </div><!-- #primary -->
    </div><!-- .wrap -->
<?php
get_footer();
die();