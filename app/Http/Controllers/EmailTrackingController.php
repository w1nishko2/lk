<?php

namespace App\Http\Controllers;

use App\Models\EmailTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    /**
     * Track email open
     */
    public function trackOpen($trackingId, Request $request): Response
    {
        $tracking = EmailTracking::where('tracking_id', $trackingId)->first();
        
        if ($tracking) {
            $tracking->markAsOpened(
                $request->header('User-Agent'),
                $request->ip()
            );
        }

        // Return 1x1 transparent pixel
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        
        return response($pixel, 200, [
            'Content-Type' => 'image/png',
            'Content-Length' => strlen($pixel),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Track email click
     */
    public function trackClick($trackingId, Request $request)
    {
        $originalUrl = $request->get('url');
        
        if (!$originalUrl) {
            abort(400, 'URL parameter is required');
        }

        $tracking = EmailTracking::where('tracking_id', $trackingId)->first();
        
        if ($tracking) {
            $tracking->markAsClicked([
                'url' => $originalUrl,
                'user_agent' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'clicked_at' => now(),
            ]);
        }

        return redirect($originalUrl);
    }

    /**
     * Unsubscribe from emails
     */
    public function unsubscribe($trackingId, Request $request)
    {
        $tracking = EmailTracking::where('tracking_id', $trackingId)->first();
        
        if (!$tracking) {
            abort(404, 'Tracking record not found');
        }

        if ($request->isMethod('post')) {
            $tracking->markAsUnsubscribed();
            $tracking->subscriber->update(['is_subscribed' => false]);
            
            return view('email.unsubscribed', compact('tracking'));
        }

        return view('email.unsubscribe', compact('tracking'));
    }
}
