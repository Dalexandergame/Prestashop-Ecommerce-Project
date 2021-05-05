
<fieldset>
    
    {if isset($confirmation)}
    <div class="alert alert-success">Settings updated</div>
    {/if}

    <div class="panel">
    <div class="panel-heading">
    <legend><img src="../img/admin/cog.gif" alt="" width="16"
    />Configuration</legend>
    </div>
        
    <form action="" method="post" >
    
    <div class="form-group clearfix">
     <div class="col-lg-9 ">
        Textes à optimiser:
        <textarea class=" textarea-autosize" rows="5" id="textes" name="textes" style="overflow: hidden; word-wrap: break-word; resize: none; height: 113px;">{if $textes}{$textes}{/if}</textarea>
        <p class="help-block">
           Entrez les textes à optimiser selon la forme suivante : "text1":"text2","text3":"text4",... 
        </p>
        <p class="help-block">
            Dans le cas ou vous comptez supprimer toute une ligne d'une commande , on aura deux cas :<br>
           1) La ligne à supprimer se trouve au début de la commande ,suivez la ligne à optimiser par une virgule , exemple : "Retourner votre,":"",...<br>
           2) La ligne à supprimer se trouve au milieu ou à la fin de la commande ,précédez la ligne à optimiser par une virgule , exemple : ",Frais virement":"",...<br>
           NB: Faire attention aux espaces se trouvant au début ou fin de la ligne à optimiser , il est important de les mentionner.<br>
               On a mis un point au début et à la fin de chaque ligne comme aide visuelle pour détecter ces espaces.<br>
        </p>
     </div>
    </div>
    <div class="form-group clearfix">
     <div class="col-lg-9 ">
        OSM API key: <input type="text" id="osmkey" name="osmkey" value=" {if $osmkey}{$osmkey}{/if} ">
     </div>
    </div>
    <div class="form-group clearfix">
     <div class="col-lg-9 ">
        GOOGLE API key: <input type="text" id="gkey" name="gkey" value=" {if $gkey}{$gkey}{/if} ">
     </div>
    </div>
    <div class="panel-footer">
    <input class="btn btn-default pull-right" type="submit" name="suivi_config_form" value="Save" />
    </div>
     
    </form>
    </div>
</fieldset>