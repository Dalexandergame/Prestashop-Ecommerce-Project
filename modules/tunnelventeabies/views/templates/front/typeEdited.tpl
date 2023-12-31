<div class="steps-header-mobile">
    <div class="container-steps-mobile">
        <div class="step-name">
            <span>Etape 2</span>/5
        </div>
        <div class="step-name">
            {if $language.iso_code == 'fr' || $language.iso_code == 'en'}
                Options
            {elseif $language.iso_code == 'de'}
                Optionen
            {/if}
        </div>
    </div>
</div>
<form action="{$urls.base_url}module/tunnelventeabies/pied" id="form_type"
      method="post">
    <div class="btns_next_prev mobile-respo">
        <button type="button" class="prev">prev</button>
        {if $language.iso_code == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}
        <button type="submit" class="next">next</button>
        {if $language.iso_code == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>

    <input type="hidden" name="taille" id="inputSelectedTaille" value="{$defaultCombination.id}">
    {if $language.iso_code == 'fr' }
        <span class="tunnelVenteHeading font-serif-title">{l s="Choisissez votre sapin" }</span>
    {elseif $language.iso_code == 'en'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Select your tree" }</span>
    {elseif $language.iso_code == 'de'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Wählen Sie Ihren Baum" }</span>
    {/if}
    {if true}
    <div class="row options-step">
        <div class="col-md-3 tunnel-taille-img">
            <img class="img-choix-prd" src="{$urls.base_url}themes/myabies/img/{$selectedTaille.image}"/>
        </div>
        <div class="col-md-3 choix-prd">
            <div class="tunnel-form-group for-mobile">
                {if $language.iso_code == 'fr' }
                    <span class="newTunnel-heading">hauteur du sapin souhaitée</span>
                {elseif $language.iso_code == 'en'}
                    <span class="newTunnel-heading">Height of your tree</span>
                {elseif $language.iso_code == 'de'}
                    <span class="newTunnel-heading">Baumgrösse</span>
                {/if}
                <div class="tunnel-select-container">
                    <select id="selectedTaille" name="preTaille" class="form-control selectedTaille">
                        {foreach $tailles as $taille}
                            <option value="{$taille.id}" {if !empty($defaultCombination[$taille.id])}selected{/if}>{$taille.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="tunnel-form-group tunnel-form-group-padding for-mobile respo-mobile">
                {if $language.iso_code == 'fr' || $language.iso_code == 'en'}
                    <span class="newTunnel-heading">Essence</span>
                {elseif $language.iso_code == 'de'}
                    <span class="newTunnel-heading">Tannenart</span>
                {/if}
                <div class="tunnel-select-container">
                    <select id="selectedEssence" name="essence" class="form-control selectedEssence-mobile">
                        {foreach $essence as $single_essence}
                            <option value="{$single_essence.id}">{$single_essence.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="tunnel-form-group tunnel-form-group-padding">
                {if $language.iso_code == 'fr' }
                    <span class="newTunnel-heading">Qualité</span>
                {elseif $language.iso_code == 'en'}
                    <span class="newTunnel-heading">Quality</span>
                {elseif $language.iso_code == 'de'}
                    <span class="newTunnel-heading">Qualität</span>
                {/if}
                <img src="{$urls.img_url}info-circle.png" class="open-pop" alt="icon-info" width="19px" height="19px"
                     style="margin-left: 10px;margin-bottom: 10px;cursor:pointer"/>
                <div class="qualites-tunnel">
                    {foreach $choix as $single}
                        <div data-id="{$single.id}"
                             class="qualite {if !empty($defaultCombination[$single.id])}selected{/if}">{$single.name}</div>
                    {/foreach}
                </div>
            </div>
            <div class="tunnel-form-group tunnel-form-group-padding desktop">
                {if $language.iso_code == 'fr' || $language.iso_code == 'en'}
                    <span class="newTunnel-heading">Essence</span>
                {elseif $language.iso_code == 'de'}
                    <span class="newTunnel-heading">Tannenart</span>
                {/if}
                <div class="tunnel-select-container">
                    <select id="selectedEssence" name="essence" class="form-control selectedEssence-desktop">
                        {foreach $essence as $single_essence}
                            <option value="{$single_essence.id}">{$single_essence.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3 agriculteur-block">
            <div>
                {if $language.iso_code == 'fr' }
                    <span class="newTunnel-heading">votre producteur</span>
                {elseif $language.iso_code == 'en'}
                    <span class="newTunnel-heading">Your Producer</span>
                {elseif $language.iso_code == 'de'}
                    <span class="newTunnel-heading">Ihr Produzent</span>
                {/if}
                <img src="{$urls.img_url}info-circle.png" alt="icon-info" style="margin-left: 10px;margin-bottom: 10px;"/>
            </div>
            <div class="agriculteur-details">
                <span class="nom_transporteur"></span>
                <img class="transporteur-img"/>
                <p class="description_transporteur"></p>
            </div>
        </div>
        <div class="col-md-3 tunnel-price-block">
            <ul class="list-tunnel">
                {if $language.iso_code == 'fr' }
                    <li class="btn-new-layout btn-new-dark-green open-pop-prix">grille des prix</li>
                    <li class="btn-new-layout btn-new-light-green"><span class="des">Dès</span> <span
                                class="total_prix">{$selectedTaille.price} {$currency.sign}</span></li>
                {elseif $language.iso_code == 'en'}
                    <li class="btn-new-layout btn-new-dark-green open-pop-prix">Price list</li>
                    <li class="btn-new-layout btn-new-light-green"><span class="des">From</span> <span
                                class="total_prix">{$selectedTaille.price} {$currency.sign}</span></li>
                {elseif $language.iso_code == 'de'}
                    <li class="btn-new-layout btn-new-dark-green open-pop-prix">Preisliste</li>
                    <li class="btn-new-layout btn-new-light-green"><span class="des">Ab</span> <span
                                class="total_prix">{$selectedTaille.price} {$currency.sign}</span></li>
                {/if}
            </ul>
        </div>
    </div>
    {else}
        <p>{l s="Cher visiteur, nous sommes victimes de notre succès et nous n'avons plus d'Ecosapins disponibles cette année. Pour être informé de l'ouverture du site l'année prochaine, vous pouvez nous envoyer votre adresse e-mail et code postal à contact@ecosapin.ch. Nous vous souhaitons de joyeuses fêtes de fin d'année auprès de votre famille et de vos proches et nous nous réjouissons de vous livrer votre Ecosapin l'année prochaine. Suivez nous sur Instagram et Facebook." mod='tunnelventeabies'}</p>
    {/if}
    <div class="btns_next_prev desktop">
        <button type="button" class="prev">prev</button>
        {if $language.iso_code == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}

        <button type="submit" class="next">next</button>
        {if $language.iso_code == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
    <div class="loading"></div>
</form>
<div id="popup-qualite" class="modalDialog">
    <div class="container-popup overflow-popup1">
        <div class="head-popup">
            <img src="{$urls.img_url}close.png" alt="close" class="closepopup close-modal">
        </div>
        <div class="body-popup">
            {if $language.iso_code == 'fr' }
                <h1 class="font-serif-title title-popup">Qualité</h1>
            {elseif $language.iso_code == 'en'}
                <h1 class="font-serif-title title-popup">Quality</h1>
            {elseif $language.iso_code == 'de'}
                <h1 class="font-serif-title title-popup">Qualität</h1>
            {/if}
            <div class="row equal">
                <div class="col-md-6 d-flex">
                    <div class="choix">
                        <img src="{$urls.img_url}img-qualite.png" alt="choix1" class="img-qualite">
                        <div class="info-pop">
                            {if $language.iso_code == 'fr'}
                            <div class="dec-pop">
                                <h3 class="title-label padding-15">{l s="Qualité 1er choix" mod='tunnelventeabies'}</h3>
                                <p class="font-serif-text padding-15">
                                    {l s="Un sapin de 1er choix doit être régulier de tous les côtés et assez touffu afin qu'on ne puisse pas voir à travers.C'est aussi un arbre avec une pointe unique et bien droite" mod='tunnelventeabies'}
                                </p>
                            </div>
                            <div class="bg-pop">
                                <span class="choix-button">
                                    1er choix </span>
                            </div>
                            {elseif $language.iso_code == 'en'}
                                <div class="dec-pop">
                                    <h3 class="title-label padding-15">Premium quality</h3>
                                    <p class="font-serif-text padding-15">
                                        A premium tree is regular from every angle, bushy (you can't see through it ) and has a nice tip.
                                    </p>
                                </div>
                                <div class="bg-pop">
                                <span class="choix-button">
                                  Premium </span>
                                </div>

                            {elseif $language.iso_code == 'de'}
                                <div class="dec-pop">
                                    <h3 class="title-label padding-15">Premium Qualität</h3>
                                    <p class="font-serif-text padding-15">
                                        Diese Premium Tannen sind regelmäßig, aus jedem Winkel. Sie sind buschig (man kann nicht durch sie hindurchsehen) und haben eine schöne Spitze
                                    </p>
                                </div>
                                <div class="bg-pop">
                                <span class="choix-button">
                                   Premium</span>
                                </div>
                            {/if}

                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="choix">
                        <img src="{$urls.img_url}img-qualite.png" alt="choix1" class="img-qualite">
                        <div class="info-pop">
                            {if $language.iso_code == 'fr'}
                                <div class="dec-pop">
                                    <h3 class="title-label padding-15">{l s="Qualité 2ème choix" mod='tunnelventeabies'}</h3>
                                    <p class="font-serif-text padding-15">
                                        {l s="Un sapin de 2ème choix présente des imperfections quand à sa régularité (sur l'un des côtés du sapin) et/ou une pointe abimée" mod='tunnelventeabies'}
                                    </p>
                                </div>
                                <div class="bg-pop">
                                    <span class="choix-button">2e choix </span>
                                </div>
                            {elseif $language.iso_code == 'en'}
                                <div class="dec-pop">
                                    <h3 class="title-label padding-15">2nd choice quality</h3>
                                    <p class="font-serif-text padding-15">
                                        A second choice tree can be irregular from one side, or have a broken tip
                                    </p>
                                </div>
                                <div class="bg-pop">
                                    <span class="choix-button">2nd Choice</span>
                                </div>
                            {elseif $language.iso_code == 'de'}
                                <div class="dec-pop">
                                    <h3 class="title-label padding-15">Zweite Wahl</h3>
                                    <p class="font-serif-text padding-15">
                                        Diese Zweite Wahl Tannen haben auf einer Seite eine Unregelmäßigkeit oder eine beschädigte Spitze
                                    </p>
                                </div>
                                <div class="bg-pop">
                                    <span class="choix-button">Zweite Wahl</span>
                                </div>
                            {/if}

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="popup-prix" class="modalDialog">
    <div class="container-popup overflow-popup">
        <div class="head-popup">
            <img src="{$urls.img_url}close.png" alt="close" class="closepopup close-modal-p">
        </div>
        <div class="body-popup">
            {if $language.iso_code == 'fr' }
                <h1 class="font-serif-title title-popup">Grille des prix</h1>
            {elseif $language.iso_code == 'en'}
                <h1 class="font-serif-title title-popup">Price list</h1>
            {elseif $language.iso_code == 'de'}
                <h1 class="font-serif-title title-popup">Preisliste</h1>
            {/if}
            <div class="row ">
                <div class="col-md-12 table-dektop">
                    <table>
                        <tr>{if $language.iso_code == 'fr' }
                                <th >Tailles</th>
                            {elseif $language.iso_code == 'en'}
                                <th >Size</th>
                            {elseif $language.iso_code == 'de'}
                                <th >Größe</th>
                            {/if}
                            <td >80cm-100cm</td>
                            <td >100cm-125cm</td>
                            <td >125cm-150cm</td>
                            <td >150cm-175cm</td>
                            <td >175cm-200cm</td>
                            <td >200cm-250cm</td>
                            <td >250cm-300cm</td>
                        </tr>
                        <tr>{if $language.iso_code == 'fr' }
                                <th >1er choix</th>
                            {elseif $language.iso_code == 'en' || $language.iso_code == 'de'}
                                <th >Premium</th>
                            {/if}
                            <td >69 CHF</td>
                            <td >79 CHF</td>
                            <td >89 CHF</td>
                            <td >99 CHF</td>
                            <td >119 CHF</td>
                            <td >179 CHF</td>
                            <td >229 CHF</td>

                        </tr>
                        <tr>{if $language.iso_code == 'fr' }
                                <th >2eme choix</th>
                            {elseif $language.iso_code == 'en'}
                                <th >2nd Choice</th>
                            {elseif $language.iso_code == 'de'}
                                <th >Zweite Wahl</th>
                            {/if}
                            <td >55 CHF</td>
                            <td >65 CHF</td>
                            <td >75 CHF</td>
                            <td >85 CHF</td>
                            <td >105 CHF</td>
                            <td >145 CHF</td>
                            <td >195 CHF</td>
                        </tr>

                        {if $language.iso_code == 'fr' }
                            <tr>
                                <td colspan="8" >10 CHF de supplément pour l’ajout d’un pied au sapin</td>
                            </tr>
                            <tr>
                                <td colspan="8" >30 CHF de supplément pour que MyAbis récupère et revalorise le sapin</td>
                            </tr>
                        {elseif $language.iso_code == 'en'}
                            <tr>
                                <td colspan="8" >10 CHF extra to add a stander to your tree</td>
                            </tr>
                            <tr>
                                <td colspan="8" >30 CHF extra for My Abies to pck-up and recycle your tree</td>
                            </tr>
                        {elseif $language.iso_code == 'de'}
                            <tr>
                                <td colspan="8" >10 CHF Zuschlag für den Holzständer</td>
                            </tr>
                            <tr>
                                <td colspan="8" >30 CHF Zuschlag für die Abholung und das Recycling des Tannenbaums</td>
                            </tr>
                        {/if}

                    </table>
                </div>

                <div class="col-md-12 table-repo">
                    <table>
                        <tbody>
                        {if $language.iso_code == 'fr' }
                            <tr><th>Taille</th><th>1er choix</th><th>2eme choix</th></tr>
                        {elseif $language.iso_code == 'en'}
                            <tr><th>Size</th><th>Premium</th><th>2nd Choice</th></tr>
                        {elseif $language.iso_code == 'de'}
                            <tr><th>Größe</th><th>Premium</th><th>Zweite Wahl</th></tr>
                        {/if}
                        <tr>
                            <td>80cm-100cm</td>
                            <td>69 CHF</td>
                            <td>55 CHF</td>
                        </tr>
                        <tr>
                            <td>100cm-125cm</td>
                            <td>79 CHF</td>
                            <td>65 CHF</td>
                        </tr>
                        <tr>
                            <td>125cm-150cm</td>
                            <td>89 CHF</td>
                            <td>75 CHF</td>
                        </tr>
                        <tr>
                            <td>150cm-175cm</td>
                            <td>99 CHF</td>
                            <td>85 CHF</td>
                        </tr>
                        <tr>
                            <td>175cm-200cm</td>
                            <td>119 CHF</td>
                            <td>105 CHF</td>
                        </tr>
                        <tr>
                            <td>200cm-250cm</td>
                            <td>179 CHF</td>
                            <td>145 CHF</td>
                        </tr>
                        <tr>
                            <td>250cm-300cm</td>
                            <td>229 CHF</td>
                            <td>195 CHF</td>
                        </tr>
                        {if $language.iso_code == 'fr' }
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">10 CHF de supplément pour l'ajout d'un pied au sapin</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">30 CHF de supplément pour que MyAbies récupère et revalorise le sapin</p>
                                </td>
                            </tr>
                        {elseif $language.iso_code == 'en'}
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">10 CHF extra to add a stander to your tree</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">30 CHF extra for My Abies to pck-up and recycle your tree</p>
                                </td>
                            </tr>
                        {elseif $language.iso_code == 'de'}
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">10 CHF Zuschlag für den Holzständer</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p class="text-popup">30 CHF Zuschlag für die Abholung und das Recycling des Tannenbaums</p>
                                </td>
                            </tr>
                        {/if}

                        </tbody>
                    </table>
                </div>
            </div>
            {if $language.iso_code == 'fr' }
                <div class="footer-popup">
                    <span class="text-popup">TVA comprise à 2,5%</span>
                </div>
            {elseif $language.iso_code == 'en'}
                <div class="footer-popup">
                    <span class="text-popup">All tax included</span>
                </div>
            {elseif $language.iso_code == 'de'}
                <div class="footer-popup">
                    <span class="text-popup">Inkl. 2.5% MwSt</span>
                </div>
            {/if}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".close-modal").click(function () {
        console.log('close')
        $('#popup-qualite').hide();
        $('#popup-qualite').removeClass('open');
        $('#typesapain').removeClass('openpopup');
    });
    $(".close-modal-p").click(function () {
        console.log('close')
        $('#popup-prix').hide();
        $('#popup-prix').removeClass('open');
        $('#typesapain').removeClass('openpopup');
    });
    $('.open-pop').click(function () {
        console.log('open')
        $('#popup-qualite').show();
        $('#popup-qualite').addClass('open');
        $('#typesapain').addClass('openpopup');
    });
    $('.open-pop-prix').click(function () {
        console.log('open')
        $('#popup-prix').show();
        $('#popup-prix').addClass('open');
        $('#typesapain').addClass('openpopup');
    });


    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/type";
    var baseurl = "{$urls.base_url}themes/myabies/img/";
    var lang = "{$language.iso_code}";
    var partner_name = "{$partner['name']}";
    var partner_img = "{$partner['img']}";
    // var partner_description = "{$partner['description']}";
    var partner_description = "{l s=$partner['description'] mod='tunnelventeabies'}";
    var allCombines = JSON.parse('{$allCombinations|@json_encode nofilter}');
    var attributs = JSON.parse('{$attributs|@json_encode nofilter}');

    if (partner_name == "" && partner_img == "") {
        $('.container_type .transporteur').addClass("hidden");
        $('.container_type .nom_transporteur').addClass("hidden");
    } else {
        $('.container_type .transporteur').removeClass("hidden");
        $('.container_type .nom_transporteur').removeClass("hidden")
    }

    if (partner_name == "Poste") {
        $('.container_type h4').hide(0);
    } else {
        $('.container_type h4').show(0);
    }

    $('.options-step .nom_transporteur').html(partner_name);
    $('.options-step .description_transporteur').html(partner_description);
    $('.options-step .transporteur-img').attr('src', "{$urls.base_url}modules/ecosapinpartners/uploads/" + partner_img);

    calPrice = $('.priceCalcContainer').data('calPrice');
    calPrice.setPriceSapin("{$defaultCombination.price}", "{$defaultCombination.name}", false, lang);
    $('.tunnel-taille-img img').attr('src', "{$defaultCombination.image}");

    $('.selectedTaille').on('change', function (e) {
        e.preventDefault();
        filterTaille();
        filterEssence();
        recalculateCombineHash();
    });

    $('.qualites-tunnel .qualite').on('click', function (e) {
        e.preventDefault();

        var currentSelector = $(this);
        $('.qualites-tunnel .qualite').each(function () {
            $(this).removeClass('selected');
        });

        currentSelector.addClass('selected');
        filterEssence();
        recalculateCombineHash();
    });

    $('.selectedEssence-mobile, .selectedEssence-desktop').on('change', function (e) {
        e.preventDefault();
        filterChoice();
        recalculateCombineHash();
    });

    function recalculateCombineHash() {
        var selectedTaille = parseInt($('.selectedTaille option:selected').val()),
            essenceClass = $(".mobile-respo").is(":visible") ? ".selectedEssence-mobile" : ".selectedEssence-desktop",
            selectedEssence = parseInt($(essenceClass + ' option:selected').val()),
            selectedQuality = parseInt($('.qualites-tunnel .selected').attr('data-id')),
            combineHash = selectedTaille * selectedEssence * selectedQuality;

        $.each(allCombines, function (i, value) {
            if (parseInt(value.combineHash) == combineHash) {
                calPrice = $('.priceCalcContainer').data('calPrice');
                calPrice.setPriceSapin(value.price, value.name, false);
                $('.tunnel-taille-img img').attr('src', value.image);
                $('#inputSelectedTaille').val(value.id);
                return;
            }
        });
    }

    function filterTaille() {
        var taille       = parseInt($('.selectedTaille option:selected').val()),
            essenceClass = $(".mobile-respo").is(":visible") ? ".selectedEssence-mobile" : ".selectedEssence-desktop";

        // reset elements
        $(".qualites-tunnel .qualite").removeClass("selected").hide();
        $(essenceClass + " option").removeAttr("selected").hide();

        var choices = [], essences = [];
        $.each(attributs, function (key, value) {
            let keys = key.split('-');
            if (keys[0] == taille) {
                if (!choices.includes(keys[1])) {
                    let choiceElem = $(".qualites-tunnel .qualite[data-id=" + keys[1] + "]");
                    if (!choices.length) {
                        choiceElem.addClass("selected")
                    }
                    choiceElem.show();
                    choices.push(keys[1]);
                }

                if (!essences.includes(keys[2])) {
                    let essenceElem = $(essenceClass + " option[value=" + keys[2] + "]");
                    if (!essences.length) {
                        $(essenceClass).val(keys[2]);
                        essenceElem.attr("selected", "selected");
                    }
                    essenceElem.show();
                    essences.push(keys[2]);
                }
            }
        })
    }

    function filterChoice() {
        var taille       = parseInt($('.selectedTaille option:selected').val()),
            essenceClass = $(".mobile-respo").is(":visible") ? ".selectedEssence-mobile" : ".selectedEssence-desktop",
            essence      = parseInt($(essenceClass + ' option:selected').val());

        // reset elements
        $(".qualites-tunnel .qualite").removeClass("selected").hide();

        var choices = [];
        $.each(attributs, function (key, value) {
            let keys = key.split('-');
            if (keys[0] == taille && keys[2] == essence) {
                if (!choices.includes(keys[1])) {
                    let choiceElem = $(".qualites-tunnel .qualite[data-id=" + keys[1] + "]");
                    if (!choices.length) {
                        choiceElem.addClass("selected")
                    }
                    choiceElem.show();
                    choices.push(keys[1]);
                }
            }
        })
    }

    function filterEssence() {
        var taille       = parseInt($('.selectedTaille option:selected').val()),
            choix        = parseInt($('.qualites-tunnel .selected').attr('data-id')),
            essenceClass = $(".mobile-respo").is(":visible") ? ".selectedEssence-mobile" : ".selectedEssence-desktop";

        // reset elements
        $(essenceClass + " option").removeAttr("selected").hide();

        var essences = [];
        $.each(attributs, function (key, value) {
            let keys = key.split('-');
            if (keys[0] == taille && keys[1] == choix) {
                if (!essences.includes(keys[2])) {
                    let essenceElem = $(essenceClass + " option[value=" + keys[2] + "]");
                    if (!essences.length) {
                        $(essenceClass).val(keys[2]);
                        essenceElem.attr("selected", "selected");
                    }
                    essenceElem.show();
                    essences.push(keys[2]);
                }
            }
        })
    }
</script>
{literal}
    <script type="text/javascript">
        $(function ($) {
            $('.selectedTaille').trigger('change');

            $('form#form_type .prev').click(function (event) {
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    try {
                        $.ajax({
                            type: 'GET',
                            url: baseurl_tunnelvente,
                            data: 'ajax=1&back=1',
                            dataType: 'json',
                            success: function (json) {

                                if (json.hasError) {

                                    $.each(json.errors, function (k, v) {
                                        showError(v);
                                    });
                                } else {


                                    $('#resp_content').html(json.html);
                                    $('#my_errors').empty();
                                    ShowHideStep(json.numStep);
                                }
                                $me.removeClass(classe);
                            }
                        });
                    } catch (e) {
                        $me.removeClass(classe);
                    }
                }
            });
            $('form#form_type').submit(function (event) {
                event.preventDefault();
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);

                    try {
                        $.ajax({
                            type: 'POST',
                            url: $me.attr("action"),
                            data: 'ajax=1&' + $me.serialize(),
                            dataType: 'json',
                            success: function (json) {
                                if (json.hasError) {
                                    $.each(json.errors, function (k, v) {
                                        showError(v);
                                    });
                                } else {
                                    $('#resp_content').html(json.html);
                                    $('#my_errors').empty();
                                    ShowHideStep(json.numStep);
                                }

                                $me.removeClass(classe);
                            }
                        });
                    } catch (e) {
                        $me.removeClass(classe);
                    }

                }

            });
        });
    </script>
{/literal}