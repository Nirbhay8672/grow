<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCategory6Data extends Model
{
    protected $fillable = [
        'project_id',
        'total_open_area',
        'total_open_area_unit_id',
        'total_no_of_plots',
        'project_with_multiple_theme_phase',
        'phase_name',
        'plots_with_construction',
        'saleable_plot_from',
        'saleable_plot_to',
        'saleable_plot_unit_id',
        'show_carpet_plot_size',
        'carpet_plot_from',
        'carpet_plot_to',
        'carpet_plot_unit_id',
        'constructed_saleable_area_from',
        'constructed_saleable_area_to',
        'constructed_saleable_area_unit_id',
    ];

    protected function casts(): array
    {
        return [
            'project_with_multiple_theme_phase' => 'boolean',
            'plots_with_construction' => 'boolean',
            'show_carpet_plot_size' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function totalOpenAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'total_open_area_unit_id');
    }

    public function saleablePlotUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'saleable_plot_unit_id');
    }

    public function carpetPlotUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'carpet_plot_unit_id');
    }

    public function constructedSaleableAreaUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'constructed_saleable_area_unit_id');
    }
}

