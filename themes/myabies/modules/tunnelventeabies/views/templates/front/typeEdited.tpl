<form action="{$urls.base_url}module/tunnelventeabies/pied" id="form_type" method="post">
    <div class="btns_next_prev mobile-respo">
        <button type="button" class="prev">prev</button>
        <span class="text-prev">{l s="Précédent" mod='tunnelventeabies'}</span>
        <button type="submit" class="next">next</button>
        {if $lang_iso == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $lang_iso == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $lang_iso == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
    <span class="tunnelVenteHeading">{l s="Choisissez votre sapin" mod='tunnelventeabies'}</span>
    <div class="row options-step">
        <div class="col-md-3 tunnel-taille-img">
            <img src="{$urls.base_url}themes/myabies/img/{$selectedTaille.image}" />
        </div>
        <div class="col-md-3">
            <div class="tunnel-form-group">
                <span class="newTunnel-heading">hauteur du sapin souhaitée</span>
                <div class="tunnel-select-container">
                    <select id="selectedTaille" name="taille" class="form-control">
                        {foreach $tailles as $taille}
                            <option data-price="{$taille.id}"  data-src="{$taille.image}"
                                    value="{$taille.id}"{if $taille.id == $selectedTaille.id} selected{/if}>{$taille.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="tunnel-form-group tunnel-form-group-padding">
                <span class="newTunnel-heading">Qualité</span>
                <div class="qualites-tunnel">
                    <div class="qualite selected">1er Choix</div>
                    <div class="qualite">2ème Choix</div>
                </div>
            </div>
            <div class="tunnel-form-group tunnel-form-group-padding">
                <span class="newTunnel-heading">Essence</span>
                <div class="tunnel-select-container">
                    <select name="essence" class="form-control">
                        <option value="n/a">Choisissez l'essence</option>
                        <option value="1">xxx</option>
                        <option value="2">aaa</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3 agriculteur-block">
            <span class="newTunnel-heading">votre producteur</span>
            <div class="agriculteur-details">
                <span class="nom_transporteur"></span>
                <img class="transporteur-img" />
                <p class="description_transporteur"></p>
            </div>
        </div>
        <div class="col-md-3 tunnel-price-block">
            <ul class="list-tunnel">
                <li class="btn-new-layout btn-new-dark-green">grille des prix</li>
                <li class="btn-new-layout btn-new-light-green"><span class="des">Dès</span> <span class="total_prix">{$selectedTaille.price}</span> {$currency->sign}</li>
            </ul>
        </div>
    </div>
    <div class="btns_next_prev dektop">
        {if !$hasSapin}<button type="button" class="prev">prev</button>{/if}

        {if count($types) }
            <button type="submit" class="next">next</button>
        {/if}
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">

    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/type";
    var baseurl = "{$urls.base_url}themes/myabies/img/";
    var partner_name = "{$partner['name']}";
    var partner_img = "{$partner['img']}";
    // var partner_description = "{$partner['description']}";
    var partner_description = "{l s=$partner['description'] mod='tunnelventeabies'}";


    if(partner_name == "" && partner_img == "") {
        $('.container_type .transporteur').addClass("hidden");
        $('.container_type .nom_transporteur').addClass("hidden");
    }else{
        $('.container_type .transporteur').removeClass("hidden");
        $('.container_type .nom_transporteur').removeClass("hidden")
    }

    if(partner_name == "Poste"){
        $('.container_type h4').hide(0);
    }else{
        $('.container_type h4').show(0);
    }

    $('.options-step .nom_transporteur').html(partner_name);
    $('.options-step .description_transporteur').html(partner_description);
    $('.options-step .transporteur-img').attr('src',"{$urls.base_url}modules/ecosapinpartners/uploads/"+partner_img );

    $('#selectedTaille').on('change', function(e) {
        e.preventDefault();

        var selectedPrice = $("#selectedTaille option:selected").attr('data-price'),
            selectedImage = $("#selectedTaille option:selected").attr('data-src');

        $('.tunnel-taille-img img').attr('src', baseurl + selectedImage);
        calPrice = $('#resp_content .tunnel-price-block').data('calPrice');
        calPrice.setPriceSapin(selectedPrice, true);
        /*$('.list-tunnel .total_prix').text(selectedPrice);*/
    });
</script>
{literal}
    <script type="text/javascript">
        $(function($){
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
        });
    </script>
{/literal}