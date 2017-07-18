<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once 'core/classes/md5.php';
require_once 'core/classes/cart-product-feed.php';
require_once 'core/classes/providerlist.php';
require_once 'core/data/attributedefaults.php';
require_once 'core/data/feedactivitylog.php';
require_once 'core/data/feedcore.php';
require_once 'core/data/productcategories.php';
require_once 'core/data/feedoverrides.php';
require_once 'core/data/productextra.php';
require_once 'core/data/productlist.php';
require_once 'core/data/shippingdata.php';
require_once 'core/data/taxationdata.php';
require_once 'core/registration.php';

//Files Required for eBaySeller 
require_once 'core/classes/eBaycontroller.php';
require_once 'core/model/eBaySite.php';
require_once 'core/model/eBayAccount.php';
require_once 'core/classes/CPF_EbatNs_Logger.php';
