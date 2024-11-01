<?php
function acemp_filter_frontend_content()
{
 ?>
   <div class="mypurchase-filter">
    <button class="filter-button">Filter</button>
   <span class="admin-url" hidden><?php echo $admin_url = admin_url('admin-ajax.php');?></span> 
  <?php
   global $woocommerce;
        $customer_orders = get_posts( array(
                  'numberposts' => -1,
                  'meta_value'  => get_current_user_id(),
                  'post_type'   => wc_get_order_types(),
                  'post_status' => array_keys( wc_get_order_statuses() ),
                  
                ));
        $year_data = [];
        
        foreach ($customer_orders as $order) 
        {  
            $order_id = $order->ID;
            $order_data = wc_get_order($order_id);
            $order_url =$order_data->get_view_order_url();
            $order_total = $order_data->get_total();
            $order_date = $order_data->order_date;
            $items = $order_data->get_items();
            $old_date_timestamp = strtotime($order_date);             
            $year = date("Y",$old_date_timestamp);      
            if(in_array($year, $year_data)) 
            {
              $order_id = $order->ID;
            }else{          
              $year_data[] = $year;
            }
            $y = sanitize_text_field($_GET['y']);
            $category = sanitize_text_field($_GET['category']);
              if($year == $y)
              {
                  $year_total = $year_total + $total;
                  foreach ( $items as $item_id => $item_data) 
                  {
                      $item_qty_total = $item_qty_total + $item_data['quantity'];                      
                      $product_id = $item_data['product_id'];
                        $terms = wp_get_post_terms( $product_id, 'product_cat');                        
                        $product_cat = $terms[0]->name;
                        $product_cat_arr[] = $terms[0]->name;                        
                      }
              }
              if($y == 'all')
              {
                foreach ( $items as $item_id => $item_data) 
                {
                    $item_qty_total = $item_qty_total + $item_data['quantity'];                      
                    $product_id = $item_data['product_id'];
                    $terms = wp_get_post_terms( $product_id, 'product_cat');                        
                    $product_cat = $terms[0]->name;
                    $product_cat_arr[] = $terms[0]->name;                        
                }
              }
        }

  ?>
    <div class="right">
    	<div class="inner">
     <div class="filters">
            <div class="inner">
              <h4>Filters <span class="clear-filter">Clear All</span></h4>
             <div class="filter-list"></div>
           </div>
      </div> 
      		<div class="select-year">        
          		<h4>Select Year</h4>
          			<ul class="filter_year_list">
		              <?php
		                foreach ($year_data as $key => $year) 
		                { ?>
		                  <li><input type="checkbox" class="filter_year" <?php if($y == $year){echo 'checked'; } ?> <?php if($y == 'all'){echo 'checked'; } ?> name="year"  value="<?php echo $year; ?>"><?php echo $year; ?>
		                  </li>
		                  <?php
		                }
		              ?>
          			</ul>    
      		</div> 
           
     		<div class="select-category">
        		<div class="inner">
        			<h4>Select Category</h4>
        			<ul class="filter_category_list"> 
            <?php
            if(!empty($product_cat_arr))
            {
                $product_cat_arr = array_unique($product_cat_arr);
                foreach ($product_cat_arr as $key => $name) 
                { ?>
                    <li><input type="checkbox" class="filter_category" <?php if($category == $name){echo 'checked'; } ?> name="year"  value="<?php echo $name; ?>"><?php echo $name; ?>
                    </li>
                    <?php
                }
            }             
            ?>
           </ul>
           </div>
      </div>

    </div>
  </div>
   <div class="left">
    <div class="loader-gif"> 
      <img width="100" src="<?php echo esc_url( plugins_url( '../images/loading1.gif', __FILE__ ) );?>">
    </div>
      <div class="product-container">  
        <div class="woocommerce columns-4 ">  
          <ul class="products columns-4"> 
        <?php
        if(empty($_GET['y']))
        {
          foreach ($customer_orders as $order) 
          {   
              $order_id = $order->ID;
              $order_data = wc_get_order($order_id);            
              $items = $order_data->get_items();           
              foreach ( $items as $item_id => $item_data) 
              {
                $item_qty_total = $item_qty_total + $item_data['quantity'];                      
                $product_id = $item_data['product_id'];
                $temp_id = $temp_id.$product_id.','; 
                                   
              }           
          }
          echo do_shortcode( '[products ids="'.$temp_id.'"]' );
        }   
          
        ?>
      </ul>
      </div>
    </div>
  </div> 
</div>
  <?php
}