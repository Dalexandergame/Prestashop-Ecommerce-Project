<form action="{$urls.base_url}module/tunnelvente/accesoire" id="form_autresapin" method="post">
    <h4>{l s="Nouveau sapin" d='Modules.Tunnelvente.Autresapin'}</h4> 
    <ul>
         <li data-id="0" data-src="{$urls.base_url}modules/tunnelvente/images/resume.png">
            <input type="radio" name="autresapin" value="0" id="autresapin_0" />
            <label for="autresapin_0">{l s="Non merci je souhaite confirmer et payer" d='Modules.Tunnelvente.Autresapin'}</label>
        </li>
        <li data-id="1" data-src="{$urls.base_url}modules/tunnelvente/images/eco_new.png" >
            <input type="radio" name="autresapin" value="1" id="autresapin_1" checked="" />
            <label for="autresapin_1">{l s="Je souhaite commander un autre sapin" d='Modules.Tunnelvente.Autresapin'}</label>
        </li>
        <li data-id="2" data-src="{$urls.base_url}modules/tunnelvente/images/icon_little.png">
            <input type="radio" name="autresapin" value="2" id="autresapin_2" checked="" />
            <label for="autresapin_2">{l s="Je souhaite commander un little sapin" d='Modules.Tunnelvente.Autresapin'}</label>
        </li>
    </ul>
    
    
    <div class="icon-tunnel">
        <div class="cercle_autresapin cercle"></div>
        {*<h2>{l s="Nouveau sapin" d='Modules.Tunnelvente.Autresapin'}</h2>*}
    </div> 
     
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>
        
   
<script type="text/javascript">
    {if $little}
        var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/little";
    {else}
        var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/pot";
    {/if}
</script> 
{literal}
<script type="text/javascript">
    $(function($){
        //current
        $('.container_newsapin').removeClass('hidden');
        //previous
        $('.container_pot').addClass('hidden');
        //next
        $('#blockProduct').addClass('hidden');
        
        $('#blockProduct').empty(); //Enlever l'image du little ecosapin dans le cas de retour arrière
        
        $('form#form_autresapin .prev').click(function(event){
             //window.location.href = baseurl_tunnelvente;
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){              
                $me.addClass(classe);
                try{
                        $.ajax({
                        type: 'GET',
                        url: baseurl_tunnelvente,
                        data: 'ajax=1&back=1',
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
        
        $('form#form_autresapin').submit(function(event){
            event.preventDefault();
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){              
                $me.addClass(classe);
                
                    try{
                        $.ajax({
                        type: 'POST',
                        url: $me.attr("action"),
                        data: 'ajax=1&'+$me.serialize(),
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
        
        $('form#form_autresapin li label').click(function(){
           $('form#form_autresapin li label').removeClass("checked");
           $(this).addClass("checked");
           
           $('.thirdCol .container_newsapin img').attr('src',$(this).parents('li').data('src')).show();
           if(!$('.thirdCol.step7Col').length)
                $('.thirdCol.step6Col').removeClass('step6Col').addClass('step7Col');
            if( parseInt($(this).parents('li').data('id')) == 0){
                $('.thirdCol .text_p_contractuelle').hide();
            }else{
                $('.thirdCol .text_p_contractuelle').show();
            }
        });
        setTimeout(function(){
            $('form#form_autresapin li input:eq(0)').parents('li').find('label').click();
        },300);
        
    });
</script>
{/literal}