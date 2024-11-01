<div id="analysis" class="tabcontent" style="display: none;">
    <span class="site_url" hidden><?php echo get_permalink( get_option('woocommerce_myaccount_page_id')); ?></span>
    <span class="admin-url" hidden><?php echo $admin_url = admin_url('admin-ajax.php');?></span>
    <div class="analysis_container">
      <?php
        $year_data = [];        
        foreach ($customer_orders as $order) 
        {    
            $order_id = $order->ID;
            $order_data = wc_get_order($order_id);
            $order_url =$order_data->get_view_order_url();
            $order_total = $order_data->get_total();
            $order_date = $order_data->order_date;
            $old_date_timestamp = strtotime($order_date);             
            $year = date("Y",$old_date_timestamp);      
            if(in_array($year, $year_data)) {
              $order_id = $order->ID;
            }else{          
              $year_data[] = $year;
            } 
        }
        /*  Category details */
        $args = array(
                    'number'     => $number,
                    'orderby'    => $orderby,
                    'order'      => $order,
                    'hide_empty' => $hide_empty,
                    'include'    => $ids
                    );
        $product_categories = get_terms( 'product_cat', $args );
        /*  Category details */
        ?>
        <div class="select-year">
          Year : 
          <select class="year_list">
          <option value="all">All</option>        
            <?php
            foreach ($year_data as $key => $year) 
            {
            ?>
              <option value="<?php echo $year; ?>"><?php echo $year; ?></option><li><span><?php echo $year; ?></span></li>
            <?php
            }
            ?>
          </select>
          <span class="generate_chart">View Graphs</span>
        </div>

        <div class="year_details">
            <?php
            $all_year_total = 0;
            $all_payment_method_arr = [];
            $all_item_total = []; 
            $all_item_qty_total = 0;
            $all_category_data = [];
            $all_year_product_qty = [];
            foreach ($year_data as $key => $year) 
            {     
              $item_total = [];       
              $categories_data = [];
              $payment_method_arr = [];
              $year_product_qty = []; 
              unset($main);
              $item_qty_total = 0;           
              $year_total = 0;
              $category_order_total = 0;
              foreach ($product_categories as $categories) 
              {
                $cat_arr[$categories->name] = '';
              }                     
              $main  = array('category' =>  '');
              foreach ($customer_orders as $order) 
              { 
                  $order_id = $order->ID;
                  $order_data = wc_get_order($order_id);
                  $total = $order_data->get_total();
                  $all_year_total = $all_year_total + $total;
                  $items = $order_data->get_items();
                  $order_date = $order_data->order_date;
                  $old_date_timestamp = strtotime($order_date);             
                  $order_year = date("Y",$old_date_timestamp);
                if($year == $order_year)
                {
                  $payment_method = get_post_meta( $order->ID, '_payment_method', true );                  
                  if(array_key_exists($payment_method, $payment_method_arr))
                  {
                    $old_total = $payment_method_arr[$payment_method];
                    $payment_method_arr[$payment_method] = $old_total + $total;
                  }
                  else
                  {
                    $payment_method_arr[$payment_method] = $total;
                  }
                  if(array_key_exists($payment_method, $all_payment_method_arr))
                  {
                    $old_total = $all_payment_method_arr[$payment_method];
                    $all_payment_method_arr[$payment_method] = $old_total + $total;
                  }
                  else
                  {
                    $all_payment_method_arr[$payment_method] = $total;
                  }
                  $order_id;
                  $year_total = $year_total + $total;
                  foreach ( $items as $item_id => $item_data) 
                  {
                      $item_qty_total = $item_qty_total + $item_data['quantity'];
                      $all_item_qty_total = $all_item_qty_total + $item_data['quantity'];
                      $item_total[] = $item_data['product_id'];
                      $all_item_total[] = $item_data['product_id'];
                      $product_id = $item_data['product_id'];
                      $terms = wp_get_post_terms( $product_id, 'product_cat');
                      $prd_category = $terms[0]->name;
                      if($prd_category == ''){
                        $prd_category = 'Uncategorized';
                      }
                      //  category with quantity 
                      if(array_key_exists($prd_category, $all_category_data))
                      {
                        $old_cat = $all_category_data[$prd_category];
                        $new_cat = $old_cat + $item_data['quantity'];
                        $all_category_data[$prd_category] = $new_cat;
                      }
                      else
                      {
                        $all_category_data[$prd_category] = $item_data['quantity'];
                      }
                      // product with quantity
                      if(array_key_exists($product_id, $year_product_qty))
                      {
                        $old_qty = $year_product_qty[$product_id];
                        $new_qty = $old_qty + $item_data['quantity'];
                        $year_product_qty[$product_id] = $new_qty;
                      }
                      else{
                        $year_product_qty[$product_id] = $item_data['quantity'];
                      }
                      // all year product with quantity
                      if(array_key_exists($product_id, $all_year_product_qty))
                      {
                        $old_qty = $all_year_product_qty[$product_id];
                        $new_qty = $old_qty + $item_data['quantity'];
                        $all_year_product_qty[$product_id] = $new_qty;
                      }
                      else{
                        $all_year_product_qty[$product_id] = $item_data['quantity'];
                      }
                     // var_dump($year_product_qty);
                      $product_cat = $terms[0]->name;
                      if(empty($main['category'][$product_cat]['product_data']))
                      { 
                        //var_dump($main['category'][$product_cat]['total']);
                      //  $main['category'][$product_cat]['total'] = $item_data['total'];
                      //  $main['category'][$product_cat]['count'] = $item_data['quantity'];
                      //  $main['category'][$product_cat]['product_data'] = array($item_data['product_id'] => $item_data['total']);
                      }
                      else
                      {                     
                        $old_total = $main['category'][$product_cat]['total'];
                        $new_total = $main['category'][$product_cat]['total'] + $item_data['total'];
                        $old_count = $main['category'][$product_cat]['count'];
                        $new_count = $main['category'][$product_cat]['count'] + $item_data['quantity'];
                        $main['category'][$product_cat]['count'] = $new_count;
                        $main['category'][$product_cat]['total'] = $new_total;
                        $single_total = $main['category'][$product_cat]['product_data'][$product_id];
                        if($single_total != '')
                        {
                          $new_total = $single_total + $item_data['total'];
                          $main['category'][$product_cat]['product_data'][$product_id] = $new_total;
                        }else{
                          $old_value = $main['category'][$product_cat]['product_data'];
                          $new_val = array($product_id => $item_data['total']);
                          $product_data = $old_value + $new_val;
                          $main['category'][$product_cat]['product_data'] = $product_data;
                        }
                      }
                    }  // order items
                  } 
                }//orders                                 
              ?>              
              <div class="single-year active" id="<?php echo $year; ?>">
                <div class="year"></div>
                <div class="details-header">Payment By</div>
                <div class="payment-details">                  
                  <div class="inner">
                    <table>
                      <tr>
                        <th>Payment Method</th>
                        <th>Amount</th>
                      </tr>                  
                      <?php 
                      foreach ($payment_method_arr as $method => $amount)
                      {                     
                      ?>
                      <tr class="payment_data">                    
                        <td><span class="method"><?php echo $method; ?></span></td>
                        <td><span><?php echo get_woocommerce_currency_symbol(); ?></span>
                            <span class="amount"><?php echo $amount; ?></span>
                      </tr>
                    <?php } ?>
                      <tr>
                        <th>Total : </th>
                        <th><?php echo get_woocommerce_currency_symbol().$year_total; ?></th>
                      </tr>
                    </table>
                  </div> <!-- inner -->            
                </div><!-- Payment details -->

                <div class="details-header">Products</div>
                <div class="product-details">
                  <div class="inner">
                    <div class="first">
                      <table>
                        <?php 
                          $item_total_new = array_unique($item_total);
                          foreach ($year_product_qty as $id => $qty)
                          {
                             $product_info = wc_get_product($id);
                             $name = $product_info->get_title();                         
                        ?>
                        <span class="product-data" data-name="<?php echo $name; ?>" data-qty="<?php echo $qty; ?>"></span>
                        <?php } ?>
                        <tr><td>Total Products : </td></td><td><?php echo count($item_total_new); ?></td></tr>
                        <tr><td>Total Quantity : </td><td><?php echo $item_qty_total; ?></td></tr>
                        <?php
                          foreach ($main as $abc => $year_category) 
                          {}
                        ?>
                        <tr><td>Total Category : </td><td><?php echo count($year_category); ?></td></tr>
                      </table>
                    </div>
                    <div class="second"><img src="<?php echo esc_url( plugins_url( '../images/icon_8433.png', __FILE__ ) );?>"></div>            
                  </div>
                </div>

                <div class="details-header">Category</div>
                  <div class="category-details">
                    <div class="inner">
                      <ul style="list-style: none; margin: 0px;" class="category-list">
                        <?php
                          foreach ($main as $abc => $year_category) 
                          {   
                           // var_dump($year_category);                     
                            foreach ($year_category as $k => $v) 
                            {
                               $category_prd[$year][$k] = $v['product_data'];
                            ?>
                              <a target="_blank" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id')).'mypurchase_filter?y='.$year.'&category='.$k;?>">
                                <li>
                                  <span class="category">
                                    <span class="category-name"><?php echo $k; ?></span>
                                    <!-- <div class="category-total"><?php echo get_woocommerce_currency_symbol().$v['total']; ?></div> --> 
                                    <span class="count" hidden=""><?php echo $v['count']; ?></span>
                                  </span>

                                </li>
                              </a> 
                        <?php } } ?> 
                      </ul>
                    </div>
                  </div> 
              </div> <!-- Single year --> 
              <div class="graph_section graph-section_<?php echo $year; ?>">
                <h2>Pay with Payment Method</h2>
                <canvas id="year_chart_<?php echo $year; ?>"></canvas>
              </div>
              <div class="graph_section graph-section_<?php echo $year; ?>">
                <h2>Category - Product Chart</h2>
                <canvas id="year_category_chart_<?php echo $year; ?>"></canvas> 
              </div>
              <div class="graph_section graph-section_<?php echo $year; ?>">
                <h2>Product - Quantity Chart</h2>
                <canvas id="year_product_chart_<?php echo $year; ?>"></canvas>
              </div>                     
          <?php
        } // year      
    ?>
    <div class="single-year" id="all">
                <div class="year"></div>
                <div class="details-header">Payment By</div>
                <div class="payment-details">                  
                  <div class="inner">
                    <table>
                      <tr>
                        <th>Payment Method</th>
                        <th>Amount</th>
                      </tr>                  
                      <?php 
                      foreach ($all_payment_method_arr as $method => $amount)
                      {                     
                      ?>
                      <tr class="payment_data">                    
                        <td class="method"><?php echo $method; ?></td>
                        <td><span><?php echo get_woocommerce_currency_symbol(); ?></span>
                            <span class="amount"><?php echo $amount; ?></span>
                        </td>
                      </tr>
                    <?php } ?>
                      <tr>
                        <th>Total : </th>
                        <th><?php echo get_woocommerce_currency_symbol().$all_year_total; ?></th>
                      </tr>
                    </table>
                  </div> <!-- inner -->            
                </div><!-- Payment details -->

                <div class="details-header">Products</div>
                <div class="product-details">
                  <div class="inner">
                    <div class="first">
                      <table>
                        <?php 
                          $all_item_total_new = array_unique($all_item_total);
                          foreach ($all_year_product_qty as $id => $qty)
                          {
                             $product_info = wc_get_product($id);
                             $name = $product_info->get_title();                         
                        ?>
                        <span class="product-data" data-name="<?php echo $name; ?>" data-qty="<?php echo $qty; ?>"></span>
                        <?php } ?>
                        <tr><td>Total Products : </td><td><?php echo count($all_item_total_new); ?></td></tr>
                        <tr><td>Total Quantity : </td><td><?php echo $all_item_qty_total; ?></td></tr>
                        <tr><td>Total Category : </td><td><?php echo count($all_category_data); ?></td></tr>
                      </table>
                    </div>
                    <div class="second"><img src="<?php echo esc_url( plugins_url( '../images/icon_8433.png', __FILE__ ) );?>"></div>            
                  </div>
                </div>

                <div class="details-header">Category</div>
                  <div class="category-details">
                    <div class="inner">
                      <ul style="list-style: none; margin: 0px;" class="category-list">
                        <?php
                          foreach ($all_category_data as $category => $count) 
                          {
                            ?>
                              <a target="_blank" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id')).'mypurchase_filter?y=all&category='.$category;?>">
                                <li>
                                  <span class="category">
                                    <span class="category-name"><?php echo $category; ?></span>
                                    <span class="count" hidden><?php echo $count; ?></span>
                                    <!-- <div class="category-total"><?php echo get_woocommerce_currency_symbol().$v['total']; ?></div> --> 
                                  </span>
                                </li>
                              </a> 
                        <?php } ?> 
                      </ul>
                    </div>
                  </div> 
              </div>
    </div><!-- year details -->  
    </div>
  </div>
  <div class="graph_section year graph-section_all">
    <h2>Pay with Payment Method</h2>
    <canvas id="year_chart_all" style="height: 100%;"></canvas>     
  </div>
  <div class="graph_section graph-section_all">
    <h2>Category - Product Chart</h2>
    <canvas id="year_category_chart_all" style="min-height: 100%;"></canvas>     
  </div>
  <div class="graph_section graph-section_all">
    <h2>Product - Quantity Chart</h2>
    <canvas id="year_product_chart_all" style="min-height: 100%;"></canvas>     
  </div>