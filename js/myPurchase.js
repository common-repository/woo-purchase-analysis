jQuery(document).ready(function($) 
{
    $('.single-year:last').css('display','block');
    //$('.year_list li:first').addClass('active');
    $('.easyPaginate').easyPaginate({
          paginateElement: 'li',
          elementsPerPage: 5,
          effect: 'climb'
    });
      
    $('.easyPaginateNav').css('width','100%');
    $(".year h3").click(function()
    {
      $(this).parent().find('.year_total').toggle();
    });
        
    select_category();
    filter_list();    

    // --------------- Graphp ---------------- //
    $('.year_details .single-year').each(function() 
    { 
        var amount = 0;
        var amount_array = [];
        var method_array = [];
        var colorList = [];
        var self = $(this);
        var id = $(self).attr('id');
        $(self).find('.payment_data').each(function() 
        {
            var amount = $(this).find('td .amount').text();
            var method = $(this).find('.method').text();         
            amount_array.push(amount);
            method_array.push(method);
            var items = Array('#DA5653','#ECC7B9','#9DC08B','#81A688','#3E6D73','#FB6B6B','#744150','#F68658','#F39C12','#5D4037');
            var item = items[Math.floor(Math.random()*items.length)];   
            colorList.push(item);
        });
         
        var config = {
                type: 'pie',
                data: {
                  datasets: [{
                    data: amount_array,
                    backgroundColor: colorList,
                    label: 'Dataset 1'
                  }],
                  labels: method_array
                },  
            };
        var temp = 'year_chart_'+ id;
        var ctx = document.getElementById(temp);
        var year_chart = new Chart(ctx, config);

        var category_list = [];
        var category_count = []; 
        $(self).find('.category-list li').each(function() 
        {
            var category = $(this).find('.category-name').text();
            var count = $(this).find('.count').text();
            category_list.push(category);
            category_count.push(count);
        });
        var barChartData = {
                labels: category_list,
                datasets: [{
                  label: 'Products',
                  backgroundColor: '#58B19F',
                  borderColor: '#fff',
                  borderWidth: 1,
                  data: category_count,
                }]

            }; 
        var temp1 = 'year_category_chart_'+id;
        var ctx = document.getElementById(temp1);
        var myBar = new Chart(ctx, {
                        type: 'bar',
                        data: barChartData,
                        options: {
                         /* responsive: true,*/
                          legend: {
                            position: 'top',
                          },
                          scales: {
                              yAxes: [{id: 'y-axis-1', position: 'left', ticks: {min: 0}}]
                            },
                          title: {
                            display: true,
                            text: ''
                          }
                        }
                      });
        var product_name = [];
        var product_qty = [];
        $(self).find('.product-details .product-data').each(function() 
        {
            var name = $(this).attr('data-name');
            var qty = $(this).attr('data-qty');
            product_name.push(name);
            product_qty.push(qty);
        });
        var barChartData = {
                labels: product_name,
                datasets: [{
                  label: 'Quantity',
                  backgroundColor: '#F45750',
                  borderColor: '#fff',
                  borderWidth: 1,
                  data: product_qty,
                }]

            };
         var temp2 = 'year_product_chart_'+id;
         
        var ctx1 = document.getElementById(temp2);
        console.log(ctx1);
        var myBar = new Chart(ctx1, {
                        type: 'bar',
                        data: barChartData,
                        options: {
                         /* responsive: true,*/
                          legend: {
                            position: 'top',
                          },
                          scales: {
                              yAxes: [{id: 'y-axis-1', position: 'left', ticks: {min: 0}}]
                            },
                          title: {
                            display: true,
                            text: ''
                          }
                        }
                      });
      });

    // ---------------- Graph ------------------//
    $(document).on('click', '.generate_chart' , function()
    {
      $('.single-year').css('display','none');
      $('.graph_section').css('display','none');
      var year = $('.year_list').val();      
      var graph_section = '.graph-section_'+year;
      $(graph_section).css('display','block');
    });

    $(document).on('change','.filter_year' , function()
    {
      select_year();
      filter_list();
      select_category();      
    });
    $(document).on('change','.filter_category' , function()
    {
        select_category(); 
        filter_list();     
    });

    $(document).on('change','.year_list' , function()
    {
        var year = $(this).val();
        var year_section = '#'+year;
        $('.single-year').css('display','none');
        $(year_section).css('display','block');
        $('.graph_section').css('display','none');
    });

    $(document).on('click', '.pagination-page' , function()
    {
      var page = $(this).attr('data-page');
      var admin_url = $('.admin-url').text();
      $.ajax({
              type: "POST",
              url: admin_url,
              data: {
                    action: 'acemp_product_content',
                    current_page: page
              },
              success: function (result) {
                    $('.product_list').html(result); 
              }
      });
    });
    $(document).on('click', '.product-container .pagination-page' , function()
    {
      
      var action = $(this).attr('data-action');
      var page = $(this).attr('data-page');
      console.log(action);
      var admin_url = $('.admin-url').text();
      var year_list = [];
      var category_list = [];
      $( ".filter_year_list li" ).each(function() 
      {
          if($(this).find('.filter_year').is(':checked'))
          {
              year_list.push($(this).find('.filter_year').val());
          }             
      });
      $( ".filter_category_list li" ).each(function() 
      {
          if($(this).find('.filter_category').is(':checked'))
          {
              category_list.push($(this).find('.filter_category').val());
          }           
      });
      $.ajax({
              type: "POST",
              url: admin_url,
              data: {
                    action: action,
                    current_page: page,
                    year: year_list,
                    category: category_list
              },
              success: function (result) {
                    $('.products.columns-4').html(result);
              }
      });
    });

    $(document).on('click', '.clear-filter' , function()
    {
      var admin_url = $('.admin-url').text();
      $('.filter_year').prop('checked', false);
      $('.filter_category').prop('checked', false);
        $.ajax({
              type: "POST",
              url: admin_url,
              data: {
                      action: 'acemp_select_nothing',
              },
              success: function (product) {
                      $('.product-container').html(product);
                      $('.select-category').html('');
                      $('.filters').html('');                      
                    // filter_list();
              }
        });
    });
    
    $(document).on('click', '.delete-filter.category' , function()
    {
      var self = $(this);
      var str = $(self).text();
      str = $.trim(str);
      $('.filter_category_list li').each(function() 
      {
          var text_val = $(this).find('.filter_category').val();
          if(text_val == str)
          {
            $(this).find('.filter_category').prop('checked', false);
            select_category();
            filter_list();
          }
      });
    });
    $(document).on('click', '.delete-filter.year' , function()
    {
      var self = $(this);
      var str = $(self).text();
      str = $.trim(str);
      $('.filter_year_list li').each(function() 
      {
          var text_val = $(this).find('.filter_year').val();
          if(text_val == str)
          {
            $(this).find('.filter_year').prop('checked', false);
            select_year();
            filter_list();
          }
      });
    });

    $(document).on('click', '.filter-button' , function()
    {
      $( ".mypurchase-filter .right" ).slideToggle( "slow", function() {
        // Animation complete.
      });
    });

function filter_list()
{
    $('.filter-list').html('');
    var year_list = [];
    var category_list = [];
    $('.filters').html('<div class="inner"><h4>Filters <span class="clear-filter">Clear All</span></h4><div class="filter-list"></div></div>');
    $( ".filter_year_list li" ).each(function() 
    {
      if($(this).find('.filter_year').is(':checked'))
      {
          var year = $(this).find('.filter_year').val();
          var year_add = '<span class="delete-filter year">'+year+' <i class="fa fa-close"> x </i></span>';
          $('.filter-list').append(year_add);
          year_list.push($(this).find('.filter_year').val());
      }             
    });
    $( ".filter_category_list li" ).each(function() 
    {
        if($(this).find('.filter_category').is(':checked'))
        {
            var category = $(this).find('.filter_category').val();
            var category_add = '<span class="delete-filter category">'+category+' <i class="fa fa-close"> x </i></span>';
            $('.filter-list').append(category_add);
            category_list.push($(this).find('.filter_category').val());
        }          
              
    });
    var year = year_list.length;
    var category = category_list.length;
    if(year == 0 && category == 0){
      $('.filters').html('');
    }
}
function select_year()
{
    var year_list = [];
    $( ".filter_year_list li" ).each(function() 
    {
      if($(this).find('.filter_year').is(':checked'))
      {
          year_list.push($(this).find('.filter_year').val());
      }             
    });
        
    var admin_url = $('.admin-url').text();
    var year_count = year_list.length;
    if(year_count == 0)
    {
         $.ajax({
              type: "POST",
              url: admin_url,
              data: {
                      action: 'acemp_select_nothing',
              },
              success: function (product) {
                      $('.products.columns-4').html(product);                 
              }
        });
         $.ajax({
              type: "POST",
              url: admin_url,
              data: {
                      action: 'acemp_all_category',
              },
              success: function (category) {
                      $('.select-category').html(category);                 
              }
        }); 
    }
    else{
        $.ajax({
                type: "POST",
                url: admin_url,
                data: {
                      action: 'acemp_select_category_based_year',
                      year: year_list
                },
                success: function (result) {
                        $('.select-category').html(result);
                        filter_list(); 
                }
        });
    }                 
}
function select_category()
{
    var year_list = [];
    var category_list = [];
    $( ".filter_year_list li" ).each(function() 
    {
        if($(this).find('.filter_year').is(':checked'))
        {
            year_list.push($(this).find('.filter_year').val());
        }             
    });
    $( ".filter_category_list li" ).each(function() 
    {
        if($(this).find('.filter_category').is(':checked'))
        {
            category_list.push($(this).find('.filter_category').val());
        }           
    });
    
    var count = category_list.length;
    var admin_url = $('.admin-url').text(); 
    var year_count = year_list.length;
    console.log(year_count);
    if(year_count == 0)
    {  
            
        $.ajax({
                type: "POST",
                url: admin_url,
                beforeSend: function() {
                  
                    $('.products.columns-4').html(''); 
                    $('.loader-gif').css('display','block');
                },
                data: {
                        action: 'acemp_select_nothing',
                        category: category_list
                },
                success: function (product) {
                          $('.products.columns-4').html(product);               
                },
                complete: function() {
                     $('.loader-gif').css('display','none');
                }
                
        });
        /*$.ajax({
              type: "POST",
              url: admin_url,
              data: {
                      action: 'all_category',
              },
              success: function (category) {
                      $('.select-category').html(product);                 
              }
        });*/

    }
    if(year_count > 0)
    {
        if(count > 0){
          $.ajax({
                  type: "POST",
                  url: admin_url,
                  beforeSend: function() {
                    $('.products.columns-4').html(''); 
                    $('.loader-gif').css('display','block');
                  },
                  data: {
                        action: 'acemp_select_category_based_year',
                        category: category_list,
                        year: year_list
                  },
                  success: function (product) {
                      $('.products.columns-4').html(product);                 
                  },
                  complete: function() {
                     $('.loader-gif').css('display','none');
                  }
          }); 
        }
        else{
              $.ajax({
                  type: "POST",
                  url: admin_url,
                  beforeSend: function() {
                    // setting a timeout
                    $('.products.columns-4').html('');
                    $('.loader-gif').css('display','block');
                  },
                  data: {
                        action: 'acemp_select_year_only',
                        year: year_list
                  },
                  success: function (product) {
                            $('.products.columns-4').html(product);                 
                  },
                  complete: function() {
                     $('.loader-gif').css('display','none');
                  }
                });
        }
    }   
    filter_list();    
}
});

function openCity(evt, cityName){
    
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    jQuery('.graph_section').css('display','none');
    evt.currentTarget.className += " active";
}