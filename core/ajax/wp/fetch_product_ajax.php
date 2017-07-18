<?php
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//if(!is_admin()){
//    die('Permission Denied!');
//}
require_once dirname(__FILE__) . '/../../../../../../wp-load.php';
define( 'XMLRPC_REQUEST', true );

//$check = wp_verify_nonce(sanitize_text_field($_POST['security']),'exportfeed_cpf');
//if (!$check)
//    return;
ob_start();
//********************************************************************
//Load the products
//********************************************************************
global $wpdb;

$cmd = sanitize_text_field( $_POST['cmd'] );
if ( $cmd == 'ajax' ) {
    $keywords   = isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '';
    $filterTerm = isset( $_POST['searchfilters'] ) ? sanitize_text_field( $_POST['searchfilters'] ) : '';
    if ( $filterTerm == 'sku' ) {
        $where = "meta_key = '_sku' AND meta_value LIKE '{$keywords}%'";
        $sql   = "SELECT meta_id as meta_id ,post_id as id,  meta_value as title
                         FROM {$wpdb->prefix}postmeta
                         where {$where}
                 ";
    }

    if ( $filterTerm == 'all' ) {
        $where = "post_title like '{$keywords}%' AND post_type='product'";
        $sql   = "SELECT ID as id ,post_title as title
                         FROM {$wpdb->prefix}posts
                         where {$where}
                    ";
    }
    $result = $wpdb->get_results( $sql, ARRAY_A ); ?>
    <ul id="filters_results">
        <?php
        if ( count( $result ) > 0 ) {
            foreach ( $result as $data => $product ) { ?>
                <li onclick="selectFilters('<?php echo $product['title']; ?>');"><?php echo $product['title']; ?></li>
                <input type="hidden" value="<?php echo $product['id']; ?>" name="cpf-hidden-id"/>
            <?php } ?>
        <?php } else { ?>
            <li><span class="no-search-results">No Record found</span></li>
        <?php } ?>
    </ul>
<?php } ?>

<?php
if ( $cmd == 'search' ) {
    print_r($_POST);
    $merchat_type = 'eBaySeller';
    $keywords     = isset( $_POST['keywords'] ) ? sanitize_text_field( $_POST['keywords'] ) : "";
    $category     = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $price_range  = isset( $_POST['price_range'] ) ? sanitize_text_field( $_POST['price_range'] ) : '';
    $product_sku  = isset( $_POST['sku'] ) ? sanitize_text_field( $_POST['sku'] ) : '';
    $cats         = "";
    $priceLimit   = "";
    $skuQuery     = "";
    if ( $category ) {
        $cats = " AND tblCategories.category_ids = {$category}";
    }
    if ( $price_range ) {
        $priceLimit = " AND postmeta_table.meta_value {$price_range}";
        if ( strpos( $price_range, '-' ) ) {
            $price      = explode( '-', $price_range );
            $priceLimit = " AND (postmeta_table.meta_value >= {$price[0]} AND postmeta_table.meta_value <= {$price[1]})";
        }
    }
    if ( $product_sku ) {
        $skuQuery = " AND postmeta_table_1.meta_value = '{$product_sku}'";
    }

    global $wpdb;
    $sql = "SELECT 
				 {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_date, {$wpdb->prefix}posts.post_title, {$wpdb->prefix}posts.post_content,{$wpdb->prefix}posts.post_excerpt, {$wpdb->prefix}posts.post_name, 
					tblCategories.category_names, tblCategories.category_ids,
					details.name as product_type,
					attribute_details.attribute_details, 
					variation_id_table.variation_ids as variation_ids,
					postmeta_table.meta_value as price, postmeta_table_1.meta_value as sku
					FROM {$wpdb->prefix}posts
				#Categories
				LEFT JOIN
    (
        SELECT postsAsTaxo.ID, GROUP_CONCAT(category_terms.name) as category_names, GROUP_CONCAT(category_terms.term_id) as category_ids
						FROM {$wpdb->prefix}posts postsAsTaxo
						LEFT JOIN {$wpdb->prefix}term_relationships category_relationships ON (postsAsTaxo.ID = category_relationships.object_id)
						LEFT JOIN {$wpdb->prefix}term_taxonomy category_taxonomy ON (category_relationships.term_taxonomy_id = category_taxonomy.term_taxonomy_id)
						LEFT JOIN {$wpdb->prefix}terms category_terms ON (category_taxonomy.term_id = category_terms.term_id)
						WHERE (category_taxonomy.taxonomy = 'product_cat') 
						 # AND category_terms.term_id IN (6)
						GROUP BY postsAsTaxo.ID
					) as tblCategories ON tblCategories.ID = {$wpdb->prefix}posts.ID
				
				#Link in product type
				LEFT JOIN
    (
        SELECT a.ID, d.name FROM {$wpdb->prefix}posts a
						LEFT JOIN {$wpdb->prefix}term_relationships b ON (a.ID = b.object_id)
						LEFT JOIN {$wpdb->prefix}term_taxonomy c ON (b.term_taxonomy_id = c.term_taxonomy_id)
						LEFT JOIN {$wpdb->prefix}terms d ON (c.term_id = d.term_id)
						WHERE c.taxonomy = 'product_type'
					) as details ON details.ID = {$wpdb->prefix}posts.ID
				
				
				#Attributes in detail
				LEFT JOIN
    (
        SELECT a.ID, GROUP_CONCAT(CONCAT(c.taxonomy, '=', d.slug, '=', d.name)) as attribute_details
						FROM {$wpdb->prefix}posts a
						LEFT JOIN {$wpdb->prefix}term_relationships b ON (a.ID = b.object_id)
						LEFT JOIN {$wpdb->prefix}term_taxonomy c ON (b.term_taxonomy_id = c.term_taxonomy_id)
						LEFT JOIN {$wpdb->prefix}terms d ON (c.term_id = d.term_id)
						WHERE c.taxonomy LIKE 'pa\_%'
						GROUP BY a.ID
					) as attribute_details ON attribute_details.ID = {$wpdb->prefix}posts.ID

				#variations
				LEFT JOIN
    (
        SELECT GROUP_CONCAT(postvars.id) as variation_ids, postvars.post_parent
						FROM {$wpdb->prefix}posts postvars
						WHERE (postvars.post_type = 'product_variation') AND (postvars.post_status = 'publish')
						GROUP BY postvars.post_parent
					) as variation_id_table on variation_id_table.post_parent = {$wpdb->prefix}posts.ID
	  #postmeta
	  LEFT JOIN (
              SELECT postmeta.meta_key , postmeta.meta_value ,postmeta.post_id
              from {$wpdb->prefix}postmeta as postmeta
              WHERE (postmeta.meta_key = '_regular_price')
            ) as postmeta_table on postmeta_table.post_id = {$wpdb->prefix}posts.ID
      LEFT JOIN (
              SELECT postmeta_1.meta_key , postmeta_1.meta_value ,postmeta_1.post_id
              from {$wpdb->prefix}postmeta as postmeta_1
              WHERE (postmeta_1.meta_key = '_sku' )
            ) as postmeta_table_1 on postmeta_table_1.post_id = {$wpdb->prefix}posts.ID
            
        WHERE {$wpdb->prefix}posts.post_status = 'publish' AND {$wpdb->prefix}posts.post_type = 'product' AND {$wpdb->prefix}posts.post_title like '{$keywords}%' $cats  $priceLimit $skuQuery
				ORDER BY post_date ASC";


    $results = $wpdb->get_results( $sql, ARRAY_A );
    $i       = 0;
    foreach ( $results as $data => $product ) : ?>
        <tr>
            <td style="width: 5%"><input type="checkbox"/></td>
            <td class="index"><?php echo $product['post_title']; ?></td>
            <td class="index"><?php echo $product['category_names']; ?>
                <div class="cpf_selected_product_hidden_attr" style="display: none ;">

                    <span class="cpf_selected_product_id"><?php echo $product['ID']; ?></span>
                    <span class="cpf_selected_product_title"><?php echo $product['post_title']; ?></span>
                    <span class="cpf_selected_product_cat_names"><?php echo $product['category_names']; ?></span>
                    <span class="cpf_selected_local_cat_ids"><?php echo $product['category_ids']; ?></span>
                    <span class="cpf_selected_product_type"><?php echo $product['product_type']; ?></span>
                    <span
                        class="cpf_selected_product_attributes_details"><?php echo $product['attribute_details']; ?></span>
                    <span class="cpf_selected_product_variation_ids"><?php echo $product['variation_ids']; ?></span>
                </div>
            </td>
            <td style="width: 40%;">
                <div><span><input type="search" name="categoryDisplayText" class="text_big" id="categoryDisplayText"
                                  value=""
                                  onclick="showeBayCategories('<?php echo $merchat_type; ?>')"
                                  autocomplete="off"
                                  placeholder="Start typing..." style="width: 100%;"></span>
                    <div class="categoryList"></div>
                    <div class="no_remote_category"></div>
                </div>
            </td>
            <td class="cpf-selected-parent" style="width: 7%"><span class="dashicons dashicons-trash "
                                                                    onclick="cpf_remove_feed_parent(this);"
                                                                    title="Delete this row."></span><span
                    class="spinner"></span></td>
        </tr>
        <?php $i ++; ?>
    <?php endforeach; ?>
<?php } ?>

<?php

if ( $cmd == 'savep' ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'ebcpf_custom_products';
    if ( ( $_POST['remote_category'] ) == '' ) {
        echo '<div id="no_remote_category_selected">Please select merchant category.</div>';
        die;
    }
    if ( $_POST['local_cat_ids'] ) {
        $wpdb->insert(
            $table_name,
            array(
                'category'              => sanitize_text_field( $_POST['local_cat_ids'] ),
                'product_title'         => sanitize_text_field( $_POST['product_title'] ),
                'category_name'         => sanitize_text_field( $_POST['category_name'] ),
                'product_type'          => sanitize_text_field( $_POST['product_type'] ),
                'product_attributes'    => sanitize_text_field( $_POST['product_attributes'] ),
                'product_variation_ids' => sanitize_text_field( $_POST['product_variation_ids'] ),
                'remote_category'       => sanitize_text_field( $_POST['remote_category'] ),
                'product_id'            => intval( $_POST['product_id'] )
            )

        );
    }
    print_r( $wpdb->last_query );
    die;
}

if ( $cmd == 'showT' ) {
    global $wpdb;

    $feed_id = isset( $_POST['feed_id'] ) ? intval( $_POST['feed_id'] ) : '';
    if ( $feed_id ) {
        $sql = "SELECT `product_details` from {$wpdb->prefix}cp_feeds where `id` = {$feed_id} ";
        $res    = $wpdb->get_var( ( $sql ) );
        $result = maybe_unserialize( $res );
    } else {
        $table_name = $wpdb->prefix . 'ebcpf_custom_products';
        $sql        = "
            SELECT id,product_title , category_name , remote_category , product_id
             FROM {$table_name}
             ORDER BY id
             ";
        $result     = $wpdb->get_results( $sql, ARRAY_A );
    }

    // print_r($results);
    // die;
    if ( count( $result ) ) {
        foreach ( $result as $data => $product ) { ?>
            <tr>
                <td style="width: 5%"><input type="checkbox"/></td>
                <td class="index"><?php echo $product['product_title']; ?><span class="cpf_product_id_hidden"
                                                                                style="display:none;"><?php echo $product['product_id']; ?></span>
                    <span class="cpf_feed_id_hidden"
                          style="display:none;"><?php echo $product['id']; ?></span>
                </td>
                <td class="index"><?php echo $product['category_name']; ?></td>
                <td style="width: 40%;"><?php echo $product['remote_category']; ?></td>
                <td class="cpf-selected-parent" style="width: 7%"><span class="dashicons dashicons-trash "
                                                                        onclick="cpf_remove_feed(this);"
                                                                        title="Delete this row."></span><span
                        class="spinner"></span></td>
            </tr>
        <?php }
    } else { ?>
        <tr id="cpf-no-products">
            <td colspan="5">No product selected.</td>
        </tr>
    <?php }

}

if ( $cmd == 'delR' ) {
    $id = intval( $_POST['id'] );
    if ( is_array( $id ) ) {
        $id = implode( ',', $id );
    }
    global $pfcore;
    $tableName = $wpdb->prefix . 'ebcpf_custom_products';
    $sql       = "DELETE FROM {$tableName} WHERE id IN ($id)";
    $wpdb->query( $sql );
    $wpdb->last_errors;
    // $wpdb->delete($tableName, array('id' => $id));
    die;

}


if ( $_POST['cmd'] == 'saveEdit' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ebcpf_custom_products';
    $feed_id    = isset( $_POST['feed_id'] ) ? intval( $_POST['feed_id'] ) : '';
    $sql        = "SELECT `product_details` from {$wpdb->prefix}cp_feeds where `feed_type`=1 AND `id` = {$feed_id} ";
    $res        = $wpdb->get_var( ( $sql ) );
    $result     = maybe_unserialize( $res );
    $table_name = $wpdb->prefix . 'ebcpf_custom_products';
    foreach ( $result as $data => $products ) {
        $wpdb->insert( $table_name, $products );
    }
    die;
}

/*if($_GET['q'] == 'feedconf'){

//    global $wpdb;
    $configs = get_option('Google-google-merchant-custom-settings');

    if(count($configs) > 0){
        $i = 1;
        foreach ($configs as $key => $config){
            echo '<tr class="loadcustomfeed">';

            echo  '<td style="text-align: center" class="feedconf1">'.$config['merchant_attr'].'</td>';
            $config_type = $config['cpf_feed_type'] == 0 ? "Attribute" : "Custom Value";
            if($config['cpf_feed_type'] == 0) {
                $config_type = 'Attribute';
                $value = $config['cpf_feed_value_default'];
            } else {
                $config_type = 'Custom Value';
                $value = $config['cpf_feed_value_custom'];
            }
            echo '<td style="text-align: center"  class="feedconf2" >'.$config_type.'</td>';
//            echo  '<td style="width: 5%;text-align: center">'.$config_type.'<input type="hidden" value="'.$config_type.'" name="config_type" /></td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf3">'.$value.'<input type="hidden" value="'.$value.'" name="config_value" /></td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf4">'.$config['cpf_prefix'].'</td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf5">'.$config['cpf_suffix'].'</td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf6">'.$config['cpf_feed_output_limit'].'</td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf7"><span class="dashicons dashicons-admin-generic" onclick="editConf(this)"></span></td>';
            echo  '<td style="width: 5%;text-align: center"  class="feedconf8"><span class="dashicons dashicons-trash" onclick="removeCustomConfig(this);" title=" delete="" this="" rows."=""></span></td>';
        }
    } else
        echo "";
}

if($_GET['q'] == 'delconf'){

    $data = $_POST;
    unset($data['params']);
    $res = maybe_serialize($data);

    if(update_option('Google-google-merchant-custom-settings',"$res",'yes'))
       echo json_encode(
           ['result' => true]
        );
}*/