<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Orders;
use App\User;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Tag;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class ComissionChartController extends ChartController
{
    public function setup()
    {
        \App::setLocale(session('locale'));

        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $drivers = User::where('is_driver',1)->get();
        $labels = [] ;
        foreach ($drivers as $driver)
        {
            $labels[] = $driver->name;
        }
        $this->chart->labels($labels);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/comissions'));

        // OPTIONAL
        $this->chart->minimalist(false);
        $this->chart->displayLegend(true);
    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
    public function data()
    {
        $drivers = User::where('is_driver',1)->get();
        $labelsMoney = [] ;
        foreach ($drivers as $driver)
        {
            $totalComission = Orders::where('driver_id', $driver->id)->where('comission_paid', 0 )->sum('comission');
            $labelsMoney[] = $totalComission;
        }

        $this->chart->dataset(trans('admin.Comission'), 'bar', $labelsMoney)->color('rgb(66, 186, 150, 1)')
            ->backgroundColor('rgb(66, 186, 150, 0.4)');
    }
}
