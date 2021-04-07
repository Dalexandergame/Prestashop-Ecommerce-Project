{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !isset($content_only) || !$content_only}
					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->
                    </div><!-- .bg-home -->
			{if isset($HOOK_FOOTER)}
				<!-- Footer -->
				<div class="footer-container">
					<footer id="footer"  class="container">
                                            <div class="row">
                                                {$HOOK_FOOTER}
                                                <a href="//www.iubenda.com/privacy-policy/8254092" class="iubenda-nostyle no-brand iubenda-embed politik" title="Privacy Policy" target="_blank">{l s="Politiques de confidentialités"}</a>
                                                <div class="footer-block footer-adresse col-xs-12 col-sm-4 col-md-3">
                                                	Ecosapin Sàrl<br>Chemin du vieux réservoir 7<br>1116 Cottens<br>
<span class="telephon telfr">+41 21 539 11 16</span>
                                                    <span class="telephon telen">+41 22 539 11 16</span>
                                                    <span class="telephon telde">+41 43 505 11 16</span><br>Suisse
                                                    
                                                </div>
                                                <div class="logo_paiement">
                                                    <img src="{$img_dir}icon-carte-bancaire.png" alt="logo paiement" />
													<p>Webdesign & development by <a href="//pulse.digital/">pulse.digital</a>
													</p>
                                                </div>
					</footer>
				</div><!-- #footer -->
			{/if}
			<a href="#test-popup" class="btn popup-link hide">tada</a>   
<div id="test-popup" class="white-popup mfp-hide animated tada">
 <strong>Sold out! Tous les Little Ecosapins ont trouvé preneur cette année. Revenez nous voir en 2016</strong>

  <a class="popup-modal-dismiss" href="#">fermé</a>
</div>
		</div><!-- #page -->
{/if}
{include file="$tpl_dir./global.tpl"}
<script>
	$(function (){
            var texteOutStock = 'Sold out! Tous les Little Ecosapins ont trouvé preneur cette année. Revenez nous voir en 2016';
		$('.popup-link').magnificPopup({ 
		    removalDelay: 300,
		    type: 'inline',
		    modal: true,
		    callbacks: {
		    beforeOpen: function() {
		       //this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure animated ' + this.st.el.attr('data-effect'));
		    }
		  },
		  });
		/*$('a.little_ecosapin').on('click', function(){
			var linkTo = $(this).attr('href');
                        $('#test-popup strong').text(texteOutStock);
			$('.popup-link').magnificPopup('open');
			return false;
		})*/
		 
		 $(document).on('click', '.popup-modal-dismiss', function (e) {
		      e.preventDefault();
		      $.magnificPopup.close();
		  });
	})
</script>

{literal}
<script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
{/literal}


	</body>
</html>