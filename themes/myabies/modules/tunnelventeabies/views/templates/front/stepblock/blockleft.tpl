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
                                {if $lang_iso == 'fr' }
                                    Code postal
                                {elseif $lang_iso == 'en' || $cookie->id_lang == '1'}
                                    Postal code
                                {elseif $lang_iso == 'de'}
                                    Postleitzahl
                                {/if}
                            {elseif $stepDetail->getTitre()=="Options"}
                                {if $lang_iso == 'fr' || $lang_iso == 'en'}
                                    Options
                                {elseif $lang_iso == 'de'}
                                    Optionen
                                {/if}
                            {elseif $stepDetail->getTitre()=="Type de pied"}
                                {if $lang_iso == 'fr' }
                                    Type de pied
                                {elseif $lang_iso == 'en' || $cookie->id_lang == '1'}
                                    Base type
                                {elseif $lang_iso == 'de'}
                                    Ständerarten
                                {/if}
                            {elseif $stepDetail->getTitre()=="Livraison et recyclage"}
                                {if $lang_iso == 'fr' }
                                    Livraison et recyclage
                                {elseif $lang_iso == 'en' || $cookie->id_lang == '1'}
                                    Pick-up and recycling
                                {elseif $lang_iso == 'de'}
                                    Abholung und Recycling
                                {/if}
                            {elseif $stepDetail->getTitre()=="Mon Panier"}
                                {if $lang_iso == 'fr' }
                                    Mon Panier
                                {elseif $lang_iso == 'en' || $cookie->id_lang == '1'}
                                    My cart
                                {elseif $lang_iso == 'de'}
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
    <div class="priceCalcContainer" data-currency="{$currency->sign}"></div>
    <script>
        $(function () {
            /*$('.step_1').addClass('active');
            $('.step_2').addClass('inactive');
            $('.step_3').addClass('inactive');*/
        })
    </script>
</div>