<div class="col-md-6 thirdCol" data-currency="{$currency.sign}">
    <div class="container_images ">
        <div class="prix hidden"><span class="text_prix">{l s='Total' }</span> <span class="total_prix ">0 CHF</span>
        </div>
        <div class="container_npa hidden ">
            <h3>Step 1: {l s='Configuration of fir' d='Modules.Tunnelvente.Blockright' }</h3>
            <ul>
                <li>{l s='Select the NPA' d='Modules.Tunnelvente.Blockright' }</li>
                <li>{l s='Choose the type of fir' d='Modules.Tunnelvente.Blockright' }</li>
                <li>{l s='Choose fir size' d='Modules.Tunnelvente.Blockright' }</li>
                <li>{l s='Choose the type of recycling' d='Modules.Tunnelvente.Blockright' }</li>
            </ul>
            <h3>Step 2: {l s='Choice of decoration' d='Modules.Tunnelvente.Blockright' }</h3>
            <ul>
                <li>{l s='Choose decoration' d='Modules.Tunnelvente.Blockright' }</li>
                <li>{l s='Choose the cache pot' d='Modules.Tunnelvente.Blockright' }</li>
            </ul>
            <h3>Step 3: {l s='Confirmation and payment' d='Modules.Tunnelvente.Blockright' }</h3>
            <ul>
                <li>{l s='Choose your accessories' d='Modules.Tunnelvente.Blockright' }</li>
                <li>{l s='Order confirmation' d='Modules.Tunnelvente.Blockright' }</li>
            </ul>
        </div>
        <div class="container_type hidden ">
            <div class="left_Type">
                <div class="transporteur">
                    <img src="{$urls.base_url}themes/ecosapin/assets/img/partenaire-fribourg.jpg"/>
                </div>
                <div class="nom_transporteur">Transporteur</div>
                <h4>{l s='I will take care of your fir' d='Modules.Tunnelvente.Blockright'}</h4>
                <p class="description_transporteur">Grâce à nos partenaires locaux, le concept Ecosapin vous est proposé
                    dans toute la Suisse. Faites connaissance avec votre sapiniste.</p>
            </div>
        </div>
        <div class="container_taille_pot hidden ">
            <img src="{$urls.base_url}themes/ecosapin/assets/img/en-pot1.png"/>
        </div>
        <div class="container_sapin hidden">
            <img class="img_sapin" style="display: none;"/>
        </div>
        <div class="container_recyclage hidden">
            <div class="left_Type">
                <h2 class="nom_transporteur" id="type">Type</h2>
                <h2 class="nom_transporteur" id="taille">Taille</h2>
                <div class="desc">{l s='Livré par' d='Modules.Tunnelvente.Blockright'}</div>
                <div class="transporteur">
                    <img src="{$urls.base_url}themes/ecosapin/assets/img/partenaire-fribourg.jpg"/>
                </div>
                <h2 class="nom_transporteur" id="transporteur">Transporteur</h2>
                {*<h2 class="nom_transporteur" id="prix" >Prix</h2>*}
            </div>
            <div class="image_right">
                <!--<img class="img_sapin" style="display: none;" />-->
                <img src="{$urls.base_url}themes/ecosapin/assets/img/ecosapin-nordman.png"/>
            </div>
        </div>
        <div class="container_boule hidden">
            <img class="img_boule" style="max-width:100%;max-height:100%;display: none;"/>
            <div class="textboutpromo">
                <!--<p class="text-center"><span style="text-decoration:underline;">{l s='Prix du kit à la location' d='Modules.Tunnelvente.Blockright'}</span>{l s=': 65 CHF' d='Modules.Tunnelvente.Blockright'}</p>
                <p class="text-center"><span style="text-decoration:underline;">{l s='Prix du kit à la vente' d='Modules.Tunnelvente.Blockright'}</span>{l s=': 135 CHF' d='Modules.Tunnelvente.Blockright'}</p>-->
            </div>
        </div>
        <div class="container_pot hidden">
            {*<img class="img_pot" style="display: none;" />*}
            <div class="textpotpromo">
                <!--<p class="text-center"><span style="text-decoration:underline;">{l s='Prix du cache pot à la location' d='Modules.Tunnelvente.Blockright'}</span>{l s=': 6 CHF' d='Modules.Tunnelvente.Blockright'}</p>
                <p class="text-center"><span style="text-decoration:underline;">{l s='Prix du cache pot à la vente' d='Modules.Tunnelvente.Blockright'}</span>{l s=': 11 CHF' d='Modules.Tunnelvente.Blockright'}</p>-->
            </div>
        </div>
        <div class="container_pied hidden">
            <div class="textpiedpromo"></div>
        </div>
        <div class="container_newsapin hidden">
            <img src="{$urls.base_url}modules/tunnelvente/images/resume.png"
                 class="img_newsapin" style="display: none;"/>
        </div>
        <div id="blockProduct"></div>
        {*        <sapn class="text_p_contractuelle">{l s='Non contractual photo' d='Modules.Tunnelvente.Blockright'}</sapn>*}
    </div>


</div>
