<div class="steps-header-mobile step-end">
    <div class="container-steps-mobile">
        <div class="step-name">
            <span>Etape 5</span>/5
        </div>
        <div class="step-name">
            {if $lang_iso == 'fr' }
                Mon Panier
            {elseif $lang_iso == 'en'}
                My cart
            {elseif $lang_iso == 'de'}
                Mein Warenkorb
            {/if}
        </div>
    </div>
</div>
<form action="{$base_url}module/tunnelventeabies/commande" id="form_accessoire" method="post">
    {if $lang_iso == 'fr' }
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">annuler et recommencer</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">Mon panier</span>
    {elseif $lang_iso == 'en'}
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">Previous</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">My cart</span>
    {elseif $lang_iso == 'de'}
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">Zurück</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">Mein Warenkorb</span>
    {/if}
    <div class="row ma-commande-recap">

        {if $lang_iso == 'fr' }
            <div class="col-md-4">
                <div class="final-recap-container">
                    <div class="prix-tva">Prix avec TVA incluse</div>
                </div>
                <div class="col-md-12 cart-tunnel dektop">
                    <button type="button" class="prev">prev</button>
                    <span class="text-prev" style="color: black;margin-top: 30px;">annuler et recommencer</span>
                    <button type="button" class="envoyer-commande-tunnel">Commander</button>
                </div>
                <button type="button" class="envoyer-commande-tunnel for-mobile">Commander</button>
            </div>


        {elseif $lang_iso == 'en'}
            <div class="col-md-4">
                <div class="final-recap-container">
                    <div class="prix-tva">Price including taxes</div>
                </div>
                <div class="col-md-12 cart-tunnel dektop">
                    <button type="button" class="prev">prev</button>
                    <span class="text-prev" style="color: black;margin-top: 30px;">Previous</span>
                    <button type="button" class="envoyer-commande-tunnel">Order</button>
                </div>
                <button type="button" class="envoyer-commande-tunnel for-mobile">Order</button>
            </div>


        {elseif $lang_iso == 'de'}
            <div class="col-md-4">
                <div class="final-recap-container">
                    <div class="prix-tva">Preis inkl. MwSt</div>
                </div>
                <div class="col-md-12 cart-tunnel dektop">
                    <button type="button" class="prev">prev</button>
                    <span class="text-prev" style="color: black;margin-top: 30px;">Zurück</span>
                    <button type="button" class="envoyer-commande-tunnel">Bestellen</button>
                </div>
                <button type="button" class="envoyer-commande-tunnel for-mobile">Bestellen</button>
            </div>

        {/if}

    </div>

   <div class="loading"></div>
</form>    
    
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{$base_url}module/tunnelventeabies/taille";
    var baseurl_tunnelvente_product = "{$base_url}module/tunnelventeabies/product";
    $(function($){
        $('.envoyer-commande-tunnel').on('click', function(event){
            event.preventDefault();
            var commandeUrl = "{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}";
            document.location.href = commandeUrl;
        });
    });
</script>
{literal}
<script type="text/javascript">
    $(function($){
        calPrice = $('.priceCalcContainer').data('calPrice');
        calPrice.recap();

        $('#typesapain').css('background-color', '#EAE9E7');
    });
</script>
{/literal}