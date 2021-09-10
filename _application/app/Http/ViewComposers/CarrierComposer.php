<?php
namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Created by PhpStorm.
 * User: EL KADIRI
 * Date: 05/03/2018
 * Time: 16:39
 */
class CarrierComposer
{
    /**
     * The user repository implementation.
     *
     */
    protected $carriers;

    /**
     * Create a new profile composer.
     *
     * @return void
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        $carriers = DB::table('ps_carrier')
            ->where("deleted", "0")
            ->where("active", "1")
            ->orderBy("name")
            ->get();
        $this->carriers = $carriers;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('carriers', $this->carriers);
    }
}
