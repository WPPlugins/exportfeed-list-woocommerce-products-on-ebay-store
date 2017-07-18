<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $ebcpf_feed_order, $ebcpf_feed_order_reverse;
require_once 'core/classes/dialogfeedsettings.php';
require_once 'core/data/savedfeed.php';

?>
    <div class="wrap">
        <?php ?>
        <h2>
        <?php
        _e( 'Manage Cart Product Feeds', 'ebcpf-exportfeed-ebay-strings' );
        $url = site_url() . '/wp-admin/admin.php?page=ebcpf-ebay-admin';
        echo '<input style="margin-top:12px;" type="button" class="add-new-h2" onclick="document.location=\'' . $url . '\';" value="' . __( 'Generate New Feed', 'ebcpf-exportfeed-ebay-strings' ) . '" />';
        ?>
        </h2>
        <?php ebcpf_print_info(); ?>


    <?php
    $message = NULL;
    // check for delete ID
    if ( isset( $_GET['action'] ) ) {
        $action = sanitize_text_field($_GET['action']);
        if ( $action == "delete" ) {
            if ( isset( $_GET['id'] ) ) {
                $delete_id = intval($_GET['id']);
                $message = ebcpf_delete_feed( $delete_id );
            }
        }
    }
    if ( $message ) {
        echo '<div id="setting-error-settings_updated" class="updated settings-error">
               <p>' . $message . '</p></div>';
    }
    //"New Feed" button
    $url = site_url() . '/wp-admin/admin.php?page=ebcpf-ebay-admin';
    ?>

    <br />
    <?php
	echo '
        <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {
           ajaxhost = "' . plugins_url( '/', __FILE__ ) . '";
        } );
        </script>';
        echo EBCPF_FeedSettingsDialogs::refreshTimeOutDialog();
    // The table of existing feeds
    ebcpf_main_table();
    ?>
    <br />
</div>
<?php

// The feeds table flat
function ebcpf_main_table() {

    global $wpdb;

    $feed_table = $wpdb->prefix . 'ebcpf_feeds';
		$providerList = new EBCPF_ProviderList();
    // Read the feeds
    $sql_feeds = ( "SELECT f.*,description FROM $feed_table as f LEFT JOIN $wpdb->term_taxonomy on ( f.category=term_id and taxonomy='product_cat'  ) ORDER BY f.id" );

    $list_of_feeds = $wpdb->get_results( $sql_feeds, ARRAY_A );
    // Find the ordering method
    $reverse = false;
    if ( isset( $_GET['order_by'] ) )
        $order = sanitize_text_field($_GET['order_by']);
    else
        $order = '';
    if ( $order == '' ) {
        $order = get_option( 'ebcpf_feed_order' );
        $reverse = get_option( 'ebcpf_feed_order_reverse' );
    } else {
        $old_order = get_option( 'ebcpf_feed_order' );
        $reverse = get_option( 'ebcpf_feed_order_reverse' );
        if ( $old_order == $order ) {
            $reverse = !$reverse;
        } else {
            $reverse = FALSE;
        }
        update_option( 'ebcpf_feed_order', $order );
        if ( $reverse )
            update_option( 'ebcpf_feed_order_reverse', TRUE );
        else
            update_option( 'ebcpf_feed_order_reverse', FALSE );
    }

    if ( ! empty( $list_of_feeds ) ) {

        // Setup the sequence array
        $seq = false;
        $num = false;
        foreach ( $list_of_feeds as $this_feed ) {
						$this_feed_ex = new EBCPF_SavedFeed($this_feed['id']);
            switch ( $order ) {
                case 'name':
                    $seq[] = strtolower( stripslashes( $this_feed['filename'] ) );
                    break;
                case 'description':
                    $seq[] = strtolower( stripslashes( $this_feed_ex->local_category ) );
                    break;
                case 'url':
                    $seq[] = strtolower( $this_feed['url'] );
                    break;
                case 'category':
                    $seq[] = $this_feed['category'];
                    $num = true;
                    break;
                case 'google_category':
                    $seq[] = $this_feed['remote_category'];
                    break;
                case 'type':
                    $seq[] = $this_feed['type'];
                    break;
                default:
                    $seq[] = $this_feed['id'];
                    $num = true;
                    break;
            }
        }

        // Sort the seq array
        if ( $num )
            asort( $seq, SORT_NUMERIC );
        else
            asort( $seq, SORT_REGULAR );

        // Reverse ?
        if ( $reverse ) {
            $t = $seq;
            $c = count( $t );
            $tmp = array_keys( $t );
            $seq = false;
            for ( $i = $c - 1; $i >= 0; $i-- ) {
                $seq[$tmp[$i]] = '0';
            }
        }

		$image['down_arrow'] = '<img src="' . esc_url(  plugins_url(  'images/down.png' , __FILE__  )  ) . '" alt="down" style=" height:12px; position:relative; top:2px; " />';
        $image['up_arrow'] = '<img src="' . esc_url(  plugins_url(  'images/up.png' , __FILE__  )  ) . '" alt="up" style=" height:12px; position:relative; top:2px; " />';
        ?>
        <!--	<div class="table_wrapper">	-->
        <table class="widefat" style="margin-top:12px;" >
            <thead>
                <tr>
                    <?php $url = get_admin_url() . 'admin.php?page=ebcpf-manage-page&amp;order_by='; ?>
                    <th scope="col" style="min-width: 40px;" >
                        <a href="<?php echo $url . "id" ?>">
                            <?php
                            _e( 'ID', 'ebcpf-exportfeed-ebay-strings' );
                             if ( $order == 'id' ) {
                                 if ( $reverse )
                                     echo $image['up_arrow'];
                                 else
                                     echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 120px;">
                        <a href="<?php echo $url . "name" ?>">
                            <?php
                            _e( 'Name', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'name' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col">
                        <a href="<?php echo $url . "category" ?>">
                            <?php
                            _e( 'Local category', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'category' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 100px;">
                        <a href="<?php echo $url . "google_category" ?>">
                            <?php
                            _e( 'Export category', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'google_category' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 50px;" >
                        <a href="<?php echo $url . "type" ?>">
                            <?php
                            _e( 'Type', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'type' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="width: 120px;">
                        <a href="<?php echo $url . "url" ?>">
                            <?php
                            _e( 'URL', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'url' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="width: 80px;"><?php _e( 'Last Updated', 'ebcpf-exportfeed-ebay-strings' ); ?></th>
					<th scope="col"><?php _e( 'Products', 'ebcpf-exportfeed-ebay-strings' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $alt = ' class="alternate" '; ?>

                <?php
                $idx = '0';
                foreach ( array_keys( $seq ) as $s ) {
                    $this_feed = $list_of_feeds[$s];
										$this_feed_ex = new EBCPF_SavedFeed($this_feed['id']);
                    $pendcount = FALSE;
                    ?>
                    <tr <?php
                    echo( $alt );
                    if ( $pendcount )
                        echo 'style="background-color:#ffdddd"'
                        ?>>
                        <td><?php echo $this_feed['id']; ?></td>
                        <td><?php echo $this_feed['filename']; ?>
                            <div class="row-actions"><span class="id">ID: <?php echo $this_feed['id'] ;?> | </span>
                            <span class="purple_xmlsedit">
                                 <a href="<?php echo $this_feed['url'] ?>" target="_blank" title="View this Feed" rel="permalink">View</a> | 
                            </span>
                            <?php $url_edit = get_admin_url() . 'admin.php?page=ebcpf-ebay-admin&action=edit&id=' . $this_feed['id'].'&feed_type='.$this_feed['feed_type']; ?>
                            <span class="purple_xmlsedit">
                                 <a href="<?php echo( $url_edit) ?>" target="_blank" title="Edit this Feed" rel="permalink">Edit</a> | 
                            </span>
                             <?php $url = get_admin_url() . 'admin.php?page=ebcpf-manage-page&action=delete&id=' . $this_feed['id']; ?>
                            <span class="delete">
                                 <a href="<?php echo( $url) ?>" title="Delete this Feed" >Delete</a> | 
                            </span>
                             <?php if($this_feed['type'] == "eBaySeller") : ?>
                                <script type="text/javascript">
                                </script>
                           <?php $upload_url =get_admin_url() . 'admin.php?page=ebcpf-ebay-settings-tabs&tab=managefeed&action=uploadFeed&id=' . $this_feed['id']; ?>
                            <span class="upload">
                                 <a href="<?php echo( $upload_url) ?>" title="Upload this Feed" rel="permalink">Upload </a> | 
                            </span>
                           
                        <?php endif;?>
                            </div>
                        </td>
                        <td><small><?php echo esc_attr( stripslashes( $this_feed_ex->local_category ) ) ?></small></td>
                        <td><?php echo str_replace( ".and.", " & ", str_replace( ".in.", " > ", esc_attr( stripslashes( $this_feed['remote_category'] ) ) ) ); ?></td>
                        <td><?php echo $providerList->getPrettyNameByType($this_feed['type']) ?></td>
                        <td><?php echo $this_feed['url'] ?></td>
                        <td><?php
														$ext = '.' . $providerList->getExtensionByType($this_feed['type']);
														$feed_file = EBCPF_FeedFolder::uploadFolder() . $this_feed['type'] . '/' . $this_feed['filename'] . $ext;
                            if ( file_exists( $feed_file ) ) {
                                echo date( "d-m-Y H:i:s", filemtime( $feed_file ) );
                            } else echo 'DNE';
                            ?></td>
						<td><?php echo $this_feed['product_count'] ?></td>

                    </tr>
                    <?php
                    if ( $alt == '' ) {
                        $alt = ' class="alternate" ';
                    } else {
                        $alt = '';
                    }

                    $idx++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php 
                    $url = get_admin_url() . 'admin.php?page=cart-product-manage-page&amp;order_by='; 
                    $order = '';
                    ?>
                    <th scope="col" style="min-width: 40px;" >
                        <a href="<?php echo $url . "id" ?>">
                            <?php
                            _e( 'ID', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'id' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 120px;">
                        <a href="<?php echo $url . "name" ?>">
                            <?php
                            _e( 'Name', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'name' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col">
                        <a href="<?php echo $url . "category" ?>">
                            <?php
                            _e( 'Local Category', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'category' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 100px;">
                        <a href="<?php echo $url . "google_category" ?>">
                            <?php
                            _e( 'Export category', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'google_category' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="min-width: 50px;" >
                        <a href="<?php echo $url . "type" ?>">
                            <?php
                            _e( 'Type', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'type' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="width: 120px;">
                        <a href="<?php echo $url . "url" ?>">
                            <?php
                            _e( 'URL', 'ebcpf-exportfeed-ebay-strings' );
                            if ( $order == 'url' ) {
                                if ( $reverse )
                                    echo $image['up_arrow'];
                                else
                                    echo $image['down_arrow'];
                            }
                            ?>
                        </a>
                    </th>
                    <th scope="col" style="width: 80px;"><?php _e( 'Last Updated', 'ebcpf-exportfeed-ebay-strings' ); ?></th>
					<th scope="col"><?php _e( 'Products', 'ebcpf-exportfeed-ebay-strings' ); ?></th>
                </tr>
            </tfoot>

        </table>

		<input class="button-primary" type="submit" value="Update Now" id="submit" name="submit" onclick="doUpdateAllFeeds()">
		<div id="update-message">&nbsp;</div>
        <?php
    } else {
        ?>
        <p><?php _e( 'No feeds yet!', 'ebcpf-exportfeed-ebay-strings' ); ?></p>
        <?php
    }
}

function ebcpf_delete_feed( $delete_id = NULL ) {
    // Delete a Feed
    global $wpdb;
    $feed_table = $wpdb->prefix . 'ebcpf_feeds';
    $sql_feeds = ( "SELECT * FROM $feed_table where id=$delete_id" );
    $list_of_feeds = $wpdb->get_results( $sql_feeds, ARRAY_A );

    if ( isset( $list_of_feeds[0] ) ) {
		$this_feed = $list_of_feeds[0];
		$ext = '.xml';
		if ( strpos( strtolower( $this_feed['url'] ), '.csv' ) > 0 ) {
		  $ext = '.csv';
		}
		$upload_dir = wp_upload_dir();
		$feed_file =  $upload_dir['basedir'] . '/ebay_export_feeds/' . $this_feed['type'] . '/' . $this_feed['filename'] . $ext;

        if( file_exists( $feed_file ) ) {
            unlink( $feed_file );
        }
        $wpdb->query( "DELETE FROM $feed_table where id=$delete_id" );
        return "Feed deleted successfully!";
    }
    return "Sorry! Feed is not deleted. Try Again";
}