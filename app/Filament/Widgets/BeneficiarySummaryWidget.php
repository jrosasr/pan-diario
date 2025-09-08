<?php
namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\Beneficiary;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class BeneficiarySummaryWidget extends Widget
{
    protected static string $view = 'filament.widgets.beneficiary-summary-widget';
    public $startDate;
    public $endDate;
    public $summary = [
        'men' => 0,
        'women' => 0,
        'boys' => 0,
        'girls' => 0,
        'beneficiaries' => [],
    ];

    public function mount($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now('America/Caracas')->startOfMonth()->toDateString();
        $this->endDate = $endDate ?? Carbon::now('America/Caracas')->endOfMonth()->toDateString();
        $this->updateSummary();
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate'])) {
            $this->updateSummary();
        }
    }

    public function updateSummary()
    {
        $deliveries = Delivery::whereBetween('delivered_at', [$this->startDate, $this->endDate])->get();
        $this->summary['men'] = $deliveries->sum('men_count');
        $this->summary['women'] = $deliveries->sum('women_count');
        $this->summary['boys'] = $deliveries->sum('boys_count');
        $this->summary['girls'] = $deliveries->sum('girls_count');
        $beneficiaryIds = $deliveries->pluck('beneficiary_id')->filter()->unique();
        $this->summary['beneficiaries'] = Beneficiary::whereIn('id', $beneficiaryIds)->get();
    }
}
