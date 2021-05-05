
<form action="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/autresapin" id="form_little" method="post">
         
   <ul class="hidden">
   {foreach from=$products item=product}
       <li data-id="{$product.id_product}" >
           <input type="radio" name="product" value="{$product.id_product}"  id="product_{$product.id_product}" /> 
           <label for="product_{$product.id_product}">{$product.name}</label>
       </li>
   {/foreach}
   </ul>
<div class="icon-tunnel">
        <div class="cercle_taille cercle"></div>
        {*<h2>{l s="Choisissez les accessoires de votre little ecosapin" mod='tunnelvente'}</h2>*}
   </div> 
<div class="clear"></div>
   <div class="description">
       {foreach from=$products item=product}
           <div class="description_{$product.id_product}" style="display: none">
               {$product.description}
           </div>
       {/foreach}
    </div>
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next-little hidden">next</button>
    </div>
   <div class="loading"></div>
</form>    
    
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/type";        
    var baseurl_tunnelvente_product = "{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/product";        
    $(function($){
        $('form#form_little').submit(function(event){
            event.preventDefault();
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){              
                $me.addClass(classe);
                
                    try{
                        $.ajax({
                        type: 'POST',
                        url: $me.attr("action"),
                        data: 'ajax=1&pot=0', 
                        dataType: 'json',
                        success: function(json) {
                                
                                if(json.hasError){
                                    
                                    $.each(json.errors,function(k,v){
                                        showError(v);
                                    });
                                }else{


                                    $('#resp_content').html(json.html);
                                    $('#my_errors').empty();
                                    ShowHideStep(json.numStep);
                                }
                                $me.removeClass(classe);
                            }
                        });
                    }catch(e){
                        $me.removeClass(classe);
                    }
                    
            }
           
        });
        
    });
</script> 
{literal}
<script type="text/javascript">
    $(function($){
          
        $('form#form_little .prev').click(function(event){
             //window.location.href = baseurl_tunnelvente;
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){              
                $me.addClass(classe);
                try{
                    $.ajax({
                    type: 'GET',
                    url: baseurl_tunnelvente,
                    data: 'ajax=1&back=2',
                    dataType: 'json',
                    success: function(json) {
                            if(json.hasError){
                                $.each(json.errors,function(k,v){
                                    showError(v);
                                });
                            }else{
                                $('#resp_content').html(json.html);
                                $('#my_errors').empty();
                                ShowHideStep(json.numStep);
                            }
                            $me.removeClass(classe);
                        }
                    });
                    }catch(e){
                        $me.removeClass(classe);
                    }
            }
        });                      
        
        $('form#form_little li label').click(function(){
           $('form#form_little li label').removeClass("checked");
           $(this).addClass("checked");
           // get product view
           var val = $(this).parent().find('input:radio').val(),$form = $('form#form_little'),classe= 'isactive';
           $('form#form_little .description').find("> div").hide()
                    .end()
                    .find(".description_"+val).show();
            if(!$form.hasClass(classe)){
                $form.addClass(classe);
            }
            if(parseInt(val) == 0){
                $('form#form_little').submit();
                return false;
            }
            try{
                $.ajax({
                type: 'POST',
                url: baseurl_tunnelvente_product,
                data: 'ajax=1&product='+val,
                dataType: 'json',
                success: function(json) {
                        if(json.hasError){
                            $.each(json.errors,function(k,v){
                                showError(v);
                            });
                        }else{
                            $('#blockProduct').html(json.html);
                            try {
                                if(json.myLittelEcosapin){
                                    $('.thirdCol').addClass('myLittelEcosapin');
                                }else{
                                    $('.thirdCol').removeClass('myLittelEcosapin');
                                }
                            }catch(e){
                                //console.info(e);
                            }
                        }
                        $form.removeClass(classe);
                    }
                });
            }catch(e){
                $form.removeClass(classe);
            }
        });
        
        if($('form#form_little li input').length){
            $('form#form_little li input:eq(0)').parents('li').find('label').click();
        }
    });
</script>
{/literal}
