
          <div class="modal fade" id="<?php echo $product_id; ?>" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <span class="close" data-dismiss="modal">&times;</span>
                  <h4 class="modal-title"><?php echo $product_data['name']; ?></h4>
                </div>
                <div class="modal-body">
                  <ul style="list-style: none;">   
                    <li class="single_order" style="font-weight: 600;">
                          <div class="order_no">Order No</div>
                          <div class="order_date">Order Date</div>
                          <div class="product_qty">Qty</div>
                          <div class="product_total">Amount</div>
                          <div class="order_total">Order Total</div>   
                    </li>
                    <div id="easyPaginate" class="easyPaginate">     
                <?php

                foreach ($customer_orders as $order) 
                   {  
                    $order_id = $order->ID;

                    $order_data = wc_get_order($order_id);
                    $order_url    =$order_data->get_view_order_url();
                    $order_total = $order_data->get_total();
                    $order_date = $order_data->order_date;
                    foreach ($order_data->get_items() as $item_id => $item_data) 
                    {
                      	$product = $item_data->get_product();                      
                      	$product_name = $product->get_name(); 
                       	$order_product_id = $product->get_id();
                       	$order_product_qty = $item_data['quantity']; 
                      	if($product_id == $order_product_id)
                      	{
                        ?>
	                        <li class="single_order">
	                        	<?php //var_dump($item_data['subtotal']);  ?>  

	                          <div class="order_no"><a target="blank" href="<?php echo $order_url; ?>"><?php echo $order_id; ?></a></div>
	                          <div class="order_date">
	                          	<?php  
	                          		$old_date_timestamp = strtotime($order_date);							
									               echo $today = date("F j, Y, g:i a",$old_date_timestamp);
								              ?>
	                          	</div>
	                          <div class="product_qty"><?php echo $order_product_qty; ?></div>
	                          <div class="product_total"><?php echo get_woocommerce_currency_symbol().$item_data['subtotal']; ?></div>
	                          <div class="order_total"><?php echo get_woocommerce_currency_symbol().$order_total; ?></div>  
	                        </li>
                        <?php              
                    	}
                   }
               }
                  ?>
                </div>
                </ul>
                </div>               
              </div>              
            </div></div> 