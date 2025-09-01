<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\EmailTracking;
use App\Services\EmailDeliveryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class EmailDashboardController extends Controller
{
    protected EmailDeliveryService $deliveryService;

    public function __construct(EmailDeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Show dashboard
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentCampaigns = $this->getRecentCampaigns();
        $chartData = $this->getChartData();

        return view('email.dashboard', compact('stats', 'recentCampaigns', 'chartData'));
    }

    /**
     * Get dashboard statistics
     */
    protected function getDashboardStats(): array
    {
        return [
            'total_campaigns' => EmailCampaign::count(),
            'active_campaigns' => EmailCampaign::whereIn('status', ['sending', 'scheduled'])->count(),
            'total_subscribers' => EmailSubscriber::where('is_subscribed', true)->count(),
            'emails_sent_today' => EmailLog::whereDate('sent_at', today())
                ->where('status', 'sent')
                ->count(),
            'emails_sent_this_month' => EmailLog::whereMonth('sent_at', now()->month)
                ->where('status', 'sent')
                ->count(),
            'average_open_rate' => $this->getAverageOpenRate(),
            'average_click_rate' => $this->getAverageClickRate(),
            'bounce_rate' => $this->getBounceRate(),
        ];
    }

    /**
     * Get recent campaigns
     */
    protected function getRecentCampaigns()
    {
        return EmailCampaign::with(['template', 'logs'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'status' => $campaign->status,
                    'sent_count' => $campaign->sent_count,
                    'total_recipients' => $campaign->total_recipients,
                    'success_rate' => $campaign->success_rate,
                    'open_rate' => $campaign->open_rate,
                    'created_at' => $campaign->created_at->format('d.m.Y H:i'),
                ];
            });
    }

    /**
     * Get chart data for dashboard
     */
    protected function getChartData(): array
    {
        // Emails sent per day (last 30 days)
        $emailsPerDay = EmailLog::where('status', 'sent')
            ->where('sent_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(sent_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Campaign performance
        $campaignPerformance = EmailCampaign::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($campaign) {
                return [
                    'name' => $campaign->name,
                    'sent' => $campaign->sent_count,
                    'opens' => $campaign->opened_count,
                    'clicks' => $campaign->clicked_count,
                ];
            });

        return [
            'emails_per_day' => $emailsPerDay,
            'campaign_performance' => $campaignPerformance,
        ];
    }

    /**
     * Calculate average open rate
     */
    protected function getAverageOpenRate(): float
    {
        $campaigns = EmailCampaign::where('status', 'completed')
            ->where('sent_count', '>', 0)
            ->get();

        if ($campaigns->isEmpty()) return 0;

        $totalOpenRate = $campaigns->sum('open_rate');
        return round($totalOpenRate / $campaigns->count(), 2);
    }

    /**
     * Calculate average click rate
     */
    protected function getAverageClickRate(): float
    {
        $campaigns = EmailCampaign::where('status', 'completed')
            ->where('sent_count', '>', 0)
            ->get();

        if ($campaigns->isEmpty()) return 0;

        $totalClickRate = $campaigns->sum('click_rate');
        return round($totalClickRate / $campaigns->count(), 2);
    }

    /**
     * Calculate bounce rate
     */
    protected function getBounceRate(): float
    {
        $totalSent = EmailLog::where('status', 'sent')->count();
        if ($totalSent === 0) return 0;

        $bounced = EmailTracking::whereNotNull('bounced_at')->count();
        return round(($bounced / $totalSent) * 100, 2);
    }

    /**
     * Get campaign details
     */
    public function campaignDetails($id)
    {
        $campaign = EmailCampaign::with(['template', 'logs', 'tracking'])
            ->findOrFail($id);

        $stats = [
            'delivery_rate' => $campaign->success_rate,
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
            'unsubscribe_rate' => $campaign->sent_count > 0 
                ? round(($campaign->unsubscribed_count / $campaign->sent_count) * 100, 2) 
                : 0,
        ];

        $hourlyStats = $this->getHourlyStats($campaign);
        $failureReasons = $this->getFailureReasons($campaign);

        return view('email.campaign-details', compact('campaign', 'stats', 'hourlyStats', 'failureReasons'));
    }

    /**
     * Get hourly statistics for campaign
     */
    protected function getHourlyStats($campaign): array
    {
        return EmailLog::where('campaign_id', $campaign->id)
            ->where('status', 'sent')
            ->selectRaw('HOUR(sent_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    /**
     * Get failure reasons for campaign
     */
    protected function getFailureReasons($campaign): array
    {
        return EmailLog::where('campaign_id', $campaign->id)
            ->where('status', 'failed')
            ->selectRaw('error_message, COUNT(*) as count')
            ->groupBy('error_message')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Pause campaign
     */
    public function pauseCampaign($id): JsonResponse
    {
        try {
            $campaign = EmailCampaign::findOrFail($id);
            $this->deliveryService->pauseCampaign($campaign);

            return response()->json([
                'success' => true,
                'message' => 'Кампания приостановлена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Resume campaign
     */
    public function resumeCampaign($id): JsonResponse
    {
        try {
            $campaign = EmailCampaign::findOrFail($id);
            $this->deliveryService->resumeCampaign($campaign);

            return response()->json([
                'success' => true,
                'message' => 'Кампания возобновлена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get real-time stats via API
     */
    public function realtimeStats(): JsonResponse
    {
        $stats = $this->getDashboardStats();
        
        // Add queue information
        $stats['queue_size'] = \Illuminate\Support\Facades\Queue::size('emails');
        $stats['failed_jobs'] = \Illuminate\Support\Facades\Queue::size('failed');

        return response()->json($stats);
    }

    /**
     * Export campaign data
     */
    public function exportCampaign($id, Request $request)
    {
        $campaign = EmailCampaign::findOrFail($id);
        $format = $request->get('format', 'csv');

        $data = EmailLog::where('campaign_id', $campaign->id)
            ->with(['subscriber'])
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($campaign, $data);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($campaign, $data);
        }

        abort(400, 'Неподдерживаемый формат');
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($campaign, $data)
    {
        $filename = "campaign_{$campaign->id}_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'Email',
                'Status',
                'Sent At',
                'Opened At',
                'Clicked At',
                'Error Message'
            ]);

            // Data
            foreach ($data as $log) {
                $tracking = EmailTracking::where('campaign_id', $log->campaign_id)
                    ->where('subscriber_id', $log->subscriber_id)
                    ->first();

                fputcsv($file, [
                    $log->email,
                    $log->status,
                    $log->sent_at?->format('Y-m-d H:i:s'),
                    $tracking?->opened_at?->format('Y-m-d H:i:s'),
                    $tracking?->clicked_at?->format('Y-m-d H:i:s'),
                    $log->error_message
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
