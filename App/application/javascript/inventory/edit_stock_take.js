$(function(){
	var scrolloptions = {
		autoHideScrollbar:true,
		autoExpandScrollbar : true,
		theme:"dark-2",
		scrollButtons:{enable:false,},				
	}
	var stocktake = {
		selection : {},
		settings: {
			'db_name':'stocktake',
			'db_version' : 1,
			'product_url' : $('#product_url').val(),	
			'stocktakes_url': $('#stocktakes_url').val(),
			'stocktake_products_url': $('#stocktake_products_url').val(),
			'ST_post_url': $('#ST_post_url').val(),
			'stocktake_counted_products' : $('#stocktake_counted_products').val(),
			'stocktake_id': $('#stocktake_id'),
			'search_prd' : $('#search_prd'),
			'count_this' : $('#count_this'),
			'count_tb' : $('#count_tb'),
			'product_count_post_url' : $('#product_count_post_url').val(),
			'max_product_count' : 500,
			'csrf_token' : $("input[name=csrf_test_name]").val()
		},
		open_init_model:function(cust_val){
			if (!$('#init_model').length) {
				$cont = '<div id="init_model" class="modal modal-lg fade container" role="dialog" style="padding-top:15%">'
				$cont += '<div class="modal-content">'
				$cont += '<div class="modal-body text-center">'+
							'<h1><i class="text-success fa fa-circle-o-notch fa-spin fa-1x"></i></h1>'+
							'<h4>Please hold tight while we initialise your stocktake products...</h4>'+
							'<h5>'+cust_val+'</h5>'+
						  '</div>'						  
				$cont += '</div></div>'
				$('body').append($cont);
			} 
			$('#init_model').modal({show:true,backdrop: 'static'});
		},
		prd_not_found_model:function(){
			var tb = this.settings.search_prd
			if (!$('#prd_model').length) {
				$cont = '<div id="prd_model" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
				$cont += '<div class="modal-content">'
				$cont += '<div class="modal-header"><h3 class="modal-title">Oops...</h3></div>'
				$cont += '<div class="modal-body text-center">'+
							'<h4><i class="fa fa-cubes fa-fw"></i>Product not found</h4>'+
						  '</div>'
				$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false"><i class="fa fa-remove fa-fw"></i>Close</button>'
				$cont += '</div></div>'
				$('body').append($cont);
			} 
			$('#prd_model').modal({show:true,backdrop: 'static'}).on('hidden.bs.modal', function () {
				tb.val('').focus()
			});
		},
		prd_max_model:function(){
			var tb = this.settings.search_prd
			if (!$('#prd_model').length) {
				$cont = '<div id="prd_model" class="modal modal-sm fade container" role="dialog" style="padding-top:15%">'
				$cont += '<div class="modal-content">'
				$cont += '<div class="modal-header"><h3 class="modal-title">Oops...</h3></div>'
				$cont += '<div class="modal-body text-center">'+
							'<div>Maximum '+this.settings.max_product_count+' products can be counted at a time. But you can still count existing products</div>'+
						  '</div>'
				$cont += '<div class="modal-footer"><button class="btn btn-danger" data-dismiss="modal" aria-hidden="false"><i class="fa fa-remove fa-fw"></i>Close</button>'
				$cont += '</div></div>'
				$('body').append($cont);
			} 
			$('#prd_model').modal({show:true,backdrop: 'static'}).on('hidden.bs.modal', function () {
				tb.val('').focus()
			});
		},
		wait_state_init_model:function(){
			$('#init_model').modal('show');
		},
		close_init_model:function(){
			$('#init_model').modal('hide');
		},
		createUUID: function() { // waiting: change this later to mac address based time based uuid
            var d = Date.now();
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c === 'x' ? r : (r & 0x7 | 0x8)).toString(16);
            });
            return uuid;
        },
		post_product_count: function(post) {
			$.ajax({ 
				url: this.settings.product_count_post_url,
				data: post,
				type: "POST",
				success: function(data){
					var resp = $.parseJSON(JSON.stringify(data))
					if(resp.status == 'success')
					{
						console.log('Remote stock take product updated')
					}
				},
				error: function(xhr,textStatus, errorThrown) {
					console.log(errorThrown);
				} 		
		   });			
		}
	}
	
	var db = new Dexie(stocktake.settings.db_name);		
	db.version(stocktake.settings.db_version).stores({
		products: 'id,name,sku',
		stocktakes: 'id',
		substocktakes: 'id, stocktakeid',
		stocktakecounts: 'id,bystocktakeid',
		stocktakeproducts: 'id, stocktake_id, product_id'
	});	
	db.open();

	loader = '<div id="loader_cart"><h1 align="center"><i class="fa fa-spinner fa-spin fa-1x"></i></h1></div>'
	$('#cart_div #cart_list').append(loader)
	loader_review = '<tr id="loader_review"><td colspan="3" align="center"><h1><i class="fa fa-spinner fa-spin fa-1x"></i></h1></td></tr>'
	$('#review_div #append_review').append(loader_review)	
	
	db.on('ready',function(){
		stocktake.open_init_model('')
        return new Dexie.Promise(function (resolve, reject) {
			$.ajax(stocktake.settings.product_url, {
				type: 'get',
				contentType: 'application/json',
				dataType: 'json',
				error: function (xhr, textStatus) {
					reject(textStatus);
				},
				success: function (data) {
					resolve(data);
				}
			});
		}).then(function (data) {
			return db.transaction('rw', db.products, function () {
				db.products.clear()
				$.each(data, function(i, item) {
					var time = new Date()
					var items = {
						id: i,
						name: item.name,
						sku: item.sku,
						handle: item.handle,
						created_at: item.created_at,
						updated_at: item.updated_at,
						track_inventory: item.track_inventory,
						scale: item.scale,
						cost_price: item.cost_price,
						retail_price: item.retail_price,
						expected: item.expected,
						status_code: item.status_code,
						timestamp: time.getTime(),
						is_variant_product : item.is_variant_product
					}						
					db.products.put(items)
				});
  
			});
		}).then(function () {
			return new Dexie.Promise(function (resolve, reject) {
				$.ajax(stocktake.settings.stocktakes_url, {
					type: 'get',
					contentType: 'application/json',
					dataType: 'json',
					error: function (xhr, textStatus) {
						reject(textStatus);
					},
					success: function (data) {
						resolve(data);
					}
				});
			}).then(function (data) {
				db.stocktakes.clear()
				$.each(data, function(i, item) {
					var items = {
						id: i,
						name: item.name,
						created_at: item.created_at,
						status_code: item.status_code,
						status_id: item.status_id,
						outlet_name: item.outlet_name,
						outlet_id: item.outlet_id
					};
					db.stocktakes.put(items)
				});
			}).then(function() {
				return new Dexie.Promise(function (resolve, reject) {
					$.ajax(stocktake.settings.stocktake_products_url, {
						type: 'get',
						contentType: 'application/json',
						dataType: 'json',
						error: function (xhr, textStatus) {
							reject(textStatus);
						},
						success: function (data) {
							resolve(data);
						}
					});
				}).then(function(data){
					db.substocktakes.clear()
					$.each(data, function(i, item) {
						var time = new Date()
						var items = {
							id: i,
							stocktakeid: item.stock_take_id,
							product_id: item.product_id,
							variant_id: item.variant_id,
							expected: item.expected,
							counted: item.counted,
							cost_gain: item.cost_gain,
							cost_loss: item.cost_loss,
							count_gain: item.count_gain,
							count_loss: item.count_loss
						};
						db.substocktakes.put(items)
					});
				}).then(function() {
					return new Dexie.Promise(function (resolve, reject) {
						$.ajax(stocktake.settings.stocktake_counted_products, {
							type: 'get',
							contentType: 'application/json',
							dataType: 'json',
							error: function (xhr, textStatus) {
								reject(textStatus);
							},
							success: function (data) {
								resolve(data);
							}
						});
					}).then(function(data){
						// clear and refresh the table
						db.stocktakeproducts.clear()
						var $elm = ''
						take_id = stocktake.settings.stocktake_id.val()
						var time = new Date()		
						$.each(data, function(i, item) {
							var items = {
								id: item.id,
								product_id: item.product_id,
								stocktake_id: take_id,
								name: item.name,
								expected: item.expected,
								counted: item.counted,
								cost_gain: item.cost_gain,
								cost_loss: item.cost_loss,
								count_gain: item.count_gain,
								count_loss: item.count_loss
							}
							db.stocktakeproducts.put(items)
							if(parseFloat(item.expected) <= parseFloat(item.counted))
							{
								var backg = '#dcf8c6', border = 'solid 1px #ddd', color = '#666'
							} else {
								var backg = '#da7673', border = 'solid 1px #d65b57', color = '#fff'
							}
							$elm += '<tr class="all_products"  id="'+item.product_id+'" data-st-index="'+item.id+'">'
								$elm += '<td class="actived">'+item.name.replace("/","<h6>")+'</td>'
								$elm += '<td class="expected_stat">'+item.expected+'</td>'
								$elm += '<td class="count_stat" style="background:'+backg+'; border:'+border+';  color: '+color+'">'+item.counted+'</td>'
							$elm += '</tr>'
							
						});
						if($elm.length > 0)
						{
							$('#review_div #append_review').append($elm)	
						}
						$('#loader_review').remove()
					}).then(function() {
						take_id = stocktake.settings.stocktake_id.val()
						db.stocktakecounts
						  .where('bystocktakeid')
						  .equalsIgnoreCase(take_id)
						  .sortBy("placement")
						  .then(function(products){
							  if(products.length > 0)
							  {
								  $.each(products,function(v,i){
									  $elm = '<div class="counted_row">'
									  $elm += '<span class="badge">'+i.counted+'</span>&nbsp;' 
									  $elm += i.name.replace("/","<h6>")
									  $elm += '<div class="pull-right"><button type="button" data-prd-id="'+i.product_id+'" data-st-id="'+i.bystocktakeid+'" id="'+i.id+'" class="btn btn-xs btn-danger cancel_count">&times;</button></div>'
									  $elm += '</div>'
									  $('#cart_div #cart_list').append($elm);
								  });
							  }
							  $('#loader_cart').remove()
							  $("#cart_list").mCustomScrollbar(scrolloptions);
							  
						  });
						  stocktake.close_init_model()
					});
	
				})
			});
		});

    },true);

	stocktake.settings.search_prd.autocomplete({
		delay: 1000,	
		autoFocus: true,
		open:function(e,ui){
			$('.ui-menu').css({'overflow-y' : 'hidden'})
			$(".ui-autocomplete").mCustomScrollbar({
				setHeight:182,
				theme:"dark-thick",
				scrollButtons:{enable:true,},				
				autoExpandScrollbar:true
			});
		},		
		response:function(e,ui){
			$(".ui-autocomplete").mCustomScrollbar("destroy");
		},
		focus:function(e,ui){
			if(!ui.item){
				var first=$(".ui-autocomplete li:first");
				first.trigger("mouseenter");
				$(this).val(first.data("uiAutocompleteItem").label);
			}
			var el=$(".ui-state-focus").parent();
			if(!el.is(":mcsInView") && !el.is(":hover")){
				$(".ui-autocomplete").mCustomScrollbar("scrollTo",el,{scrollInertia:0,timeout:0});
			}
		},
		close:function(e,ui){
			$(".ui-autocomplete").mCustomScrollbar("destroy");
		},		
		source: function (request, response) {
			db.transaction('rw', db.products, function() {
				return db.products
				  .where('sku')
				  .startsWithIgnoreCase(stocktake.settings.search_prd.val())
				  .or('name')
				  .startsWithIgnoreCase(stocktake.settings.search_prd.val())	
				  .limit(10)
				  .toArray()
				  .then(function(products){
					 response($.map(products, function (v,i) {
						 return {
							 label: v.name,
							 value: v.name,
							 id: v.id,
							 sku: v.sku,
							 cost_price:v.cost_price,
							 retail_price:v.retail_price,
							 expected:v.expected,
							 is_variant_product:v.is_variant_product,
						 };
					 }));				  
				  });
			});
		},
		select: function(event, ui) { 
			event.preventDefault();
			var stocktake_id = stocktake.settings.stocktake_id.val()
			var retail_price = ui.item.retail_price
			var cost_price = ui.item.cost_price
			var name = ui.item.value
			var product_id = ui.item.id
			var expected = ui.item.expected
			var sku = ui.item.sku
			var is_variant_product = ui.item.is_variant_product

			search_tb = stocktake.settings.search_prd

			return new Dexie.Promise(function(resolve, reject) {
				db.stocktakeproducts.where('stocktake_id').equalsIgnoreCase(stocktake_id).count().then(function(count){
					resolve(count)
				});
			}).then(function(count){
				if(count < stocktake.settings.max_product_count) // limit to count 500 products for 1 stocktake
				{
					return new Dexie.Promise(function (resolve, reject) {
						db.stocktakeproducts.where('product_id').equalsIgnoreCase(product_id).count().then(function(count){
							resolve(count)
						});
					}).then(function(count){
						if(count > 0)
						{
							return new Dexie.Promise(function (resolve, reject) {
								db.stocktakeproducts.where('product_id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(products){
									resolve(products);
								});
							}).then(function(products){
								stocktake.selection['main'] = products[0]						
							});
							
						} else {
							var uuid = stocktake.createUUID()
							var items = {
								id: uuid,
								product_id: product_id,
								stocktake_id: stocktake_id,
								name: name,
								expected: expected,
								counted: 0,
								cost_gain: 0,
								cost_loss: 0,
								count_gain: 0,
								count_loss: 0
							}	
							db.stocktakeproducts.put(items).then(function(){
								var post = {
									'index'	: uuid,
									'take_id' : stocktake_id,
									'product_id' : product_id,
									'is_variant_product' : is_variant_product,
									'expected' : expected,
									'counted' : 0,
									'cost_gain' : 0,
									'cost_loss' : 0,
									'count_gain' : 0,
									'count_loss' : 0,
									'csrf_test_name' : stocktake.settings.csrf_token
								}
								return $.ajax({
									url: stocktake.settings.product_count_post_url,
									data: post,
									type: 'post'
								});								
							}).then(function(){
								stocktake.selection['main'] = items
								$elm = '<tr class="all_products"  id="'+product_id+'" data-st-index="'+stocktake_id+'">'
									$elm += '<td class="actived">'+name.replace("/","<h6>")+'</td>'
									$elm += '<td class="expected_stat">'+expected+'</td>'
									$elm += '<td class="count_stat">0</td>'
								$elm += '</tr>'
								if($('table #append_review tr').length > 0)
								{
									$($elm).insertBefore('table > #append_review > tr:first');
								} else {
									$('table > #append_review').append($elm)								
								}
							});
						}
					}).finally(function(){
						return new Dexie.Promise(function (resolve, reject) {
							db.products.where('id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(details){
								resolve(details)
							});
						}).then(function(details) {
							stocktake.selection['details'] = details[0]
							search_tb.val('').focus()		
							stocktake.settings.count_this.removeClass('disabled')		
							stocktake.settings.count_tb.val('1').focus().select()
							
							$('#review_div #append_review .all_products').removeClass('selected')
							$('#review_div #append_review tr#'+product_id).addClass('selected')
							var scroll_point = $('#review_div table tbody#append_review tr#'+product_id).offset().top - ($('#review_div table tbody#append_review').height()/2)
							//$('#review_div').animate({scrollTop: scroll_point},1000);
							$('#review_div').mCustomScrollbar("scrollTo",$('#review_div').find('.mCSB_container').find('tr#'+product_id));
						});
					});
				} else {
					stocktake.prd_max_model()
				}
			});
		}
	});
	$('#review_div #append_review').on("click", ".all_products", function (event) {
		self = $(this)
		product_id = self.attr('id')		
		return new Dexie.Promise(function (resolve, reject) {
			db.stocktakeproducts.where('product_id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(products){
				resolve(products)
			});
		}).then(function(products) {
			stocktake.selection['main'] = products[0]
		}).then(function(){
			return new Dexie.Promise(function(resolve, reject) {
				db.products.where('id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(details){
					resolve(details)
				});
			}).then(function(details) {
				stocktake.selection['details'] = details[0]
			}).then(function(){
				$('#review_div #append_review .all_products').removeClass('selected')
				self.addClass('selected')
				stocktake.settings.count_tb.val('1').focus().select()
				stocktake.settings.count_this.removeClass('disabled')
			});
		});
	});
	$('#count_this').on('click',function(){ 
		var time = new Date()
		var uuid = stocktake.createUUID()		
		var count_tb = isNaN(stocktake.settings.count_tb.val()) ? 0 : parseFloat(stocktake.settings.count_tb.val())
		count_tb = (count_tb % 1 == 0) ? count_tb : parseFloat(count_tb).toFixed(3)
		if(isNaN(stocktake.settings.count_tb.val()))
		{
			stocktake.settings.count_tb.val(1)	
		}
		var counts = {
			id: uuid,
			counted: count_tb,
			bystocktakeid: stocktake.settings.stocktake_id.val(),
			product_id: stocktake.selection['details']['id'],
			name: stocktake.selection['details']['name'],
			placement: time.getTime()
		};
		db.stocktakecounts.put(counts).then(function(){
			$elm = '<div class="counted_row">'
			$elm += '<span class="badge">'+count_tb+'</span>&nbsp;' 
			$elm += stocktake.selection['details']['name'].replace("/","<h6>")
			$elm += '<span class="pull-right"><button type="button" data-st-id="'+stocktake.settings.stocktake_id.val()+'" data-prd-id="'+stocktake.selection['details']['id']+'" id="'+uuid+'" class="btn btn-xs btn-danger cancel_count">&times;</button></span>'
			$elm += '</div>'
			$("#cart_list").mCustomScrollbar(scrolloptions);
			if($('#cart_div #cart_list .counted_row').length > 0)
			{
				$('#cart_div #cart_list .counted_row').eq(0).before($elm)
			} else {
				$('#cart_div #cart_list').append($elm)
			}
			$('#cart_list').mCustomScrollbar("scrollTo","top");
			
			
			expected = parseFloat($('.all_products#'+stocktake.selection['details']['id']+' td.expected_stat').text())
			count_before = parseFloat($('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').text())
			count_after = parseFloat(count_before) + parseFloat(count_tb)
			count_after = (count_after % 1 == 0) ? count_after : parseFloat(count_after).toFixed(3)
			
			$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').text(count_after)
			if(expected <= count_after)
			{
				$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').css({'background' : '#dcf8c6','border' : 'solid 1px #ddd','color' : '#666'})
			} else {
				$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').css({'background' : '#d9534f','border' : 'solid 1px #d9534f','color' : '#fff'})
			}
			var cost_gain = count_after > stocktake.selection['details']['expected'] ? (stocktake.selection['details']['retail_price'] * count_after) - (stocktake.selection['details']['expected'] * stocktake.selection['details']['retail_price']) : 0
			var cost_loss = count_after < stocktake.selection['details']['expected'] ? (stocktake.selection['details']['expected'] * stocktake.selection['details']['retail_price']) - (stocktake.selection['details']['retail_price'] * count_after) : 0		
			var count_gain = count_after > stocktake.selection['details']['expected'] ? (count_after - stocktake.selection['details']['expected']) : 0
			var count_loss = count_after < stocktake.selection['details']['expected'] ? (stocktake.selection['details']['expected'] - count_after) : 0
			
			var post = {
				'index'	: stocktake.selection['main']['id'],
				'take_id' : stocktake.selection['main']['stocktake_id'],
				'product_id' : stocktake.selection['details']['id'],
				'is_variant_product' : stocktake.selection['details']['is_variant_product'],
				'expected' : stocktake.selection['details']['expected'],
				'counted' : count_after,
				'cost_gain' : cost_gain,
				'cost_loss' : cost_loss,
				'count_gain' : count_gain,
				'count_loss' : count_loss,
				'csrf_test_name' : stocktake.settings.csrf_token
			}
			stocktake.post_product_count(post)
			
		});


	});
	stocktake.settings.count_tb.keypress(function(e){
        if(e.which == 13 && !isNaN(stocktake.settings.count_tb.val())){
            $('#count_this').click();
        }
    });	
	$('body').on("click",".counted_row .cancel_count",function(){
		var count_id = $(this).attr('id')
		var take_id = $(this).attr('data-st-id')
		var product_id = $(this).attr('data-prd-id')
		var self = $(this)
		//self.parent().parent().parent().hide(500, function(){ self.remove(); });
		self.parents('.counted_row').hide(500, function(){ self.remove(); });

		db.stocktakecounts.where('id').equalsIgnoreCase(count_id)
		.delete()
		.then(function (deleteCount) {
			db.products.where('id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(details){
				var count = isNaN(self.parents('.counted_row').find('.badge').text()) ? 0 : parseFloat(self.parents('.counted_row').find('.badge').text())
				var count_before = parseFloat($('.all_products#'+product_id+' td.count_stat').text())
				var count_after = count_before - count
				count_after = (count_after % 1 == 0) ? count_after : parseFloat(count_after).toFixed(3)
				var expected = parseFloat($('.all_products#'+product_id+' td.expected_stat').text())
	
				$('.all_products#'+product_id+' td.count_stat').text(count_after)
				if(expected <= count_after)
				{
					$('.all_products#'+product_id+' td.count_stat').css({'background' : '#dcf8c6','border' : 'solid 1px #ddd','color' : '#666'})
				} else {
					$('.all_products#'+product_id+' td.count_stat').css({'background' : '#d9534f','border' : 'solid 1px #d9534f','color' : '#fff'})
				}
		
				var cost_gain = count_after > details[0]['expected'] ? (details[0]['retail_price'] * count_after) - (details[0]['expected'] * details[0]['retail_price']) : 0
				var cost_loss = count_after < details[0]['expected'] ? (details[0]['expected'] * details[0]['retail_price']) - (details[0]['retail_price'] * count_after) : 0		
				var count_gain = count_after > details[0]['expected'] ? (count_after - details[0]['expected']) : 0
				var count_loss = count_after < details[0]['expected'] ? (details[0]['expected'] - count_after) : 0
	
				db.stocktakeproducts.where('product_id').equalsIgnoreCase(product_id).limit(1).toArray().then(function(products){
					var post = {
						'index'	: products[0]['id'],
						'take_id' : products[0]['stocktake_id'],
						'product_id' : details[0]['id'],
						'is_variant_product' : details[0]['is_variant_product'],
						'expected' : details[0]['expected'],
						'counted' : count_after,
						'cost_gain' : cost_gain,
						'cost_loss' : cost_loss,
						'count_gain' : count_gain,
						'count_loss' : count_loss,
						'csrf_test_name' : stocktake.settings.csrf_token
					}
					stocktake.post_product_count(post)
				});
			});
		 });
	});

	var pressed = false; 
	var chars = []; 
	stocktake.settings.search_prd.keypress(function(e) {
		if (e.which >= 48 && e.which <= 90) {
			chars.push(String.fromCharCode(e.which));
		}
		if (pressed == false) 
		{
			setTimeout(function()
			{
				if(chars.length >= 5) 
				{
					stocktake.settings.search_prd.autocomplete("disable");	
					var stocktake_id = stocktake.settings.stocktake_id.val()
					var sku = chars.join("");
					return new Dexie.Promise(function(resolve, reject) {
						db.stocktakeproducts.where('stocktake_id').equalsIgnoreCase(stocktake_id).count().then(function(count){
							resolve(count)
						});
					}).then(function(count){
						if(count < stocktake.settings.max_product_count) // limit to count 500 products for 1 stocktake
						{
							return new Dexie.Promise(function (resolve, reject) {
								db.products.where('sku').equalsIgnoreCase(sku).limit(1).toArray().then(function(details){
									resolve(details);
								});
							}).then(function(details){
								if(typeof details[0] === "undefined")
								{
									stocktake.prd_not_found_model()
								}		
								stocktake.selection['details'] = details[0]
								var product_id = details[0]['id']
								var retail_price = details[0]['retail_price']
								var cost_price = details[0]['cost_price']
								var name = details[0]['name']
								var expected = details[0]['expected']
								var is_variant_product = details[0]['is_variant_product']
								return new Dexie.Promise(function(resolve, reject){
									db.stocktakeproducts.where('product_id').equalsIgnoreCase(details[0]['id']).count().then(function(count){
										resolve(count)
									});
								}).then(function(count){
									if(count < 1)
									{
										$('#review_div #append_review .all_products').removeClass('selected')
										$elm = '<tr class="all_products selected"  id="'+product_id+'" data-st-index="'+stocktake_id+'">'
											$elm += '<td class="actived">'+name.replace("/","<h6>")+'</td>'
											$elm += '<td class="expected_stat">'+expected+'</td>'
											$elm += '<td class="count_stat">0</td>'
										$elm += '</tr>'
										
										if($('table #append_review tr').length > 0)
										{
											$($elm).insertBefore('table > #append_review > tr:first');
										} else {
											$('table > #append_review').append($elm)							
										}

										var uuid = stocktake.createUUID()
										var init_counted = 1, init_cost_gain = 0, init_cost_loss = 0, init_count_gain = 0, init_count_loss = 0;
										var items = {
											id: uuid,
											product_id: product_id,
											stocktake_id: stocktake_id,
											name: name,
											expected: expected,
											counted: init_counted,
											cost_gain: init_cost_gain,
											cost_loss: init_cost_loss,
											count_gain: init_count_gain,
											count_loss: init_count_loss
										}				
										db.stocktakeproducts.put(items).then(function(){
											var post = {
												'index' : uuid,
												'take_id' : stocktake_id,	
												'is_variant_product' : is_variant_product,
												'product_id' : product_id,
												'expected' : expected,
												'counted' : init_counted,
												'cost_gain' : init_cost_gain,
												'cost_loss' : init_cost_loss,
												'count_gain' : init_count_gain,
												'count_loss' : init_count_loss,
												'csrf_test_name' : stocktake.settings.csrf_token
											}
											stocktake.selection['main'] = items
											return $.ajax({ 
												url: stocktake.settings.ST_post_url,
												data: post,
												type: "POST",
										   });
										});
									} else {
										return new Dexie.Promise(function(resolve, reject){
											db.stocktakeproducts.where('product_id').equalsIgnoreCase(details[0]['id']).limit(1).toArray().then(function(products){
												resolve(products)
											});
										}).then(function(products){
											stocktake.selection['main'] = products[0]
											//highlight the product
											$('#review_div #append_review .all_products').removeClass('selected')
											$('#review_div #append_review tr#'+product_id).addClass('selected')
											var scroll_point = $('#review_div table tbody#append_review tr#'+product_id).offset().top - ($('#review_div table tbody#append_review').height()/2)
											//$('#review_div').animate({scrollTop: scroll_point},1000);
											$('#review_div').mCustomScrollbar("scrollTo",$('#review_div').find('.mCSB_container').find('tr#'+product_id));
										});
									}

								}).then(function(){
									// mark inventory count
									var time = new Date()
									var uuid = stocktake.createUUID()		
									var count_tb = 1
									var counts = {
										id: uuid,
										counted: count_tb,
										bystocktakeid: stocktake.settings.stocktake_id.val(),
										product_id: stocktake.selection['details']['id'],
										name: stocktake.selection['details']['name'],
										placement: time.getTime()

									};
									db.stocktakecounts.put(counts)
									// Add to selection									
									$elm = '<div class="counted_row">'
									$elm += '<span class="badge">'+count_tb+'</span>&nbsp;' 
									$elm += stocktake.selection['details']['name'].replace("/","<h6>")
									$elm += '<span class="pull-right"><button type="button" data-st-id="'+stocktake.settings.stocktake_id.val()+'" data-prd-id="'+stocktake.selection['details']['id']+'" id="'+uuid+'" class="btn btn-xs btn-danger cancel_count">&times;</button></span>'
									$elm += '</div>'
									$("#cart_list").mCustomScrollbar(scrolloptions);
									if($('#cart_div #cart_list .counted_row').length > 0)
									{
										$('#cart_div #cart_list .counted_row').eq(0).before($elm)
									} else {
										$('#cart_div #cart_list').append($elm)
									}
									$('#cart_list').mCustomScrollbar("scrollTo","top");

									//ajax post
									expected = parseFloat($('.all_products#'+stocktake.selection['details']['id']+' td.expected_stat').text())
									count_before = parseFloat($('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').text())
									count_after = parseFloat(count_before) + parseFloat(count_tb)
									count_after = (count_after % 1 == 0) ? count_after : parseFloat(count_after).toFixed(3)
									
									$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').text(count_after)
									if(expected <= count_after)
									{
										$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').css({'background' : '#dcf8c6','border' : 'solid 1px #ddd','color' : '#666'})
									} else {
										$('.all_products#'+stocktake.selection['details']['id']+' td.count_stat').css({'background' : '#da7673','border' : 'solid 1px #d65b57','color' : '#fff'})
									}
									var cost_gain = count_after > stocktake.selection['details']['expected'] ? (stocktake.selection['details']['retail_price'] * count_after) - (stocktake.selection['details']['expected'] * stocktake.selection['details']['retail_price']) : 0
									var cost_loss = count_after < stocktake.selection['details']['expected'] ? (stocktake.selection['details']['expected'] * stocktake.selection['details']['retail_price']) - (stocktake.selection['details']['retail_price'] * count_after) : 0		
									var count_gain = count_after > stocktake.selection['details']['expected'] ? (count_after - stocktake.selection['details']['expected']) : 0
									var count_loss = count_after < stocktake.selection['details']['expected'] ? (stocktake.selection['details']['expected'] - count_after) : 0
									
									var post = {
										'index'	: stocktake.selection['main']['id'],
										'take_id' : stocktake.selection['main']['stocktake_id'],
										'product_id' : stocktake.selection['details']['id'],
										'is_variant_product' : stocktake.selection['details']['is_variant_product'],
										'expected' : stocktake.selection['details']['expected'],
										'counted' : count_after,
										'cost_gain' : cost_gain,
										'cost_loss' : cost_loss,
										'count_gain' : count_gain,
										'count_loss' : count_loss,
										'csrf_test_name' : stocktake.settings.csrf_token
									}
									stocktake.post_product_count(post)
									stocktake.settings.count_this.removeClass('disabled')
									
								});
							}).finally(function(){
								chars = [];
								pressed = false;
								stocktake.settings.search_prd.val(sku).focus().select()
							});
						} else {
							stocktake.prd_max_model()
						}
					});
				} else {
					stocktake.settings.search_prd.autocomplete("enable");						
				}
			},500);
		}
		pressed = true;
	});
	$("#review_div").mCustomScrollbar(scrolloptions);
	
});