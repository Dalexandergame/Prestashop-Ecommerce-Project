{if isset($autresapin) && $autresapin}
    <script type="text/javascript"> 
    $(function($){   
        try{
            $.ajax({
            type: 'GET',
            url: "{$urls.base_url}module/tunnelvente/type",
            data: 'ajax=1&back=2&npa={$npa}',
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
                }
            });
        }catch(e){
            
        }    
    });    
    </script> 
{else}
   
<form action="{$urls.base_url}module/tunnelvente/commande" id="form_accessoire" method="post">
    <h4>{l s="Choisissez vos accessoires" mod='tunnelvente'}</h4> 
   <ul>
   {foreach from=$products item=product}
       <li data-id="{$product.id_product}" >
           <input type="radio" name="product" value="{$product.id_product}"  id="product_{$product.id_product}" /> 
           <label for="product_{$product.id_product}">{$product.name}</label>
       </li>
   {/foreach}
        <li data-id="0" >
            <input type="radio" name="product" value="0"  id="product_0" /> 
            <label for="product_0">{l s="Non merci, je ne souhaite pas d’accessoire" mod='tunnelvente'}</label>
        </li>
   </ul>
    <div class="icon-tunnel">
        <div class="cercle_taille cercle"></div>
        {*<h2>{l s="Choisissez vos accessoires" mod='tunnelvente'}</h2>*}
   </div>       
   <div class="clear"></div>
   <div class="description">
       {foreach from=$products item=product}
           <div class="description_{$product.id_product}" style="display: none">
               {$product.description nofilter}
           </div>
       {/foreach}
    </div>
    <div class="btns_next_prev">
{*        <button type="button" class="prev">prev</button>*}
        <button type="submit" class="next">{l s="Commander" mod='tunnelvente'}</button>
    </div>
   <div class="loading"></div>
</form>    
    
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/taille";
    var baseurl_tunnelvente_product = "{$urls.base_url}module/tunnelvente/product";
    $(function($){
        $('form#form_accessoire').submit(function(event){
            event.preventDefault();
            var commandeUrl = "{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}";
            document.location.href = commandeUrl;
        });  
    });
</script> 
{literal}
<script type="text/javascript">
    $(function($){

        $('form#form_accessoire .prev').click(function(event){
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
        
        $('form#form_accessoire li label').click(function(){
           $('form#form_accessoire li label').removeClass("checked");
           $(this).addClass("checked");
           // get product view
           var val = $(this).parent().find('input:radio').val(),$form = $('form#form_accessoire'),classe= 'isactive';
           $('form#form_accessoire .description').find("> div").hide()
                    .end()
                    .find(".description_"+val).show();
            if(!$form.hasClass(classe)){
                $form.addClass(classe);
            }
            if(parseInt(val) == 0){
                $('form#form_accessoire').submit();
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
        
        if($('form#form_accessoire li input').length){
            $('form#form_accessoire li input:eq(0)').parents('li').find('label').click();
        }
    });
</script>
{/literal}
{/if}