<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ClientAsset;
use App\Models\Company;
use App\Models\ContractSpaceHoure;
use App\Models\Roomassign;
use App\Models\Space;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class WorkspaceReportsController extends Controller
{
    /**
     * Generate and return a PDF report
     *
     * @param string $view The view to render
     * @param array $data Data to pass to the view
     * @param string $filename The PDF filename
     * @return mixed
     */
    protected function generatePdf($view, $data, $filename)
    {
        $bodyHtml = view($view, $data)->render();
        $finalHtml = '
        <html><head>
        <style>
            @page {
                margin-top: 100px;
                margin-bottom: 100px;
            }
            body { font-family: sans-serif; font-size: 12px; }
            
        </style>
        </head>
        <body>
            ' . $bodyHtml . '
        </body></html>';
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($finalHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Get owner ID and column based on user type
     *
     * @return array [ownerId, column]
     */
    protected function getOwnerInfo()
    {
        $user = \Auth::user();
        $ownerId = $user->type === 'company' ? $user->creatorId() : $user->ownedId();
        $column = ($user->type == 'company') ? 'created_by' : 'owned_by';
        
        return [$ownerId, $column];
    }

    /**
     * Spaces report
     *
     * @param Request $request
     * @return mixed
     */
    public function spacesReport(Request $request)
    {
        [$ownerId, $column] = $this->getOwnerInfo();
        $spaces = Space::where($column, $ownerId)->get();
        
        if ($request->filled('is_print') && $request->is_print == 1) {
            return $this->generatePdf(
                'workspace.reports.spacesReportPdf', 
                compact('spaces'), 
                'space_report.pdf'
            );
        }
        
        return view('workspace.reports.spacesReport', compact('spaces'));
    }

    /**
     * Client space report
     *
     * @param Request $request
     * @return mixed
     */
    public function clientspaceReport(Request $request)
    {
        $contracts = ContractSpaceHoure::where('hourly_rate','!=',0)->where('assign_hour','!=',0)->get();
        
        if ($request->filled('is_print') && $request->is_print == 1) {
            return $this->generatePdf(
                'workspace.reports.clientspaceReportPdf', 
                compact('contracts'), 
                'space_contracts_report.pdf'
            );
        }
        
        return view('workspace.reports.clientspaceReport', compact('contracts'));
    }

    /**
     * Vacant space report
     *
     * @param Request $request
     * @return mixed
     */
    public function vacantspaceReport(Request $request)
    {
        $user = \Auth::user();
        
        if ($user->type == 'company') {
            $a = Company::where('created_by', '=', $user->creatorId())->pluck('id');
            $assignroome = Roomassign::where('status', 'assign')->whereIN('company_id', $a)->groupBy('space_id');
            $b = $assignroome->pluck('space_id');
            $spaces = Space::where('created_by', '=', $user->creatorId())->whereNotIn('id', $b)->get();
        } else {
            $a = Company::where('owned_by', '=', $user->ownedId())->pluck('id');
            $assignroome = Roomassign::where('status', 'assign')->whereIN('company_id', $a)->groupBy('space_id');
            $b = $assignroome->pluck('space_id');
            $spaces = Space::where('owned_by', '=', $user->ownedId())->whereNotIn('id', $b)->get();
        }
        
        $assignroome->get();
        
        if ($request->filled('is_print') && $request->is_print == 1) {
            return $this->generatePdf(
                'workspace.reports.vacantspacePdf', 
                compact('spaces', 'assignroome'), 
                'vacant_space_report.pdf'
            );
        }
        
        return view('workspace.reports.vacantspaceReport', compact('spaces', 'assignroome'));
    }

    /**
     * Meeting space report
     *
     * @param Request $request
     * @return mixed
     */
    public function meetingspacereport(Request $request)
    {
        [$ownerId, $column] = $this->getOwnerInfo();
        $spaces = Space::where('meeting', 'yes')->where($column, $ownerId)->get();
        
        if ($request->filled('is_print') && $request->is_print == 1) {
            return $this->generatePdf(
                'workspace.reports.meetingspacereportPdf', 
                compact('spaces'), 
                'meeting_space_report.pdf'
            );
        }
        
        return view('workspace.reports.meetingspacereport', compact('spaces'));
    }

    /**
     * Asset report
     *
     * @param Request $request
     * @return mixed
     */
    public function assetreport(Request $request)
    {
        [$ownerId, $column] = $this->getOwnerInfo();
        $assets = ClientAsset::with('assetdetail')->where($column, $ownerId)->get();
        
        if ($request->filled('is_print') && $request->is_print == 1) {
            return $this->generatePdf(
                'workspace.reports.assetReportPdf', 
                compact('assets'), 
                'asset_report.pdf'
            );
        }
        
        return view('workspace.reports.assetReport', compact('assets'));
    }
}