<div class="steps-header-mobile step-end">
    <div class="container-steps-mobile">
        <div class="step-name">
            <span>Etape 5</span>/5
        </div>
        <div class="step-name">
            {if $language.iso_code == 'fr' }
                Mon Panier
            {elseif $language.iso_code == 'en'}
                My cart
            {elseif $language.iso_code == 'de'}
                Mein Warenkorb
            {/if}
        </div>
    </div>
</div>
<form action="{$urls.base_url}module/tunnelventeabies/commande" id="form_accessoire" method="post">
    {if $language.iso_code == 'fr' }
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">Annuler et recommencer</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">Mon panier</span>
    {elseif $language.iso_code == 'en'}
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">Cancel and start over</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">My cart</span>
    {elseif $language.iso_code == 'de'}
        <div class="col-md-12 cart-tunnel mobile-respo">
            <button type="button" class="prev">prev</button>
            <span class="text-prev" style="color: black;margin-top: 30px;">Stornieren un fangen von vorne an</span>
        </div>
        <span class="tunnelVenteHeading-final font-serif-title">Mein Warenkorb</span>
    {/if}
    <div class="row ma-commande-recap">
        {if $language.iso_code == 'fr' }
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
        {elseif $language.iso_code == 'en'}
            <div class="col-md-4">
                <div class="final-recap-container">
                    <div class="prix-tva">Price including taxes</div>
                </div>
                <div class="col-md-12 cart-tunnel dektop">
                    <button type="button" class="prev">prev</button>
                    <span class="text-prev" style="color: black;margin-top: 30px;">Cancel and start over</span>
                    <button type="button" class="envoyer-commande-tunnel">Order</button>
                </div>
                <button type="button" class="envoyer-commande-tunnel for-mobile">Order</button>
            </div>
            {elseif $language.iso_code == 'de'}
            <div class="col-md-4">
                <div class="final-recap-container">
                    <div class="prix-tva">Preis inkl. MwSt</div>
                </div>
                <div class="col-md-12 cart-tunnel dektop">
                    <button type="button" class="prev">prev</button>
                    <span class="text-prev" style="color: black;margin-top: 30px;">Stornieren un fangen von vorne an</span>
                    <button type="button" class="envoyer-commande-tunnel">Bestellen</button>
                </div>
                <button type="button" class="envoyer-commande-tunnel for-mobile">Bestellen</button>
            </div>
            {/if}
        </div>

   <div class="loading"></div>
</form>    
    
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/taille";
    var baseurl_tunnelvente_product = "{$urls.base_url}module/tunnelventeabies/product";
    $(function($){
        $('.envoyer-commande-tunnel').on('click', function(event){
            event.preventDefault();
            var commandeUrl = "{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}";
            document.location.href = commandeUrl;
        });

        $('.cart-tunnel .prev').on('click', function(event) {
            event.preventDefault();

            window.location.replace("{$urls.base_url}module/tunnelventeabies/type");
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