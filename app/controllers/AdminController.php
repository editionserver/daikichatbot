<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Chat;
use App\Models\Plan;
use App\Models\CustomResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = Cache::remember('admin_dashboard_stats', 3600, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::active()->count(),
                'total_chats' => Chat::count(),
                'total_messages' => Message::count(),
                'pro_users' => User::whereHas('plan', function ($q) {
                    $q->where('name', 'Pro');
                })->count(),
                'recent_chats' => Chat::with('user')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get(),
                'popular_queries' => $this->getPopularQueries(),
                'system_health' => $this->getSystemHealth()
            ];
        });

        return view('admin.dashboard', compact('stats'));
    }

    // Kullanıcı Yönetimi
    public function users()
    {
        $users = User::with('plan')
                    ->withCount('chats')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $plans = Plan::all();

        return view('admin.users.edit', compact('user', 'plans'));
    }

    public function updateUser($id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'plan_id' => 'required|exists:plans,id',
            'is_admin' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user->update(request()->all());

        return redirect()->route('admin.users.index')
                        ->with('success', 'Kullanıcı başarıyla güncellendi');
    }

    // Plan Yönetimi
    public function plans()
    {
        $plans = Plan::withCount('users')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.plans.create');
    }

    public function storePlan()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|unique:plans',
            'monthly_price' => 'required|numeric|min:0',
            'response_limit' => 'required|integer|min:-1',
            'features' => 'array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        Plan::create(request()->all());

        return redirect()->route('admin.plans.index')
                        ->with('success', 'Plan başarıyla oluşturuldu');
    }

    // Özel Yanıtlar Yönetimi
    public function customResponses()
    {
        $responses = CustomResponse::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.responses.index', compact('responses'));
    }

    public function createCustomResponse()
    {
        return view('admin.responses.create');
    }

    public function storeCustomResponse()
    {
        $validator = Validator::make(request()->all(), [
            'keyword' => 'required|string|unique:custom_responses',
            'response_text' => 'required|string',
            'attachments' => 'array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $response = CustomResponse::create(request()->all());

        // Dosya eklerini işle
        if (request()->hasFile('files')) {
            foreach (request()->file('files') as $file) {
                $path = $file->store('responses', 'public');
                $response->addAttachment($path, $file->getClientOriginalName());
            }
        }

        return redirect()->route('admin.responses.index')
                        ->with('success', 'Özel yanıt başarıyla oluşturuldu');
    }

    // Analitik ve Raporlar
    public function analytics()
    {
        $dateRange = request('date_range', '7days');
        $stats = $this->getAnalytics($dateRange);
        
        return view('admin.analytics', compact('stats'));
    }

    protected function getAnalytics($dateRange)
    {
        $query = Chat::query();
        
        switch ($dateRange) {
            case '7days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case '30days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'all':
                break;
        }

        return [
            'chat_count' => $query->count(),
            'message_distribution' => $this->getMessageDistribution($query),
            'user_activity' => $this->getUserActivity($dateRange),
            'response_times' => $this->getResponseTimes($query),
            'popular_topics' => $this->getPopularTopics($query)
        ];
    }

    // Sistem Ayarları
    public function settings()
    {
        $settings = [
            'app' => config('app'),
            'chat' => config('chat'),
            'openai' => [
                'model' => config('openai.model'),
                'temperature' => config('openai.temperature')
            ]
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings()
    {
        $validator = Validator::make(request()->all(), [
            'app_name' => 'required|string',
            'guest_limit' => 'required|integer|min:1',
            'default_plan_limit' => 'required|integer|min:1',
            'openai_model' => 'required|string',
            'openai_temperature' => 'required|numeric|between:0,2'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Ayarları güncelle
        $this->updateConfigFile(request()->all());

        return back()->with('success', 'Ayarlar başarıyla güncellendi');
    }

    // Yardımcı metodlar
    protected function getPopularQueries()
    {
        return Message::where('is_bot', false)
                     ->selectRaw('content, COUNT(*) as count')
                     ->groupBy('content')
                     ->orderBy('count', 'desc')
                     ->limit(10)
                     ->get();
    }

    protected function getSystemHealth()
    {
        return [
            'disk_usage' => disk_free_space('/') / disk_total_space('/') * 100,
            'memory_usage' => memory_get_usage(true) / 1024 / 1024,
            'average_response_time' => $this->getAverageResponseTime(),
            'error_rate' => $this->getErrorRate()
        ];
    }

    protected function getMessageDistribution($query)
    {
        return $query->withCount([
            'messages',
            'messages as bot_messages_count' => function ($q) {
                $q->where('is_bot', true);
            },
            'messages as user_messages_count' => function ($q) {
                $q->where('is_bot', false);
            }
        ])->get();
    }

    protected function getUserActivity($dateRange)
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                  ->groupBy('date')
                  ->orderBy('date', 'desc')
                  ->limit(30)
                  ->get();
    }

    protected function getResponseTimes($query)
    {
        $times = [];
        $chats = $query->with(['messages' => function ($q) {
            $q->orderBy('created_at');
        }])->get();

        foreach ($chats as $chat) {
            $times[] = $chat->calculateAverageResponseTime();
        }

        return collect($times)->average();
    }

    protected function getPopularTopics($query)
    {
        return Message::whereIn('chat_id', $query->pluck('id'))
                     ->where('is_bot', false)
                     ->selectRaw('SUBSTRING_INDEX(content, " ", 3) as topic, COUNT(*) as count')
                     ->groupBy('topic')
                     ->orderBy('count', 'desc')
                     ->limit(10)
                     ->get();
    }
}