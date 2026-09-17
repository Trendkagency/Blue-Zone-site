<?php

namespace App\Services;

use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmReportService
{
    /**
     * Get Leads Funnel and Conversion Report.
     */
    public function getLeadsReport(string $startDate, string $endDate): array
    {
        $base = CrmLead::whereBetween('crm_leads.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalLeads = (clone $base)->count();
        $convertedLeads = (clone $base)->where('crm_leads.status', 'converted')->count();
        $lostLeads = (clone $base)->where('crm_leads.status', 'lost')->count();
        $qualifiedLeads = (clone $base)->where('crm_leads.status', 'qualified')->count();

        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0.0;

        $bySource = (clone $base)
            ->leftJoin('crm_lead_sources', 'crm_leads.source_id', '=', 'crm_lead_sources.id')
            ->select('crm_lead_sources.name_en', 'crm_lead_sources.name_ar', DB::raw('COUNT(crm_leads.id) as count'))
            ->groupBy('crm_lead_sources.id', 'crm_lead_sources.name_en', 'crm_lead_sources.name_ar')
            ->orderByDesc('count')
            ->get();

        $byStatus = (clone $base)
            ->select('crm_leads.status', DB::raw('COUNT(crm_leads.id) as count'))
            ->groupBy('crm_leads.status')
            ->get();

        return [
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'lost_leads' => $lostLeads,
            'qualified_leads' => $qualifiedLeads,
            'conversion_rate' => $conversionRate,
            'by_source' => $bySource,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Get Opportunities & Pipeline Forecast Report.
     */
    public function getOpportunitiesReport(string $startDate, string $endDate): array
    {
        $base = CrmOpportunity::whereBetween('crm_opportunities.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalDeals = (clone $base)->count();
        $totalPipelineValue = (float) (clone $base)->where('crm_opportunities.status', 'open')->sum('crm_opportunities.value');
        $wonDeals = (clone $base)->where('crm_opportunities.status', 'won')->count();
        $wonValue = (float) (clone $base)->where('crm_opportunities.status', 'won')->sum('crm_opportunities.value');
        $lostDeals = (clone $base)->where('crm_opportunities.status', 'lost')->count();
        $lostValue = (float) (clone $base)->where('crm_opportunities.status', 'lost')->sum('crm_opportunities.value');

        $winRate = ($wonDeals + $lostDeals) > 0 ? round(($wonDeals / ($wonDeals + $lostDeals)) * 100, 2) : 0.0;
        $avgDealSize = $wonDeals > 0 ? round($wonValue / $wonDeals, 2) : 0.0;

        return [
            'total_deals' => $totalDeals,
            'open_pipeline_value' => $totalPipelineValue,
            'won_deals' => $wonDeals,
            'won_value' => $wonValue,
            'lost_deals' => $lostDeals,
            'lost_value' => $lostValue,
            'win_rate' => $winRate,
            'avg_deal_size' => $avgDealSize,
        ];
    }

    /**
     * Export Leads to CSV safely via stream (prevents memory exhaustion).
     */
    public function streamLeadsCsv(array $filters): StreamedResponse
    {
        $fileName = 'crm_leads_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic character compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Lead Number',
                'Full Name',
                'Email',
                'Phone',
                'Company',
                'Status',
                'Priority',
                'Estimated Value',
                'Owner',
                'Created At',
            ]);

            $query = CrmLead::with(['owner:id,name'])->latest();

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['owner_id'])) {
                $query->where('owner_id', $filters['owner_id']);
            }

            // Chunk in batches of 200 to prevent memory exhaustion
            $query->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->lead_number,
                        $lead->full_name,
                        $lead->email ?? 'N/A',
                        $lead->phone ?? 'N/A',
                        $lead->company_name ?? 'N/A',
                        ucfirst($lead->status),
                        ucfirst($lead->priority),
                        number_format($lead->estimated_value ?? 0, 2),
                        $lead->owner?->name ?? 'Unassigned',
                        $lead->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
