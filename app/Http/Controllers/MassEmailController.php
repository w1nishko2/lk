<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MassEmailController extends Controller
{
    /**
     * Display mass email management page
     */
    public function index()
    {
        $stats = [
            'total' => EmailQueue::count(),
            'pending' => EmailQueue::pending()->count(),
            'sent' => EmailQueue::sent()->count(),
            'failed' => EmailQueue::failed()->count(),
        ];
        
        $recentSent = EmailQueue::sent()
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get();
            
        $recentFailed = EmailQueue::failed()
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('mass-email.index', compact('stats', 'recentSent', 'recentFailed'));
    }
    
    /**
     * Import emails from file
     */
    public function importEmails(Request $request)
    {
        try {
            Artisan::call('email:send-mass', ['--import' => true]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Email import started successfully',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Email import failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Email import failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send batch of emails
     */
    public function sendBatch(Request $request)
    {
        $batchSize = $request->input('batch_size', 5);
        
        try {
            Artisan::call('email:send-mass', [
                '--send' => true,
                '--batch' => $batchSize
            ]);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Email batch sent successfully',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Email batch sending failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Email batch sending failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get current statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => EmailQueue::count(),
            'pending' => EmailQueue::pending()->count(),
            'sent' => EmailQueue::sent()->count(),
            'failed' => EmailQueue::failed()->count(),
            'progress' => 0
        ];
        
        if ($stats['total'] > 0) {
            $stats['progress'] = round(($stats['sent'] / $stats['total']) * 100, 2);
        }
        
        return response()->json($stats);
    }
    
    /**
     * Reset failed emails
     */
    public function resetFailed()
    {
        try {
            $count = EmailQueue::failed()->update([
                'status' => 'pending',
                'error_message' => null,
                'attempts' => 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Reset {$count} failed emails to pending status"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset emails: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all email queue
     */
    public function clearQueue()
    {
        try {
            $count = EmailQueue::count();
            EmailQueue::truncate();
            
            return response()->json([
                'success' => true,
                'message' => "Cleared {$count} emails from queue"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear queue: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Preview email template
     */
    public function previewTemplate()
    {
        return view('emails.mass-email');
    }
}
