<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::getGroup('notifications');

        return view('dashboard.admin.Settings.notifications', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_phone'  => 'nullable|string|max:20',
            'whatsapp_apikey' => 'nullable|string|max:100',
            'whatsapp_enabled'=> 'nullable|boolean',
        ]);

        Setting::setGroup('notifications', [
            'whatsapp_phone'   => $request->input('whatsapp_phone', ''),
            'whatsapp_apikey'  => $request->input('whatsapp_apikey', ''),
            'whatsapp_enabled' => $request->boolean('whatsapp_enabled') ? '1' : '0',
        ]);

        return back()->with('success', 'Paramètres de notification enregistrés.');
    }

    public function test(Request $request, WhatsAppService $whatsApp)
    {
        $result = $whatsApp->send(
            "✅ *Test de notification — Agri-Shop*\n\nLes notifications WhatsApp fonctionnent correctement !"
        );

        return back()->with(
            $result ? 'success' : 'error',
            $result
                ? 'Message de test envoyé avec succès sur WhatsApp !'
                : 'Échec de l\'envoi. Vérifiez le numéro et la clé API.'
        );
    }
}
