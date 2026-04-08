<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private PushNotificationService $push) {}

    public function index(Request $request): View
    {
        $query = NotificationLog::with('user')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) =>
                $w->where('title', 'like', "%$q%")
                  ->orWhere('body', 'like', "%$q%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$q%"))
            );
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(25)->withQueryString();
        $types = NotificationLog::distinct()->pluck('type')->filter()->sort()->values();
        $stats = [
            'total'  => NotificationLog::count(),
            'unread' => NotificationLog::whereNull('read_at')->count(),
            'read'   => NotificationLog::whereNotNull('read_at')->count(),
        ];

        return view('dashboard.admin.Notifications.index', compact('notifications', 'types', 'stats'));
    }

    public function send(): View
    {
        $usersCount = User::where('is_active', true)->whereHas('deviceTokens')->count();
        return view('dashboard.admin.Notifications.send', compact('usersCount'));
    }

    public function broadcast(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'   => 'required|string|max:100',
            'body'    => 'required|string|max:500',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($data['user_id'] ?? null) {
            $user = User::findOrFail($data['user_id']);
            $this->push->sendToUser($user, $data['title'], $data['body']);

            NotificationLog::create([
                'user_id' => $user->id,
                'type'    => 'admin_broadcast',
                'title'   => $data['title'],
                'body'    => $data['body'],
            ]);

            return back()->with('success', "Notification envoyée à {$user->name}.");
        }

        $users = User::where('is_active', true)->whereHas('deviceTokens')->get();

        foreach ($users as $user) {
            $this->push->sendToUser($user, $data['title'], $data['body']);

            NotificationLog::create([
                'user_id' => $user->id,
                'type'    => 'admin_broadcast',
                'title'   => $data['title'],
                'body'    => $data['body'],
            ]);
        }

        return back()->with('success', "Notification envoyée à {$users->count()} utilisateurs.");
    }

    public function destroy(int $id): RedirectResponse
    {
        NotificationLog::findOrFail($id)->delete();
        return back()->with('success', 'Notification supprimée.');
    }
}
