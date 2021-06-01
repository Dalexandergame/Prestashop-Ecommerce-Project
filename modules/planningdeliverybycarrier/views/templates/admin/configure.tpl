{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
<form action="" method="post" name="settings_form">
	<fieldset><h3><i class="icon icon-cogs"></i> {l s='Configuration par transporteur' mod='Configurationpartransporteur'}</h3>
		<label for="goToCarrier"><i class="icon-AdminParentShipping"></i>Transporteur</label>
		<div class="margin-form">
			<select name="goToCarrier" id="goToCarrier" style="width: 300px">
				<option value="7">La Poste (id:7)</option>
				<option value="79">Camionnette Ecosapin VD A (id:79)</option>
				<option value="25">Camionnette Ecosapin VD B (id:25)</option>
				<option value="29">Camionnette Ecosapin VD C (id:29)</option>
				<option value="27">Camionnette Ecosapin VD D (id:27)</option>
				<option value="22">Camionnette Ecosapin FR A (id:22)</option>
				<option value="21">Camionnette Ecosapin FR B (id:21)</option>
				<option value="23">Camionnette Ecosapin FR C (id:23)</option>
				<option value="30">Camionnette Ecosapin VS A (id:30)</option>
				<option value="31">Camionnette Ecosapin VS B (id:31)</option>
				<option value="32">Camionnette Ecosapin VS C (id:32)</option>
				<option value="33">Camionnette Ecosapin GE A (id:33)</option>
				<option value="34">Camionnette Ecosapin GE B (id:34)</option>
				<option value="43">Camionnette Ecosapin NE A (id:43)</option>
				<option value="45">Camionnette Ecosapin BE A (id:45)</option>
				<option value="37">Camionnette Ecosapin VD E (id:37)</option>
				<option value="38">Camionnette Ecosapin ZH A (id:38)</option>
				<option value="39">Camionnette Ecosapin ZH B (id:39)</option>
				<option value="40">Camionnette Ecosapin ZH C (id:40)</option>
				<option value="41">Camionnette Ecosapin ZH D (id:41)</option>
				<option value="42">Camionnette Ecosapin ZH E (id:42)</option>
				<option value="44">Camionnette Ecosapin NE B (id:44)</option>
				<option value="46">Camionnette Ecosapin BE B (id:46)</option>
				<option value="47">Camionnette Ecosapin BE C (id:47)</option>
				<option value="50">Camionnette Ecosapin Grand Sapin (id:50)</option>
				<option value="78">Camionnette Ecosapin GE C (id:78)</option>
				<option value="77">Camionnette Ecosapin GE D (id:77)</option>
				<option value="76">Camionnette Ecosapin BS A (id:76)</option>
				<option value="74">Camionnette Ecosapin BS B (id:74)</option>
				<option value="72">Camionnette Ecosapin BE D (id:72)</option>
				<option value="71">Camionnette Ecosapin BE E (id:71)</option>
				<option value="70">Camionnette Ecosapin VD F (id:70)</option>
				<option value="69">Camionnette Ecosapin VD G (id:69)</option>
				<option value="68">Camionnette Ecosapin SG A (id:68)</option>
				<option value="66">Camionnette Ecosapin TI A (id:66)</option>
				<option value="65">Camionnette Ecosapin TI B (id:65)</option>
				<option value="80">Camionnette Ecosapin FR D (id:80)</option>
				<option value="81">Camionnette Ecosapin VD H (id:81)</option>
				<option value="82">Genève Vélo (id:82)</option>
				<option value="83">Camionnette Ecosapin VD I (id:83)</option>
				<option value="84">Camionnette Ecosapin VS D (id:84)</option>
				<option value="85">Camionnette Ecosapin LU A (id:85)</option>
				<option value="86">Camionnette Ecosapin LU B (id:86)</option>
				<option value="87">Camionnette Ecosapin LU C (id:87)</option>
				<option value="88">Camionnette Ecosapin LU D (id:88)</option>
			</select>
			<button type="submit" value="1" id="module_form_submit_btn" name="submitPlanningdeliverybycarrierModule" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> Configurer
			</button>
		</div>
	</fieldset>
</form>
</div>




<div class="panel">
	<form action="" method="post" name="form">
		<br><h3><i class="icon icon-cogs"></i> {l s='Configuration par transporteur' mod='Configurationpartransporteur'}</h3>
		<label class="t">Transporteurs</label>
		<div class="margin-form">
			<label class="t" for="7_La Poste"> La Poste</label>
			<input type="checkbox" name="carriersBox[]" id="7_La Poste" value="7" checked="checked">
			<label class="t" for="79_Camionnette Ecosapin VD A"> Camionnette Ecosapin VD A</label>
			<input type="checkbox" name="carriersBox[]" id="79_Camionnette Ecosapin VD A" value="79" checked="checked">
			<label class="t" for="25_Camionnette Ecosapin VD B"> Camionnette Ecosapin VD B</label>
			<input type="checkbox" name="carriersBox[]" id="25_Camionnette Ecosapin VD B" value="25" checked="checked">
			<label class="t" for="29_Camionnette Ecosapin VD C"> Camionnette Ecosapin VD C</label>
			<input type="checkbox" name="carriersBox[]" id="29_Camionnette Ecosapin VD C" value="29" checked="checked">
			<label class="t" for="27_Camionnette Ecosapin VD D"> Camionnette Ecosapin VD D</label>
			<input type="checkbox" name="carriersBox[]" id="27_Camionnette Ecosapin VD D" value="27" checked="checked">
			<label class="t" for="22_Camionnette Ecosapin FR A"> Camionnette Ecosapin FR A</label>
			<input type="checkbox" name="carriersBox[]" id="22_Camionnette Ecosapin FR A" value="22" checked="checked">
			<label class="t" for="21_Camionnette Ecosapin FR B"> Camionnette Ecosapin FR B</label>
			<input type="checkbox" name="carriersBox[]" id="21_Camionnette Ecosapin FR B" value="21" checked="checked">
			<label class="t" for="23_Camionnette Ecosapin FR C"> Camionnette Ecosapin FR C</label>
			<input type="checkbox" name="carriersBox[]" id="23_Camionnette Ecosapin FR C" value="23" checked="checked">
			<label class="t" for="30_Camionnette Ecosapin VS A"> Camionnette Ecosapin VS A</label>
			<input type="checkbox" name="carriersBox[]" id="30_Camionnette Ecosapin VS A" value="30" checked="checked">
			<label class="t" for="31_Camionnette Ecosapin VS B"> Camionnette Ecosapin VS B</label>
			<input type="checkbox" name="carriersBox[]" id="31_Camionnette Ecosapin VS B" value="31" checked="checked">
			<label class="t" for="32_Camionnette Ecosapin VS C"> Camionnette Ecosapin VS C</label>
			<input type="checkbox" name="carriersBox[]" id="32_Camionnette Ecosapin VS C" value="32" checked="checked">
			<label class="t" for="33_Camionnette Ecosapin GE A"> Camionnette Ecosapin GE A</label>
			<input type="checkbox" name="carriersBox[]" id="33_Camionnette Ecosapin GE A" value="33" checked="checked">
			<label class="t" for="34_Camionnette Ecosapin GE B"> Camionnette Ecosapin GE B</label>
			<input type="checkbox" name="carriersBox[]" id="34_Camionnette Ecosapin GE B" value="34" checked="checked">
			<label class="t" for="43_Camionnette Ecosapin NE A"> Camionnette Ecosapin NE A</label>
			<input type="checkbox" name="carriersBox[]" id="43_Camionnette Ecosapin NE A" value="43" checked="checked">
			<label class="t" for="45_Camionnette Ecosapin BE A"> Camionnette Ecosapin BE A</label>
			<input type="checkbox" name="carriersBox[]" id="45_Camionnette Ecosapin BE A" value="45" checked="checked">
			<label class="t" for="37_Camionnette Ecosapin VD E"> Camionnette Ecosapin VD E</label>
			<input type="checkbox" name="carriersBox[]" id="37_Camionnette Ecosapin VD E" value="37" checked="checked">
			<label class="t" for="38_Camionnette Ecosapin ZH A"> Camionnette Ecosapin ZH A</label>
			<input type="checkbox" name="carriersBox[]" id="38_Camionnette Ecosapin ZH A" value="38" checked="checked">
			<label class="t" for="39_Camionnette Ecosapin ZH B"> Camionnette Ecosapin ZH B</label>
			<input type="checkbox" name="carriersBox[]" id="39_Camionnette Ecosapin ZH B" value="39" checked="checked">
			<label class="t" for="40_Camionnette Ecosapin ZH C"> Camionnette Ecosapin ZH C</label>
			<input type="checkbox" name="carriersBox[]" id="40_Camionnette Ecosapin ZH C" value="40" checked="checked">
			<label class="t" for="41_Camionnette Ecosapin ZH D"> Camionnette Ecosapin ZH D</label>
			<input type="checkbox" name="carriersBox[]" id="41_Camionnette Ecosapin ZH D" value="41" checked="checked">
			<label class="t" for="42_Camionnette Ecosapin ZH E"> Camionnette Ecosapin ZH E</label>
			<input type="checkbox" name="carriersBox[]" id="42_Camionnette Ecosapin ZH E" value="42" checked="checked">
			<label class="t" for="44_Camionnette Ecosapin NE B"> Camionnette Ecosapin NE B</label>
			<input type="checkbox" name="carriersBox[]" id="44_Camionnette Ecosapin NE B" value="44" checked="checked">
			<label class="t" for="46_Camionnette Ecosapin BE B"> Camionnette Ecosapin BE B</label>
			<input type="checkbox" name="carriersBox[]" id="46_Camionnette Ecosapin BE B" value="46" checked="checked">
			<label class="t" for="47_Camionnette Ecosapin BE C"> Camionnette Ecosapin BE C</label>
			<input type="checkbox" name="carriersBox[]" id="47_Camionnette Ecosapin BE C" value="47" checked="checked">
			<label class="t" for="50_Camionnette Ecosapin Grand Sapin"> Camionnette Ecosapin Grand Sapin</label>
			<input type="checkbox" name="carriersBox[]" id="50_Camionnette Ecosapin Grand Sapin" value="50" checked="checked">
			<label class="t" for="78_Camionnette Ecosapin GE C"> Camionnette Ecosapin GE C</label>
			<input type="checkbox" name="carriersBox[]" id="78_Camionnette Ecosapin GE C" value="78" checked="checked">
			<label class="t" for="77_Camionnette Ecosapin GE D"> Camionnette Ecosapin GE D</label>
			<input type="checkbox" name="carriersBox[]" id="77_Camionnette Ecosapin GE D" value="77" checked="checked">
			<label class="t" for="76_Camionnette Ecosapin BS A"> Camionnette Ecosapin BS A</label>
			<input type="checkbox" name="carriersBox[]" id="76_Camionnette Ecosapin BS A" value="76" checked="checked">
			<label class="t" for="74_Camionnette Ecosapin BS B"> Camionnette Ecosapin BS B</label>
			<input type="checkbox" name="carriersBox[]" id="74_Camionnette Ecosapin BS B" value="74" checked="checked">
			<label class="t" for="72_Camionnette Ecosapin BE D"> Camionnette Ecosapin BE D</label>
			<input type="checkbox" name="carriersBox[]" id="72_Camionnette Ecosapin BE D" value="72" checked="checked">
			<label class="t" for="71_Camionnette Ecosapin BE E"> Camionnette Ecosapin BE E</label>
			<input type="checkbox" name="carriersBox[]" id="71_Camionnette Ecosapin BE E" value="71" checked="checked">
			<label class="t" for="70_Camionnette Ecosapin VD F"> Camionnette Ecosapin VD F</label>
			<input type="checkbox" name="carriersBox[]" id="70_Camionnette Ecosapin VD F" value="70" checked="checked">
			<label class="t" for="69_Camionnette Ecosapin VD G"> Camionnette Ecosapin VD G</label>
			<input type="checkbox" name="carriersBox[]" id="69_Camionnette Ecosapin VD G" value="69" checked="checked">
			<label class="t" for="68_Camionnette Ecosapin SG A"> Camionnette Ecosapin SG A</label>
			<input type="checkbox" name="carriersBox[]" id="68_Camionnette Ecosapin SG A" value="68" checked="checked">
			<label class="t" for="66_Camionnette Ecosapin TI A"> Camionnette Ecosapin TI A</label>
			<input type="checkbox" name="carriersBox[]" id="66_Camionnette Ecosapin TI A" value="66" checked="checked">
			<label class="t" for="65_Camionnette Ecosapin TI B"> Camionnette Ecosapin TI B</label>
			<input type="checkbox" name="carriersBox[]" id="65_Camionnette Ecosapin TI B" value="65" checked="checked">
			<label class="t" for="80_Camionnette Ecosapin FR D"> Camionnette Ecosapin FR D</label>
			<input type="checkbox" name="carriersBox[]" id="80_Camionnette Ecosapin FR D" value="80" checked="checked">
			<label class="t" for="81_Camionnette Ecosapin VD H"> Camionnette Ecosapin VD H</label>
			<input type="checkbox" name="carriersBox[]" id="81_Camionnette Ecosapin VD H" value="81" checked="checked">
			<label class="t" for="82_Genève Vélo"> Genève Vélo</label>
			<input type="checkbox" name="carriersBox[]" id="82_Genève Vélo" value="82" checked="checked">
			<label class="t" for="83_Camionnette Ecosapin VD I"> Camionnette Ecosapin VD I</label>
			<input type="checkbox" name="carriersBox[]" id="83_Camionnette Ecosapin VD I" value="83" checked="checked">
			<label class="t" for="84_Camionnette Ecosapin VS D"> Camionnette Ecosapin VS D</label>
			<input type="checkbox" name="carriersBox[]" id="84_Camionnette Ecosapin VS D" value="84" checked="checked">
			<label class="t" for="85_Camionnette Ecosapin LU A"> Camionnette Ecosapin LU A</label>
			<input type="checkbox" name="carriersBox[]" id="85_Camionnette Ecosapin LU A" value="85" checked="checked">
			<label class="t" for="86_Camionnette Ecosapin LU B"> Camionnette Ecosapin LU B</label>
			<input type="checkbox" name="carriersBox[]" id="86_Camionnette Ecosapin LU B" value="86" checked="checked">
			<label class="t" for="87_Camionnette Ecosapin LU C"> Camionnette Ecosapin LU C</label>
			<input type="checkbox" name="carriersBox[]" id="87_Camionnette Ecosapin LU C" value="87" checked="checked">
			<label class="t" for="88_Camionnette Ecosapin LU D"> Camionnette Ecosapin LU D</label>
			<input type="checkbox" name="carriersBox[]" id="88_Camionnette Ecosapin LU D" value="88" checked="checked">
			<p class="clear" style="display: block; width: 550px;">Sélectionnez les transporteurs pour lesquels le choix de la date de livraison sera activé.
			</p>
		</div>
			<br></br>
		<label>Liste en page d'accueil du BO</label>
		<div class="margin-form">
			<input type="radio" name="active_hook_home_back_office" id="active_hook_home_back_office_on" value="1" checked="checked">
			<label class="t" for="active_hook_home_back_office_on"> <img src="../img/admin/enabled.gif" alt="Activé" title="Activé"></label>
			<input type="radio" name="active_hook_home_back_office" id="active_hook_home_back_office_off" value="0">
			<label class="t" for="active_hook_home_back_office_off"> <img src="../img/admin/disabled.gif" alt="Handicapé" title="Handicapé"></label>
			<p class="clear" style="display: block; width: 550px;">Active le rappel des livraisons à effectuer sur la page d'accueil de l'administration.
			</p>
			<div class="clear"></div>
		</div>
		<p>Sélectionnez les statuts des commandes que vous ne voulez pas voir apparaître dans le récapitulatif des livraisons à effectuer. Vous pouvez sélectionner plusieurs statuts en appuyant sur la touche CTRL.</p><br>
		<label for="osPlanning">Jour</label>
		<div class="margin-form">
			<select name="osPlanning[]" id="osPlanning" multiple="true" style="height:200px;width:360px;">
				<option value="6" selected="selected">Annulé</option>
				<option value="13">Autorisation acceptée par PayPal</option>
				<option value="26">Awaiting Payment (TWINT)</option>
				<option value="9" selected="selected">En attente de réapprovisionnement</option>
				<option value="16">En attente de réapprovisionnement</option>
				<option value="1">En attente du paiement par chèque</option>
				<option value="11">En attente du paiement par PayPal</option>
				<option value="4">En cours de livraison</option>
				<option value="25">En cours de récupération</option>
				<option value="8" selected="selected">Erreur de paiement</option>
				<option value="24">Expédié</option>
				<option value="5">Livré</option>
				<option value="12">Paiement à distance accepté</option>
				<option value="2">Paiement accepté</option>
				<option value="14">paiement incomplet</option>
				<option value="15">Paiement modifié</option>
				<option value="20">Paiement par facture (via Swissbilling)</option>
				<option value="10">Paiment par BVR</option>
				<option value="18">Payé avec Postfinance</option>
				<option value="23">Payée par CRM</option>
				<option value="22">Payée par PayPoint</option>
				<option value="3">Préparation en cours</option>
				<option value="21">Récupéré</option>
				<option value="7" selected="selected">Remboursé</option>
				<option value="17">Waiting cod validation</option>
			</select>
		</div>
		<br></br>
		<div class="margin-form clear"><input type="submit" name="submitCarriersGlobalSettings" value="Sauver" class="button"></div>
	</fieldset>
	</form>
</div>




<div class="panel">
<form action="" method="post" name="exception_form">
	<h3><i class="icon icon-cogs"></i> {l s='Date de livraison' mod='Datedelivraison'}</h3>
	<fieldset>
		<p>Ajoutez autant des dates que nécessaire.</p><br>
		<label>Date de livraison</label>
		<div class="margin-form" style="width: 300px">
			<label class="t" for="date_from"> Du</label>
			<br>
			<input type="text" name="date_from" id="date_from" class="hasDatepicker">
			<br>
			<label class="t" for="date_to"> au</label>
			<br>
			<input type="text" name="date_to" id="date_to" class="hasDatepicker">
			<br>
			<label class="t" for="max_places"> max places</label>
			<br>
			<input type="number" name="max_places" id="max_places">
			<br>
			<label class="t" for="id_carrier">carrier</label>
			<br>
			<select id="id_carrier" name="id_carrier"><option value="7">La Poste (id: 7)</option><option value="79">Camionnette Ecosapin VD A (id: 79)</option><option value="25">Camionnette Ecosapin VD B (id: 25)</option><option value="29">Camionnette Ecosapin VD C (id: 29)</option><option value="27">Camionnette Ecosapin VD D (id: 27)</option><option value="22">Camionnette Ecosapin FR A (id: 22)</option><option value="21">Camionnette Ecosapin FR B (id: 21)</option><option value="23">Camionnette Ecosapin FR C (id: 23)</option><option value="30">Camionnette Ecosapin VS A (id: 30)</option><option value="31">Camionnette Ecosapin VS B (id: 31)</option><option value="32">Camionnette Ecosapin VS C (id: 32)</option><option value="33">Camionnette Ecosapin GE A (id: 33)</option><option value="34">Camionnette Ecosapin GE B (id: 34)</option><option value="43">Camionnette Ecosapin NE A (id: 43)</option><option value="45">Camionnette Ecosapin BE A (id: 45)</option><option value="37">Camionnette Ecosapin VD E (id: 37)</option><option value="38">Camionnette Ecosapin ZH A (id: 38)</option><option value="39">Camionnette Ecosapin ZH B (id: 39)</option><option value="40">Camionnette Ecosapin ZH C (id: 40)</option><option value="41">Camionnette Ecosapin ZH D (id: 41)</option><option value="42">Camionnette Ecosapin ZH E (id: 42)</option><option value="44">Camionnette Ecosapin NE B (id: 44)</option><option value="46">Camionnette Ecosapin BE B (id: 46)</option><option value="47">Camionnette Ecosapin BE C (id: 47)</option><option value="50">Camionnette Ecosapin Grand Sapin (id: 50)</option><option value="78">Camionnette Ecosapin GE C (id: 78)</option><option value="77">Camionnette Ecosapin GE D (id: 77)</option><option value="76">Camionnette Ecosapin BS A (id: 76)</option><option value="74">Camionnette Ecosapin BS B (id: 74)</option><option value="72">Camionnette Ecosapin BE D (id: 72)</option><option value="71">Camionnette Ecosapin BE E (id: 71)</option><option value="70">Camionnette Ecosapin VD F (id: 70)</option><option value="69">Camionnette Ecosapin VD G (id: 69)</option><option value="68">Camionnette Ecosapin SG A (id: 68)</option><option value="66">Camionnette Ecosapin TI A (id: 66)</option><option value="65">Camionnette Ecosapin TI B (id: 65)</option><option value="80">Camionnette Ecosapin FR D (id: 80)</option><option value="81">Camionnette Ecosapin VD H (id: 81)</option><option value="82">Genève Vélo (id: 82)</option><option value="83">Camionnette Ecosapin VD I (id: 83)</option><option value="84">Camionnette Ecosapin VS D (id: 84)</option><option value="85">Camionnette Ecosapin LU A (id: 85)</option><option value="86">Camionnette Ecosapin LU B (id: 86)</option><option value="87">Camionnette Ecosapin LU C (id: 87)</option><option value="88">Camionnette Ecosapin LU D (id: 88)</option></select>
			<br>
			<input type="submit" name="submitException" value="Ajouter" class="button" style="margin-bottom: 20px">
		</div>
		<input type="hidden" name="id_planning_delivery_carrier_exception" id="id_planning_delivery_carrier_exception">
		<input type="hidden" name="exception_name" id="exception_name">
		<input type="hidden" name="exception_action" id="slot_action">
		<button id="toggleDeliveryTable" class="">Afficher/Cacher Tableau</button>
	</fieldset>
</form>
</div>





<div class="panel">
<form action="" method="post" name="retour_exception_form">
	<h3><i class="icon icon-cogs"></i> {l s='Retours' mod='Retours'}</h3>
	<fieldset>
		<p>Ajoutez autant des dates que nécessaire.</p><br>
		<label>Date de livraison</label>
		<div class="margin-form" style="width: 300px">
			<label class="t" for="date_from_r"> Du</label>
			<br>
			<input type="text" name="date_from" id="date_from_r" class="hasDatepicker">
			<br>
			<label class="t" for="date_to_r"> au</label>
			<br>
			<input type="text" name="date_to" id="date_to_r" class="hasDatepicker">
			<br>
			<label class="t" for="max_places"> max places</label>
			<br>
			<input type="number" name="max_places" id="max_places">
			<br>
			<label class="t" for="id_carrier">carrier</label>
			<br>
			<select id="id_carrier" name="id_carrier"><option value="7">La Poste (id: 7)</option><option value="79">Camionnette Ecosapin VD A (id: 79)</option><option value="25">Camionnette Ecosapin VD B (id: 25)</option><option value="29">Camionnette Ecosapin VD C (id: 29)</option><option value="27">Camionnette Ecosapin VD D (id: 27)</option><option value="22">Camionnette Ecosapin FR A (id: 22)</option><option value="21">Camionnette Ecosapin FR B (id: 21)</option><option value="23">Camionnette Ecosapin FR C (id: 23)</option><option value="30">Camionnette Ecosapin VS A (id: 30)</option><option value="31">Camionnette Ecosapin VS B (id: 31)</option><option value="32">Camionnette Ecosapin VS C (id: 32)</option><option value="33">Camionnette Ecosapin GE A (id: 33)</option><option value="34">Camionnette Ecosapin GE B (id: 34)</option><option value="43">Camionnette Ecosapin NE A (id: 43)</option><option value="45">Camionnette Ecosapin BE A (id: 45)</option><option value="37">Camionnette Ecosapin VD E (id: 37)</option><option value="38">Camionnette Ecosapin ZH A (id: 38)</option><option value="39">Camionnette Ecosapin ZH B (id: 39)</option><option value="40">Camionnette Ecosapin ZH C (id: 40)</option><option value="41">Camionnette Ecosapin ZH D (id: 41)</option><option value="42">Camionnette Ecosapin ZH E (id: 42)</option><option value="44">Camionnette Ecosapin NE B (id: 44)</option><option value="46">Camionnette Ecosapin BE B (id: 46)</option><option value="47">Camionnette Ecosapin BE C (id: 47)</option><option value="50">Camionnette Ecosapin Grand Sapin (id: 50)</option><option value="78">Camionnette Ecosapin GE C (id: 78)</option><option value="77">Camionnette Ecosapin GE D (id: 77)</option><option value="76">Camionnette Ecosapin BS A (id: 76)</option><option value="74">Camionnette Ecosapin BS B (id: 74)</option><option value="72">Camionnette Ecosapin BE D (id: 72)</option><option value="71">Camionnette Ecosapin BE E (id: 71)</option><option value="70">Camionnette Ecosapin VD F (id: 70)</option><option value="69">Camionnette Ecosapin VD G (id: 69)</option><option value="68">Camionnette Ecosapin SG A (id: 68)</option><option value="66">Camionnette Ecosapin TI A (id: 66)</option><option value="65">Camionnette Ecosapin TI B (id: 65)</option><option value="80">Camionnette Ecosapin FR D (id: 80)</option><option value="81">Camionnette Ecosapin VD H (id: 81)</option><option value="82">Genève Vélo (id: 82)</option><option value="83">Camionnette Ecosapin VD I (id: 83)</option><option value="84">Camionnette Ecosapin VS D (id: 84)</option><option value="85">Camionnette Ecosapin LU A (id: 85)</option><option value="86">Camionnette Ecosapin LU B (id: 86)</option><option value="87">Camionnette Ecosapin LU C (id: 87)</option><option value="88">Camionnette Ecosapin LU D (id: 88)</option></select>
			<br>
			<input type="submit" name="submitRetourException" value="Ajouter" class="button" style="margin-bottom: 20px">
		</div>
		<input type="hidden" name="id_planning_retour_carrier_exception" id="id_planning_retour_carrier_exception">
		<input type="hidden" name="exception_name" id="exception_name">
		<input type="hidden" name="retour_exception_action" id="slot_action">
		<button id="toggleRetourTable" class="">Afficher/Cacher Tableau</button>

	</fieldset>
</form>
</div>








<div class="panel">
<form action="#" method="post">
	<h3><i class="icon icon-warning"></i> {l s='Informations' mod='Informations'}</h3>
	<fieldset>
		<hr>
		<p>Une réalisation : <a href="http://www.speedyweb.fr" title="Création et référencement de sites web à Perpignan, Lille et Paris - SpeedyWeb">SpeedyWeb</a></p>
	</fieldset>
</form>
</div>









{*<div classes="panel">
	<h3><i classes="icon icon-tags"></i> {l s='Documentation' mod='testmodule'}</h3>
	<p>
		&raquo; {l s='You can get a PDF documentation to configure this module' mod='testmodule'} :
		<ul>
			<li><a href="#" target="_blank">{l s='English' mod='testmodule'}</a></li>
			<li><a href="#" target="_blank">{l s='French' mod='testmodule'}</a></li>
		</ul>
	</p>
</div>*}
