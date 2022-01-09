<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Orders;
use App\User;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\NewsCRUD\app\Models\Category;
use Backpack\NewsCRUD\app\Models\Tag;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class NewEntriesChartController extends ChartController
{
    public function setup()
    {
        \App::setLocale(session('locale'));

        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $labels = [];
        for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
            if ($days_backwards == 1) {
            }
            $labels[] = $days_backwards.' days ago';
        }
        $this->chart->labels($labels);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/new-entries'));

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
        for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
            // Could also be an array_push if using an array rather than a collection.
            $users[] = Orders::whereDate('created_at', today()->subDays($days_backwards))->where('status',2)
                            ->count();
            $articles[] = Orders::whereDate('created_at', today()->subDays($days_backwards))->where('status',8)->whereNoTNull('driver_id')
                            ->count();
            $categories[] = Orders::whereDate('created_at', today()->subDays($days_backwards))->where('status',8)
                            ->count();
            $paidtags[] = Orders::whereDate('created_at', today()->subDays($days_backwards))->where('is_paid',1)
                            ->count();
            $notpaidtags[] = Orders::whereDate('created_at', today()->subDays($days_backwards))->where('is_paid',0)
                            ->count();
        }

        $this->chart->dataset(trans('admin.new Orders'), 'line', $users)
            ->color('rgb(66, 186, 150)')
            ->backgroundColor('rgba(66, 186, 150, 0.4)');

        $this->chart->dataset(trans('admin.Assigned Orders'), 'line', $articles)
            ->color('rgb(96, 92, 168)')
            ->backgroundColor('rgba(96, 92, 168, 0.4)');

        $this->chart->dataset(trans('admin.In Progress Orders'), 'line', $categories)
            ->color('rgb(255, 193, 7)')
            ->backgroundColor('rgba(255, 193, 7, 0.4)');

        $this->chart->dataset(trans('admin.Paid Orders'), 'line', $paidtags)
            ->color('rgba(70, 127, 208, 1)')
            ->backgroundColor('rgba(70, 127, 208, 0.4)');

        $this->chart->dataset(trans('admin.Not Paird Orders'), 'line', $notpaidtags)
            ->color('rgba(70, 127, 208, 1)')
            ->backgroundColor('rgba(70, 127, 208, 0.4)');
    }
}
