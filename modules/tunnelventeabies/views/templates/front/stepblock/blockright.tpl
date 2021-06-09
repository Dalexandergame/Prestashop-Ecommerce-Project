<div class="col-md-6 thirdCol" data-currency="{$currency->sign}">
    <div class="container_images ">
        <div class="prix hidden"><span class="text_prix">{l s='Total' }</span> <span class="total_prix ">0 CHF</span>
        </div>
        <div class="container_npa hidden ">
            <h3>Step 1: {l s='Configuration of fir' mod='tunnelventeabies' }</h3>
            <ul>
                <li>{l s='Select the NPA' mod='tunnelventeabies' }</li>
                <li>{l s='Choose the type of fir' mod='tunnelventeabies' }</li>
                <li>{l s='Choose fir size' mod='tunnelventeabies' }</li>
                <li>{l s='Choose the type of recycling' mod='tunnelventeabies' }</li>
            </ul>
            <h3>Step 2: {l s='Choice of decoration' mod='tunnelventeabies' }</h3>
            <ul>
                <li>{l s='Choose decoration' mod='tunnelventeabies' }</li>
                <li>{l s='Choose the cache pot' mod='tunnelventeabies' }</li>
            </ul>
            <h3>Step 3: {l s='Confirmation and payment' mod='tunnelventeabies' }</h3>
            <ul>
                <li>{l s='Choose your accessories' mod='tunnelventeabies' }</li>
                <li>{l s='Order confirmation' mod='tunnelventeabies' }</li>
            </ul>
        </div>
        <div class="container_type hidden ">
            <div class="left_Type">
                <div class="transporteur">
                    <img src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}themes/ecosapin-v2/img/partenaire-fribourg.jpg"/>
                </div>
                <div class="nom_transporteur">Transporteur</div>
                <h4>{l s='I will take care of your fir' mod='tunnelventeabies'}</h4>
                <p class="description_transporteur">Grâce à nos partenaires locaux, le concept Ecosapin vous est proposé
                    dans toute la Suisse. Faites connaissance avec votre sapiniste.</p>
            </div>
        </div>
        <div class="container_taille_pot hidden ">
            <img src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}themes/ecosapin-v2/img/en-pot1.png"/>
        </div>
        <div class="container_sapin hidden">
            <img class="img_sapin" style="display: none;"/>
        </div>
        <div class="container_recyclage hidden">
            <div class="left_Type">
                <h2 class="nom_transporteur" id="type">Type</h2>
                <h2 class="nom_transporteur" id="taille">Taille</h2>
                <div class="desc">{l s='Livré par' mod='tunnelventeabies'}</div>
                <div class="transporteur">
                    <img src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}themes/ecosapin-v2/img/partenaire-fribourg.jpg"/>
                </div>
                <h2 class="nom_transporteur" id="transporteur">Transporteur</h2>
                {*<h2 class="nom_transporteur" id="prix" >Prix</h2>*}
            </div>
            <div class="image_right">
                <!--<img class="img_sapin" style="display: none;" />-->
                <img src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}themes/ecosapin-v2/img/ecosapin-nordman.png"/>
            </div>
        </div>
        <div class="container_boule hidden">
            <img class="img_boule" style="max-width:100%;max-height:100%;display: none;"/>
            <div class="textboutpromo">
                <!--<p class="text-center"><span style="text-decoration:underline;">{l s='Prix du kit à la location' mod='tunnelventeabies'}</span>{l s=': 65 CHF' mod='tunnelventeabies'}</p>
                <p class="text-center"><span style="text-decoration:underline;">{l s='Prix du kit à la vente' mod='tunnelventeabies'}</span>{l s=': 135 CHF' mod='tunnelventeabies'}</p>-->
            </div>
        </div>
        <div class="container_pot hidden">
            {*<img class="img_pot" style="display: none;" />*}
            <div class="textpotpromo">
                <!--<p class="text-center"><span style="text-decoration:underline;">{l s='Prix du cache pot à la location' mod='tunnelventeabies'}</span>{l s=': 6 CHF' mod='tunnelventeabies'}</p>
                <p class="text-center"><span style="text-decoration:underline;">{l s='Prix du cache pot à la vente' mod='tunnelventeabies'}</span>{l s=': 11 CHF' mod='tunnelventeabies'}</p>-->
            </div>
        </div>
        <div class="container_pied hidden">
            <div class="textpiedpromo"></div>
        </div>
        <div class="container_newsapin hidden">
            <img src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}modules/tunnelvente/images/resume.png"
                 class="img_newsapin" style="display: none;"/>
        </div>
        <div id="blockProduct"></div>
        {*        <sapn class="text_p_contractuelle">{l s='Non contractual photo' mod='tunnelventeabies'}</sapn>*}
    </div>


</div>
