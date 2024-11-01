<?php
function acemp_product_content()
{
    global $woocommerce;
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_order_statuses() ),
        ) );           
        $array = [];
        $product_id_arr =[];
        foreach ($customer_orders as $order) 
        {       
          $order_id = $order->ID;                    
          $order_data = wc_get_order($order_id);
          foreach ($order_data->get_items() as $item_id => $item_data) 
          {
            $product = $item_data->get_product();
            $product_id = $product->get_id();
            $product_info = wc_get_product($product_id);
            $product_quantity = $item_data->get_quantity();                 
            if(in_array($product_id, $product_id_arr))
            { 
              foreach ($array as $key => $value) 
              {
                foreach ($value as $id => $qty) 
                {
                  if($id == $product_id)
                  {
                    $array[$key][$product_id] = $qty + $product_quantity;
                  }
                }
              }   
            }
            else{
              $array[][$product_id] = $item_data->get_quantity();
              $product_id_arr[] = $product_id;
            }                  
          }
        }
            $array_count = count($array);
            $limit = 12;
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
              if($array_count > $i){
              foreach ($array[$i] as $product_id => $qty) 
              {                
                $product_info = wc_get_product( $product_id );
                $product_data = $product_info->get_data();
                $image = get_the_post_thumbnail_url($product_info->get_id(), 'full');
                $url = get_permalink( $product_id ) ;
              ?>
              <li class="single_product">
                <div class="inner">
                  <sapn class="thumb"><img height="80" width="80" src="<?php echo $image; ?>"></sapn>
                  <span class="name"><a target="_blank" href="<?php echo $url; ?>"><?php echo $product_data['name']; ?></a></span>
                  <span class="qty" data-toggle="modal" data-target="#<?php echo $product_id; ?>"><?php echo $qty; ?></span>
                  <?php include('modal.php'); ?>
              </li>
              <?php
               } } }
  die();    
}
function acemp_frontend_content()
{
?>
  <div class="mypurchase_container">
    <div class="tab">
  		<button class="tablinks active" onclick="openCity(event, 'mypurchase')">My Purchase</button>
  		<button class="tablinks" onclick="openCity(event, 'analysis')">Purchase analysis</button>
  	</div>
    <!-- - - - - - - - - - My Purchase  - - - - - - - - -->
      <div id="mypurchase" class="tabcontent">
        <?php    
          global $woocommerce;
          $customer_orders = get_posts( array(
              'numberposts' => -1,
              'meta_key'    => '_customer_user',
              'meta_value'  => get_current_user_id(),
              'post_type'   => wc_get_order_types(),
              'post_status' => array_keys( wc_get_order_statuses() ),
          ) );           
          $array = [];
          $product_id_arr =[];
          foreach ($customer_orders as $order) 
          {       
            $order_id = $order->ID;                    
            $order_data = wc_get_order($order_id);
            foreach ($order_data->get_items() as $item_id => $item_data) 
            {
              $product = $item_data->get_product();
              $product_name = $product->get_name(); // Get the product name
              $product_id = $product->get_id();
              $product_info = wc_get_product($product_id);
              $product_quantity = $item_data->get_quantity();
              if(in_array($product_id, $product_id_arr))
              { 
                foreach ($array as $key => $value) 
                {
                  foreach ($value as $id => $qty) 
                  {
                    if($id == $product_id)
                    {
                      $array[$key][$product_id] = $qty + $product_quantity;
                    }
                  }
                }
              }
              else{
                $array[][$product_id] = $item_data->get_quantity();
                $product_id_arr[] = $product_id;
              }                  
            }
          }
          ?>
          <ul class="product_list">
            <?php
              $array_count = count($array);
              $limit = 12;
              if (isset($_POST["current_page"]))
              {
                 $page = sanitize_text_field($_POST["current_page"]);
              }
              else 
              {
                  $page=1;
              }
              $start_from = ($page-1) * $limit;
              $total_page =ceil( $array_count / $limit);
              for ($i = $start_from; $i < $start_from + $limit; $i++)
              {
                if($array_count > $i){
                foreach ($array[$i] as $product_id => $qty) 
                {                
                  $product_info = wc_get_product( $product_id );
                  $product_data = $product_info->get_data();
                  $image = get_the_post_thumbnail_url($product_info->get_id(), 'full');
                  $url = get_permalink( $product_id ) ;
                ?>
                <li class="single_product">
                  <div class="inner">
                    <sapn class="thumb"><img height="80" width="80" src="<?php echo $image; ?>"></sapn>
                    <span class="name"><a target="_blank" href="<?php echo $url; ?>"><?php echo $product_data['name']; ?></a></span>
                    <span class="qty" data-toggle="modal" data-target="#<?php echo $product_id; ?>"><?php echo $qty; ?></span>
                    <?php include('modal.php'); ?>
                </li>
              <?php } } } ?>
          </ul>
          <?php
            $total_pages = ceil( $array_count / $limit);
            $pagLink = "<div class='order-pagination' style=''><div class='inner'>";
            $pagLink .= "<div class='pagination-page' aria-hidden='true' data-page='1'><</div>";
            for ($i=1; $i<=$total_pages; $i++)
            {
                $pagLink .= "<span class='pagination-page' data-page='".$i."'>".$i."</span>";
            }
            echo $pagLink . "<div class='pagination-page' aria-hidden='true' data-page='".$total_pages."'>></div></div></div>";
          ?>
      </div>
  <!-- - - - - - - - - - Purchase analysis - - - - - - - - -->
   <?php include('purchase-analysis.php'); ?>
  <!-- - - - - - - - - - Purchase analysis - - - - - - - - -->
</div><!-- .content-area -->
</div>
<?php
}