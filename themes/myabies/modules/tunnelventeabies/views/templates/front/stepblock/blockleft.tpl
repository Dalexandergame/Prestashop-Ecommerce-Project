<div class="col-md-12 step-ul">
    <div class="cont-table container">
        {assign var='i' value=1}
        {assign var='isOk' value="isOk"}
        <div class="steps-container">
            {foreach from=$steps->getListStep()  item=step}
                {foreach from=$step->getListStepDetail()   item=stepDetail}
                    <div data-step="{$i}" class="step{if $stepDetail->getActive() } active{/if}">
                        <div class="step-number">{$i}</div>
                        <div class="step-description">
                            {if $stepDetail->getTitre()=="Code postal"}
                                {if $language.iso_code == 'fr' }
                                    Code postal
                                {elseif $language.iso_code == 'en' || $cookie->id_lang == '1'}
                                    Postal code
                                {elseif $language.iso_code == 'de'}
                                    Postleitzahl
                                {/if}
                            {elseif $stepDetail->getTitre()=="Options"}
                                {if $language.iso_code == 'fr' || $language.iso_code == 'en'}
                                    Options
                                {elseif $language.iso_code == 'de'}
                                    Optionen
                                {/if}
                            {elseif $stepDetail->getTitre()=="Type de pied"}
                                {if $language.iso_code == 'fr' }
                                    Type de pied
                                {elseif $language.iso_code == 'en' || $cookie->id_lang == '1'}
                                    Base type
                                {elseif $language.iso_code == 'de'}
                                    Ständerarten
                                {/if}
                            {elseif $stepDetail->getTitre()=="Livraison et recyclage"}
                                {if $language.iso_code == 'fr' }
                                    Livraison et recyclage
                                {elseif $language.iso_code == 'en' || $cookie->id_lang == '1'}
                                    Pick-up and recycling
                                {elseif $language.iso_code == 'de'}
                                    Abholung und Recycling
                                {/if}
                            {elseif $stepDetail->getTitre()=="Mon Panier"}
                                {if $language.iso_code == 'fr' }
                                    Mon Panier
                                {elseif $language.iso_code == 'en' || $cookie->id_lang == '1'}
                                    My cart
                                {elseif $language.iso_code == 'de'}
                                    Mein Warenkorb
                                {/if}
                            {/if}
                        </div>
                    </div>
                    <div class="line-step"></div>
                    {assign var='i' value=$i+1}
                {/foreach}
            {/foreach}
        </div>


    </div>
    <div class="priceCalcContainer" data-currency="{$currency.sign}"></div>
    <script>
        $(function () {
            /*$('.step_1').addClass('active');
            $('.step_2').addClass('inactive');
            $('.step_3').addClass('inactive');*/
        })
    </script>
</div>