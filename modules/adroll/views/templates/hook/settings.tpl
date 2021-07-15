{*
 * This file is part of the prestahsop-adroll module.
 *
 * (c) AdRoll
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Damián Nohales <damian.nohales@adroll.com>
 * @copyright AdRoll
 * @license   https://opensource.org/licenses/MIT The MIT License
 *}
<style type="text/css">
    label {
        font-size: 1.05rem;
    }

    #prestashop-adroll-settings-userinfo .userinfo-message {
        margin-top: 4px;
        margin-bottom: 0;
    }

    .single-advertisable-message {
        font-size: 13pt;
    }

    .pas-section-configured h2 i {
        font-size: inherit;
        margin-right: 5px;
    }

    .pas-section-onboarding select {
        width: 40% !important;
    }

    #content.bootstrap {
        padding-left: 20px;
        padding-right: 20px;
    }
</style>
<script type="text/javascript">
    PrestashopAdrollSettings = {
        l: {
            adroll_profile_settings_page:
                '{l s='AdRoll profile settings page' mod='adroll' js=true}',
            current_advertisable_info:
                '{l s='Your current advertiser profile is: @advertisable_name@.' mod='adroll' js=true}',
            error_loading:
                '{l s='We couldn\'t get your advertiser profiles from AdRoll, please try again later' mod='adroll' js=true}',
            error_no_advertisables:
                '{l s='Your AdRoll user, @username@, has no advertiser profiles, please go to your @adroll_profile_settings_page_link@ to add an advertiser profile to your account.' mod='adroll' js=true}',
            error_onboarding:
                '{l s='An error has ocurred when trying to do the onboarding: @error_details@. Please refresh the page and try again.' mod='adroll' js=true}',
            onboarding_single_advertisable:
                '{l s='Connecting your AdRoll advertiser profile @advertisable_name@ with your PrestaShop store.' mod='adroll' js=true}',
            onboarding_multiple_advertisables:
                '{l s='Select which advertiser profile you\'d like to connect to this shop' mod='adroll' js=true}',
            unowned_advertisable_warning:
                '{l s='(you are not signed in with the AdRoll account that owns this advertiser profile, hence you cannot see its descriptive name)' mod='adroll' js=true}',
            userinfo_message:
                '{l s='You\'re signed in to AdRoll as @username@' mod='adroll' js=true}',
            signup_message:
                '{l s='If you don\'t have an AdRoll account, sign up @here_link@' mod='adroll' js=true}',
            here_link:
                '{l s='here' mod='adroll' js=true}'
        },

        init: function() {
            this.shopHost = '{$shop_host|escape:'htmlall':'UTF-8'}';
            this.currentAdvertisableId = '{$adroll_advertisable_id|escape:'htmlall':'UTF-8'}';
            this.currentPixelId = '{$adroll_pixel_id|escape:'htmlall':'UTF-8'}';
            this.currentPopup = null;
            this.adrollBaseUri = '{$adroll_base_uri|escape:'htmlall':'UTF-8'}';
            this.currentSection = 'loading';
            this.user = null;

            $('#prestashop-adroll-settings-userinfo').hide();
            this.selectSection(null).hide();
            this.selectSection(this.currentSection).show();

            this.selectSection('login').find('.signup-message').html(
                this.l.signup_message
                    .replace('@here_link@', '<a href="javascript:PrestashopAdrollSettings.openAdrollRegister();">' + this.l.here_link + '</a>')
            );

            this.selectSection('configured').find('.start-onboarding-info a').click(function(event) {
                event.preventDefault();
                this.showSection('onboarding');
            }.bind(this));

            this.selectSection('onboarding').find('form').submit(function(event) {
                event.preventDefault();
                this.startOnboarding($(event.target).serializeArray());
                $(event.target).find(':input').attr('disabled', true);
            }.bind(this));

            $(window).on('message', function(event) {
                if (this.currentPopup &&
                    event.originalEvent.data === 'onAdrollSigninSuccess' &&
                    event.originalEvent.origin === this.adrollBaseUri) {
                    this.currentPopup.close();
                    this.showSection('loading');
                    location.reload();
                }
            }.bind(this));

            this.loadUser();
        },

        loadUser: function() {
            $.ajax({
                url: this.adrollBaseUri + '/prestashop/api/v2/get_user_info',
                dataType: 'jsonp',
                success: function(response) {
                    var error;
                    if (response.error) {
                        if (response.error.status === 401) {
                            this.showSection('login');
                            return;
                        } else {
                            error = this.l.error_loading;
                        }
                    } else {
                        this.user = response;
                        if (this.user.advertisables.length === 0) {
                            error = this.l.error_no_advertisables
                                .replace('@username@', '<strong>' + this.getPrintUserInfo() + '</strong>')
                                .replace('@adroll_profile_settings_page_link@', '<a href="' + this.adrollBaseUri + '/dashboard/account/profiles">' + this.l.adroll_profile_settings_page + '</a>');
                        }
                    }

                    if (error) {
                        this.showError(error)
                    } else {
                        this.onUserLoaded();
                    }
                }.bind(this)
            });
        },

        openPopup: function(url) {
            var width = 500;
            var height = 665;
            var left = $(window).width() / 2 - width / 2 + window.screenX;
            var top = $(window).height() / 2 - height / 2 + window.screenY;

            this.currentPopup = window.open(
                url, 'AdRoll',
                'menubar=no,toolbar=no,status=no,width=' + width + ',height=' + height + ',left=' + left + ',top' + top
            );

            if (window.focus) {
                this.currentPopup.focus();
            }
        },

        getAdrollRegisterPath: function() {
            return '/onboarding/register?experiment=popup';
        },

        openAdrollLogin: function() {
            this.openPopup(this.adrollBaseUri + '/account/signin?next=/account/signin_success_popup');
        },

        openAdrollRegister: function() {
            this.openPopup(this.adrollBaseUri + this.getAdrollRegisterPath());
        },

        openAdrollLogout: function() {
            this.openPopup(this.adrollBaseUri + '/account/signout_popup?next=' + encodeURIComponent(this.getAdrollRegisterPath()));
        },

        selectSection: function(section) {
            if (section) {
                return $('#prestashop-adroll-settings-sections > .pas-section-' + section);
            } else {
                return $('#prestashop-adroll-settings-sections > div');
            }
        },

        showSection: function(section) {
            this.selectSection(this.currentSection).hide();
            this.currentSection = section;
            this.selectSection(this.currentSection).show();
        },

        showError: function(message) {
            this.selectSection('error').find('.message').html(message);
            this.showSection('error');
        },

        isRecommendedAdvertisable: function(advertisable) {
            return advertisable.url.search(RegExp('https?://' + this.shopHost)) === 0;
        },

        onUserLoaded: function() {
            $('#prestashop-adroll-settings-userinfo').show();
            $('#prestashop-adroll-settings-userinfo .userinfo-message').html(
                this.l.userinfo_message
                    .replace('@username@', '<strong>' + this.getPrintUserInfo() + '</strong>')
            );

            if (this.user.advertisables.length === 1) {
                this.selectSection('onboarding').find('p').html(
                    '<p class="single-advertisable-message">' +
                        this.l.onboarding_single_advertisable
                            .replace('@advertisable_name@', '<strong>' + this.user.advertisables[0].name + ' (' + this.user.advertisables[0].url + ')</strong>') +
                    '</p>' +
                    '<input type="hidden" name="advertisable" value="' + this.user.advertisables[0].eid + '" />'
                )
            } else {
                this.selectSection('onboarding').find('p').html(
                    '<label class="form-control-label">' +
                        this.l.onboarding_multiple_advertisables +
                    '</label>' +
                    '<select name="advertisable" class="form-control">' +
                    this.user.advertisables.map(function(advertisable) {
                        return (
                            '<option value="' + advertisable.eid + '"' +
                            (this.isRecommendedAdvertisable(advertisable) ? ' selected="selected"' : '') + '>' +
                            advertisable.name + ' (' + advertisable.url + ')' +
                            '</option>'
                        )
                    }.bind(this)).join('') +
                    '</select>'
                );
            }

            if (this.currentAdvertisableId && this.currentPixelId) {
                var foundAdvertisables = $.grep(this.user.advertisables, function(advertisable) {
                    return advertisable.eid === this.currentAdvertisableId;
                }.bind(this));
                if (foundAdvertisables.length > 0) {
                    var advertisableInfo = foundAdvertisables[0].name;
                } else {
                    var advertisableInfo =
                        this.currentAdvertisableId + ' ' +
                        this.l.unowned_advertisable_warning;
                    this.selectSection('configured').find('.go-dashboard-info').hide();
                }
                this.selectSection('configured').find('.current-advertisable-info').html(
                    this.l.current_advertisable_info
                        .replace('@advertisable_name@', '<strong>' + advertisableInfo + '</strong>')
                );
                this.selectSection('configured').find('.go-dashboard-info a').attr(
                    'href',
                    this.adrollBaseUri + '/dashboard?advertisable=' + this.currentAdvertisableId
                );
                this.showSection('configured');
            } else {
                this.showSection('onboarding');
            }
        },

        getPrintUserInfo: function() {
            return this.user.name + ' (' + this.user.email + ')';
        },

        startOnboarding: function(submitData) {
            if (this.currentAdvertisableId) {
                submitData.push({
                    name: 'previous_advertisable',
                    value: this.currentAdvertisableId
                });
            }

            $.ajax({
                url: this.adrollBaseUri + '/prestashop/api/v2/create_user',
                dataType: 'jsonp',
                data: submitData,
                success: function(response) {
                    if (response.error) {

                        this.showError(
                            this.l.error_onboarding
                                .replace('@error_details@', JSON.stringify(response))
                        );
                    } else {
                        $('#pas_form [name="adroll_advertisable_id"]').val(response.advertisable_eid);
                        $('#pas_form [name="adroll_pixel_id"]').val(response.pixel_eid);
                        $('#pas_form').submit();
                    }
                }.bind(this)
            });
        }
    };
    $(function() {
        PrestashopAdrollSettings.init();
    });
</script>

<script type="text/javascript">
    {literal}
    adroll_adv_id = "3QOM4TKN4RD7TO3HCPYRKV";
    adroll_pix_id = "MIOLTYI6GNDAVO6IN67BFT";
    adroll_version = "2.0";
    (function(w,d,e,o,a){
        w.__adroll_loaded=true;
        w.adroll=w.adroll||[];
        w.adroll.f=['setProperties','identify','track'];
        var roundtripUrl = "//s.adroll.com/j/" + adroll_adv_id + "/roundtrip.js";
        for(a=0;a<w.adroll.f.length;a++){
            w.adroll[w.adroll.f[a]]=w.adroll[w.adroll.f[a]]||(function(n){return function(){w.adroll.push([n,arguments])}})(w.adroll.f[a])};e=d.createElement('script');o=d.getElementsByTagName('script')[0];e.async=1;e.src=roundtripUrl;o.parentNode.insertBefore(e, o);})(window,document);
    adroll.track("pageView");
    {/literal}
</script>

<div id="prestashop-adroll-settings-userinfo">
    <div class="panel clearfix">
        <p class="userinfo-message pull-left">
        </p>
        <a class="btn btn-primary btn-sm pull-right"
           href="javascript:PrestashopAdrollSettings.openAdrollLogout();">
            {l s='Sign In as a different user' mod='adroll'}
        </a>
    </div>
</div>

<div id="prestashop-adroll-settings-sections">
    <div class="pas-section-loading">
        <div class="alert alert-info">
            <span>{l s='Please wait a few moments...' mod='adroll'}</span>
        </div>
    </div>

    <div class="pas-section-login">
        <div class="alert alert-warning">
            <p>
                <a class="btn btn-primary btn-lg"
                   href="javascript:PrestashopAdrollSettings.openAdrollLogin();">
                    {l s='Log in to your AdRoll account' mod='adroll'}
                </a>
            </p>
            <p class="signup-message">
            </p>
        </div>
    </div>

    <div class="pas-section-error">
        <div class="alert alert-warning">
            <span class="message"></span>
        </div>
    </div>

    <div class="pas-section-configured">
        <div class="panel">
            <span>
                <h2>
                    <i class="icon-check-circle"></i>
                    {l s='Congrats, your shop is connected to AdRoll!' mod='adroll'}
                </h2>
                <p>
                    {l s='We\'ve created dynamic ads featuring YOUR products and are ready to start showing them to visitors browsing your products.' mod='adroll'}
                </p>
                <p>
                    {l s='Launch your first campaign on AdRoll and increase your sales today!' mod='adroll'}
                </p>
                <p class="go-dashboard-info">
                    <a class="btn btn-primary btn-lg" href="#" target="_blank">
                        {l s='Go to your AdRoll dashboard' mod='adroll'}
                    </a>
                </p>
                <hr />
                <p class="current-advertisable-info"></p>
                <p class="start-onboarding-info">
                    <a class="btn btn-default" href="#">
                        {l s='Change advertiser profile' mod='adroll'}
                    </a>
                    <form action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" novalidate>
                        <input type="hidden" name="remove" value="1" />
                        <button type="submit" class="btn btn-danger">
                            {l s='Remove AdRoll from this shop' mod='adroll'}
                        </button>
                    </form>
                </p>
            </span>
        </div>
    </div>

    <div class="pas-section-onboarding">
        <div class="panel">
            <form>
                <p></p>
                <input type="hidden" name="shop_id" value="{$shop_id|escape:'htmlall':'UTF-8'}" />
                <input type="hidden" name="webservice_key" value="{$webservice_key|escape:'htmlall':'UTF-8'}" />
                <button type="submit" class="btn btn-primary btn-lg">
                    {l s='Connect with AdRoll' mod='adroll'}
                </button>
            </form>
        </div>
    </div>

    <form id="pas_form" class="hidden" action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" novalidate>
        <input type="hidden" name="adroll_advertisable_id" />
        <input type="hidden" name="adroll_pixel_id" />
    </form>
</div>
