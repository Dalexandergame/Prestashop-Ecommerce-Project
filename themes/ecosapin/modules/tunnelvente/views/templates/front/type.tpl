<form action="{$urls.base_url}module/tunnelvente/taille" id="form_type" method="post">
    <h4>{l s="Choisissez le type de sapin" mod='tunnelvente'}</h4>
    <ul>
        {foreach from=$types item=type}
            <li data-id="{$type.id}" data-desc="{$type.desc}">
                <input  type="radio" name="type" value="{$type.id}" id="type_{$type.id}" {if isset($id_type) and $type.id == $id_type } checked="" {/if}/>
                <label for="type_{$type.id}">{$type.name}
                </label>
            </li>
            {foreachelse}
            <li class="not-taille">
                <span>{l s="Désolé nous sommes en rupture de stock, revenez plus tard" mod='tunnelvente'}</span>
            </li>
        {/foreach}
    </ul>
    {if count($types) }
        <div class="icon-tunnel">
            <div class="cercle_taille cercle"></div>
            {*<h2>{l s="Choisissez le type de votre sapin" mod='tunnelvente'}</h2>*}
        </div>
        <div class="clear"></div>
        <div class="description"><div></div></div>
    {/if}
    <div class="btns_next_prev">
        {if !$hasSapin}<button type="button" class="prev">prev</button>{/if}

        {if count($types) }
            <button type="submit" class="next">next</button>
        {/if}
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">

    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/type";
    var partner_name = "{$partner['name']}";
    var partner_img = "{$partner['img']}";
    var partner_description = "{$partner['description']}";

    if(partner_name == "" && partner_img == "") {
        $('.container_type .transporteur').addClass("hidden");
        $('.container_type .nom_transporteur').addClass("hidden");
    }else{
        $('.container_type .transporteur').removeClass("hidden");
        $('.container_type .nom_transporteur').removeClass("hidden")
    }
    $('.container_type .nom_transporteur').html(partner_name);
    $('.container_type .description_transporteur').html(partner_description);
    $('.container_type .transporteur img').attr('src',"{$urls.base_url}modules/ecosapinpartners/uploads/"+partner_img );

</script>
{literal}
    <script type="text/javascript">
        $(function($){
            //current
            $('.container_type').removeClass('hidden');
            //previous
            $('.container_npa').addClass('hidden');
            //or previous
            $('.container_newsapin').addClass('hidden');
            //next
            $('.container_taille_pot').addClass('hidden');

            $('#blockProduct').empty(); //Enlever l'image du little ecosapin dans le cas de retour arrière

            $('form#form_type .prev').click(function(event){
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

            $('form#form_type').submit(function(event){
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
            //*/
            $('form#form_type li:not(.disabled) label').click(function(){
                $('form#form_type li label').removeClass("checked");
                $(this).addClass("checked");
                $('.description div').html($(this).parents('li').data('desc'));
            });

            $('#form_type ul input').change(function(){
                if($('input#type_13').is(":checked")) {
                    $('.cercle_taille').addClass("sap-coupe");
                } else {
                    $('.cercle_taille').removeClass("sap-coupe");
                }
            });

            $.each($('form#form_type li input:checked').parents('li'),function(){
                $(this).find('label').click();
            });

            if( !$('form#form_type li input:checked').length && $('form#form_type li:not(.disabled) label:eq(0)').length){
                $('form#form_type li:not(.disabled) label:eq(0)').click();
            }
        });
    </script>
{/literal}