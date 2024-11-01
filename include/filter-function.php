<?php 
function acemp_select_nothing()
{   
    $category_list = array_map("strip_tags", $_POST['category']);

    $main = array('category' => '');
    $customer_orders = get_posts(array(
            'numberposts' => - 1,
            'meta_value' => get_current_user_id() ,
            'post_type' => wc_get_order_types() ,
            'post_status' => array_keys(wc_get_order_statuses()) ,
    ));
    $productIdData = [];
    foreach($customer_orders as $order)
    {
      $order_id = $order->ID;
      $order_data = wc_get_order($order_id);
      $items = $order_data->get_items();
      foreach($items as $item_id => $item_data)
      {
        $item_qty_total = $item_qty_total + $item_data['quantity'];
        $product_id = $item_data['product_id'];
        $variation_id= $item_data['variation_id'];       
                        
        if($variation_id != 0){
          $product_id = $variation_id;
        }
       $terms = wp_get_post_terms( $product_id, 'product_cat');
      $product_cat = $terms[0]->name;
        if(isset($_POST['category']) && in_array($product_cat, $category_list))
        {
          if(in_array($product_id, $productIdData))
          {  }
          else
          {
            $productIdData[] = $product_id;         
          } 
        }
        else if(!isset($_POST['category']))
        {
          if(in_array($product_id, $productIdData))
          {  }
          else
          {
            $productIdData[] = $product_id;         
          } 
        }
        else{

        }
           
      }   
    } 

      $array_count = count($productIdData);
            $limit = 4;
            if (isset($_POST["current_page"])){
                $page = sanitize_text_field($_POST["current_page"]);
            }
            else{
                $page=1;
            }
          $page;
          $start_from = ($page-1) * $limit;
            $total_page =ceil( $array_count / $limit);
            for ($i = $start_from; $i < $start_from + $limit; $i++)
            {
              
              if($array_count > $i)
              {              
                $product_id = $productIdData[$i];
                $product_info = wc_get_product( $product_id );
                $product_addtocart = $product_info->add_to_cart_url();
                $product_data = $product_info->get_data();
                $image = get_the_post_thumbnail_url($product_info->get_id(), 'full');
                $url = get_permalink( $product_id );          
          ?>
          <li class="post-<?php echo $product_id; ?> type-product status-publish has-post-thumbnail product_cat-headphone product instock shipping-taxable purchasable product-type-simple">
            <a href="<?php echo $url; ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
              <img width="250" height="250" src="<?php echo $image; ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" sizes="100vw">
                <h2 class="woocommerce-loop-product__title"><?php echo $product_data['name']; ?></h2>
                <span class="price">
                  <span class="woocommerce-Price-amount amount">
                    <span class="woocommerce-Price-currencySymbol">
                      <?php echo get_woocommerce_currency_symbol(); ?>
                    </span>
                    <?php echo $product_data['regular_price']; ?>
                  </span>
                </span>
            </a>
            <a href="<?php echo $product_addtocart; ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $product_id; ?>" data-product_sku="SS038" aria-label="Add “Headphone” to your cart" rel="nofollow">Add to cart</a></li> 

        <?php        
                
            }
            }

            $total_pages = ceil( $array_count / $limit);
            $pagLink = "<div class='order-pagination' style=''><div class='inner'>";
            $pagLink .= "<div class='pagination-page' aria-hidden='true' data-page='1'><</div>";
            for ($i=1; $i<=$total_pages; $i++)
            {
                $pagLink .= "<span class='pagination-page' data-action='acemp_select_nothing' data-page='".$i."'>".$i."</span>";
            }
            echo $pagLink . "<div class='pagination-page' aria-hidden='true' data-page='".$total_pages."'>></div></div></div>";
        
  die();
}
function acemp_all_category()
{
    
      $customer_orders = get_posts( array(
                  'numberposts' => -1,
                  'meta_value'  => get_current_user_id(),
                  'post_type'   => wc_get_order_types(),
                  'post_status' => array_keys( wc_get_order_statuses() ),
                  
      ));
         
      foreach ($customer_orders as $order) 
      { 
          $order_id = $order->ID;
          $order_data = wc_get_order($order_id);
          $total = $order_data->get_total();
          $items = $order_data->get_items();
          $order_date = $order_data->order_date;
          $old_date_timestamp = strtotime($order_date);             
          $order_year = date("Y",$old_date_timestamp);
          $year_total = $year_total + $total;
          foreach ( $items as $item_id => $item_data) 
          {
              $item_qty_total = $item_qty_total + $item_data['quantity'];
              
              $product_id = $item_data['product_id'];
                $terms = wp_get_post_terms( $product_id, 'product_cat');                        
                $product_cat = $terms[0]->name;
                if($product_cat == ''){
                  $product_cat = 'Uncategorized';
                }
                $product_cat_arr[] = $product_cat;                       
          }                   
      }
            
      $final_product_cat_arr = array_unique($product_cat_arr);
      echo '<div class="inner"><h4>Select Category</h4><ul class="filter_category_list">';         
      foreach ($final_product_cat_arr as $key => $cat_name) 
      {
        echo '<li>';
        ?>
        <input type="checkbox" name="year" class="filter_category" value="<?php echo $cat_name; ?>" >                   
        <?php
        echo $cat_name.'</li>';
      }          
      echo '</ul></div>'; 
                die(); 
}
function acemp_select_category_based_year()
{
    $year_list = array_map("strip_tags", $_POST['year']);
    $category_list = array_map("strip_tags", $_POST['category']);
     $main  = array('category' =>  '');
      $customer_orders = get_posts( array(
                  'numberposts' => -1,
                  'meta_value'  => get_current_user_id(),
                  'post_type'   => wc_get_order_types(),
                  'post_status' => array_keys( wc_get_order_statuses() ),
                  
                ));
          foreach ($year_list as $key => $year) 
          {
              foreach ($customer_orders as $order) 
              { 
                  $order_id = $order->ID;
                  $order_data = wc_get_order($order_id);
                  $total = $order_data->get_total();
                  $items = $order_data->get_items();
                  $order_date = $order_data->order_date;
                  $old_date_timestamp = strtotime($order_date);             
                  $order_year = date("Y",$old_date_timestamp);
                  if($year == $order_year)
                  {
                    $year_total = $year_total + $total;
                    foreach ( $items as $item_id => $item_data) 
                    {
                      $item_qty_total = $item_qty_total + $item_data['quantity'];
                      
                      $product_id = $item_data['product_id'];
                        $terms = wp_get_post_terms( $product_id, 'product_cat');                        
                        $product_cat = $terms[0]->name;
                        if($product_cat == ''){
                          $product_cat = 'Uncategorized';
                        }
                        $product_cat_arr[] = $product_cat;
                        if(isset($_POST['category']) && in_array($product_cat, $category_list))
                        {
                            
                            if($item_data['variation_id'] != 0)
                            {
                                $product_id = $item_data['variation_id'];
                            }
                            else{
                              $product_id = $item_data['product_id'];
                            }
                            if(!in_array($product_id, $product_arr))
                            {  
                                  $product_arr[] = $product_id;                  
                            }
                        }
                    }  
                  } 
                }
              }
             // var_dump($main);
              $final_product_cat_arr = array_unique($product_cat_arr);             
              $final_product_arr = array_unique($product_arr);
              
                if(isset($_POST['category']))
                {  
                   $array_count = count($final_product_arr);
                  $limit = 4;
                  if (isset($_POST["current_page"])){
                      $page = sanitize_text_field($_POST["current_page"]);
                  }
                  else{
                      $page=1;
                  }
                $page;
                $start_from = ($page-1) * $limit;
                  $total_page =ceil( $array_count / $limit);
                  for ($i = $start_from; $i < $start_from + $limit; $i++)
                  {                    
                    if($array_count > $i)
                    {   
                      $product_id = $final_product_arr[$i];
                      $product_info = wc_get_product( $product_id );
                      $product_addtocart = $product_info->add_to_cart_url();
                      $product_data = $product_info->get_data();
                      $image = get_the_post_thumbnail_url($product_info->get_id(), 'full');
                      $url = get_permalink( $product_id ) ; 
                      ?>
                        <li class="post-<?php echo $product_id; ?> type-product status-publish has-post-thumbnail product_cat-headphone product instock shipping-taxable purchasable product-type-simple">
                        <a href="<?php echo $url; ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                          <img width="250" height="250" src="<?php echo $image; ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" sizes="100vw">
                            <h2 class="woocommerce-loop-product__title"><?php echo $product_data['name']; ?></h2>
                            <span class="price">
                              <span class="woocommerce-Price-amount amount">
                                <span class="woocommerce-Price-currencySymbol">
                                  <?php echo get_woocommerce_currency_symbol(); ?>
                                </span>
                                <?php echo $product_data['regular_price']; ?>
                              </span>
                            </span>
                        </a>
                        <a href="<?php echo $product_addtocart; ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $product_id; ?>" data-product_sku="SS038" aria-label="Add “Headphone” to your cart" rel="nofollow">Add to cart</a></li>
                      <?php                     
                    }
                }
                $total_pages = ceil( $array_count / $limit);
                  $pagLink = "<div class='order-pagination' style=''><div class='inner'>";
                  $pagLink .= "<div class='pagination-page' aria-hidden='true' data-page='1'><</div>";
                  for ($i=1; $i<=$total_pages; $i++)
                  {
                      $pagLink .= "<span class='pagination-page' data-action='acemp_select_category_based_year' data-page='".$i."'>".$i."</span>";
                  }
                  echo $pagLink . "<div class='pagination-page' aria-hidden='true' data-page='".$total_pages."'>></div></div></div>";
              }
                else
                {                  
                  echo '<div class="inner"><h4>Select Category</h4><ul class="filter_category_list">';         
                  foreach ($final_product_cat_arr as $key => $cat_name) 
                  {
                    echo '<li>';
                    ?>
                    <input type="checkbox" name="year" class="filter_category" value="<?php echo $cat_name; ?>" >                   
                    <?php
                    echo $cat_name.'</li>';
                  }          
                  echo '</ul></div>';
                }
              die();

}
function acemp_select_year_only()
{
    $year_list = array_map("strip_tags", $_POST['year']);

    $main  = array('category' =>  '');
    $customer_orders = get_posts( array(
                  'numberposts' => -1,
                  'meta_value'  => get_current_user_id(),
                  'post_type'   => wc_get_order_types(),
                  'post_status' => array_keys( wc_get_order_statuses() ),
                  
                ));
              $productIdData = [];

                foreach ($year_list as $key => $year) 
                {
                    foreach ($customer_orders as $order) 
                    { 
                        $order_id = $order->ID;
                        $order_data = wc_get_order($order_id);
                        $items = $order_data->get_items();
                        $order_date = $order_data->order_date;
                        $old_date_timestamp = strtotime($order_date);             
                        $order_year = date("Y",$old_date_timestamp);
                        if($year == $order_year)
                        {
                          foreach ( $items as $item_id => $item_data) 
                          {
                            if($item_data['variation_id'] != 0)
                            {
                                $product_id = $item_data['variation_id'];
                            }
                            else{
                              $product_id = $item_data['product_id'];
                            }
                            if(!in_array($product_id, $product_arr))
                            {  
                                  $product_arr[] = $product_id;                  
                            }
                          }  
                        } 
                    }
                }
                $final_product_arr = array_unique($product_arr);    
                  $array_count = count($final_product_arr);
                  $limit = 4;
                  if (isset($_POST["current_page"])){
                      $page = sanitize_text_field($_POST["current_page"]);
                  }
                  else{
                      $page=1;
                  }
             
                $start_from = ($page-1) * $limit;
                  $total_page =ceil( $array_count / $limit);
                  for ($i = $start_from; $i < $start_from + $limit; $i++)
                  {                   
                    if($array_count > $i)
                    {   
                      $product_id = $final_product_arr[$i];
                      $product_info = wc_get_product( $product_id );
                      $product_addtocart = $product_info->add_to_cart_url();
                      $product_data = $product_info->get_data();
                      $image = get_the_post_thumbnail_url($product_info->get_id(), 'full');
                      $url = get_permalink( $product_id ) ; 
                      ?>
                        <li class="post-<?php echo $product_id; ?> type-product status-publish has-post-thumbnail product_cat-headphone product instock shipping-taxable purchasable product-type-simple">
                        <a href="<?php echo $url; ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                          <img width="250" height="250" src="<?php echo $image; ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" sizes="100vw">
                            <h2 class="woocommerce-loop-product__title"><?php echo $product_data['name']; ?></h2>
                            <span class="price">
                              <span class="woocommerce-Price-amount amount">
                                <span class="woocommerce-Price-currencySymbol">
                                  <?php echo get_woocommerce_currency_symbol(); ?>
                                </span>
                                <?php echo $product_data['regular_price']; ?>
                              </span>
                            </span>
                        </a>
                        <a href="<?php echo $product_addtocart; ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $product_id; ?>" data-product_sku="SS038" aria-label="Add “Headphone” to your cart" rel="nofollow">Add to cart</a></li>
                      <?php                     
                    }
                }
                $total_pages = ceil( $array_count / $limit);
                  $pagLink = "<div class='order-pagination' style=''><div class='inner'>";
                  $pagLink .= "<div class='pagination-page' aria-hidden='true' data-page='1'><</div>";
                  for ($i=1; $i<=$total_pages; $i++)
                  {
                      $pagLink .= "<span class='pagination-page' data-action='acemp_select_year_only' data-page='".$i."'>".$i."</span>";
                  }
                  echo $pagLink . "<div class='pagination-page' aria-hidden='true' data-page='".$total_pages."'>></div></div></div>";
                //  echo do_shortcode( '[products ids="'.$temp_id.'"]' );
  die();
}