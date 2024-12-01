<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'monthly_price',
        'response_limit',
        'features'
    ];

    protected $casts = [
        'monthly_price' => 'float',
        'response_limit' => 'integer',
        'features' => 'array'
    ];

    // İlişkiler
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Yardımcı metodlar
    public function isPro()
    {
        return $this->name === 'Pro';
    }

    public function isFree()
    {
        return $this->name === 'Free';
    }

    public function hasUnlimitedResponses()
    {
        return $this->response_limit === -1;
    }

    public function hasFeature($featureName)
    {
        return in_array($featureName, $this->features);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Factory metodları
    public static function createFreePlan()
    {
        return self::create([
            'name' => 'Free',
            'monthly_price' => 0,
            'response_limit' => config('chat.default_plan_limit'),
            'features' => [
                'basic_chat',
                'export_txt'
            ]
        ]);
    }

    public static function createProPlan()
    {
        return self::create([
            'name' => 'Pro',
            'monthly_price' => 9.99,
            'response_limit' => -1, // Sınırsız
            'features' => [
                'basic_chat',
                'priority_support',
                'export_txt',
                'export_pdf',
                'custom_responses',
                'advanced_analytics'
            ]
        ]);
    }

    // İstatistikler
    public function getSubscriberCount()
    {
        return $this->users()->count();
    }

    public function getActiveSubscriberCount()
    {
        return $this->users()
                    ->where('last_login_at', '>=', now()->subDays(30))
                    ->count();
    }

    public function getMonthlyRevenue()
    {
        return $this->monthly_price * $this->getSubscriberCount();
    }

    public function getFeatureList()
    {
        $featureDescriptions = [
            'basic_chat' => 'Temel sohbet özellikleri',
            'priority_support' => 'Öncelikli destek',
            'export_txt' => 'TXT formatında dışa aktarma',
            'export_pdf' => 'PDF formatında dışa aktarma',
            'custom_responses' => 'Özel yanıtlar',
            'advanced_analytics' => 'Gelişmiş analitik'
        ];

        $list = [];
        foreach ($this->features as $feature) {
            $list[$feature] = $featureDescriptions[$feature] ?? $feature;
        }

        return $list;
    }

    public function toPresenter()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => [
                'monthly' => $this->monthly_price,
                'annual' => $this->monthly_price * 12 * 0.8, // 20% yıllık indirim
                'currency' => 'TRY'
            ],
            'response_limit' => $this->response_limit,
            'features' => $this->getFeatureList(),
            'subscriber_count' => $this->getSubscriberCount(),
            'is_popular' => $this->is_popular,
            'is_recommended' => $this->is_recommended,
            'trial_days' => $this->trial_days
        ];
    }

    // Plan karşılaştırma metodları
    public function compareTo(Plan $otherPlan)
    {
        return [
            'price_difference' => $this->monthly_price - $otherPlan->monthly_price,
            'additional_features' => array_diff($this->features, $otherPlan->features),
            'missing_features' => array_diff($otherPlan->features, $this->features),
            'response_limit_difference' => $this->response_limit - $otherPlan->response_limit
        ];
    }

    // Plan geçiş metodları
    public function canUpgradeTo(Plan $newPlan)
    {
        return $this->monthly_price < $newPlan->monthly_price;
    }

    public function canDowngradeTo(Plan $newPlan)
    {
        return $this->monthly_price > $newPlan->monthly_price;
    }

    // Plan özellik yönetimi
    public function addFeature($feature)
    {
        if (!in_array($feature, $this->features)) {
            $this->features[] = $feature;
            $this->save();
        }
    }

    public function removeFeature($feature)
    {
        $this->features = array_diff($this->features, [$feature]);
        $this->save();
    }

    // Fiyatlandırma metodları
    public function getAnnualPrice()
    {
        return $this->monthly_price * 12 * 0.8; // 20% yıllık indirim
    }

    public function getTrialDays()
    {
        return $this->trial_days ?? 0;
    }

    public function hasFreeTrial()
    {
        return $this->getTrialDays() > 0;
    }

    // Admin metodları
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function setAsPopular($isPopular = true)
    {
        $this->is_popular = $isPopular;
        $this->save();
    }

    public function setAsRecommended($isRecommended = true)
    {
        $this->is_recommended = $isRecommended;
        $this->save();
    }
}