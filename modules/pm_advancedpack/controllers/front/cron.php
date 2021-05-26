<?php
/**
 * Advanced Pack 5
 *
 * @author    Presta-Module.com <support@presta-module.com> - http://www.presta-module.com
 * @copyright Presta-Module 2017 - http://www.presta-module.com
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
class pm_advancedpackcronModuleFrontController extends ModuleFrontController
{
    public $ajax = true;
    public $display_header = false;
    public $display_footer = false;
    public $display_column_left = false;
    public $display_column_right = false;
    public function init()
    {
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('X-Robots-Tag: noindex, nofollow', true);
        header('Content-type: application/json');
        if (!Tools::isPHPCLI()) {
            $secureKey = Configuration::getGlobalValue('PM_AP5_SECURE_KEY');
            if (empty($secureKey) || $secureKey !== Tools::getValue('secure_key')) {
                Tools::redirect('404');
                die;
            }
        }
        set_time_limit(0);
        $start_memory = memory_get_usage();
        $time_start = microtime(true);
        $idPackList = $this->module->getPackIdToUpdate();
        foreach ($idPackList as $idPack) {
            AdvancedPack::addPackSpecificPrice((int)$idPack, 0);
        }
        $this->module->cleanPackIdToUpdate();
        die(Tools::jsonEncode(array(
            'result' => true,
            'source' => (Tools::isPHPCLI() ? 'cli' : 'web'),
            'elasped_time' => round((microtime(true) - $time_start)*1000, 2),
            'memory_usage' => round((memory_get_usage() - $start_memory)/1024/1024, 2),
            'updated_packs' => count($idPackList),
        )));
    }
}
