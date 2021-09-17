<style>
    /*div .step7Col {*/
        /*overflow-x: hidden;*/
        /*overflow-y: scroll;*/
    /*}*/
    .btns_next_prev .next {
        box-shadow: 0px 0px 10px 2px gold;
    }
</style>

{if isset($autresapin) && $autresapin}
    <script type="text/javascript">

    $(function($){   
        var goto = null;
        {if $autresapin == 1 }
            goto = '{$urls.base_url}module/tunnelventeabies/type';
        {else}
            goto = '{$urls.base_url}fr/stock-pack/93-my-little-ecosapin.html';
            window.location = goto;
        {/if}
        console.log(goto);
        try{
            $.ajax({
            type: 'GET',
            url: goto,
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
                }
            });
        }catch(e){
        }    
    });    
    </script> 
{else}

<form action="{$urls.base_url}module/tunnelventeabies/commande" id="form_accessoire" method="post">
    <h4>{l s="Choisissez vos accessoires" mod='tunnelventeabies'}</h4>
   <ul>
   {foreach from=$products item=product}
       <li data-id="{$product.id_product}" >
           <input type="radio" name="product" value="{$product.id_product}"  id="product_{$product.id_product}" />
           <label for="product_{$product.id_product}">{$product.name}</label>
       </li>
   {/foreach}
       <li>
           <input type="radio" name="product" value="0"  id="noChoice" />
           <label for="noChoice">{l s="Je ne souhaite pas d'accessoire"  mod='tunnelventeabies'}</label>
       </li>
   </ul>
    <div class="icon-tunnel">
        <div class="cercle_taille cercle"></div>
        {*<h2>{l s="Choisissez vos accessoires" mod='tunnelventeabies'}</h2>*}
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
       <ul style="padding: 0 15px;float: right">
            <li data-id="0" >
                <input type="submit" name="product" value="0"  id="product_0" />
                <label for="product_0" class="passe-commande">{l s="Passer à la commande" mod='tunnelventeabies'}</label>
            </li>
       </ul>
        {*<button type="button" class="prev">prev</button>*}
        {*<button type="submit" class="next" style="background: #cbdeb1 url({$urls.img_url}cercle-acce.png) no-repeat center center">{l s="Commander" mod='tunnelventeabies'}</button>*}
    </div>
   <div class="loading"></div>
</form>    
    
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/taille";
    var baseurl_tunnelvente_product = "{$urls.base_url}module/tunnelventeabies/product";
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
        //current
        $('#blockProduct').removeClass('hidden');
        //previous
        $('.container_newsapin').addClass('hidden');


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
           var val = $(this).parent().find('input:radio').val(),$form = $('form#form_accessoire'),classe= 'isactive';
           $('form#form_accessoire .description').find("> div").hide()
                    .end()
                    .find(".description_"+val).show();
            if(!$form.hasClass(classe)){
                $form.addClass(classe);
            }
            if(parseInt(val) == 0){
                $('div .loading').addClass("hidden");
                return false;
            }else{
                $('div .loading').removeClass("hidden");
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