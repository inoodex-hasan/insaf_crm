<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    use HasFactory;

    protected $table = 'partner_commissions';

    protected $fillable = [
        'commission_type',
        'direction',
        'agency_referral_type',
        'partner_agency_id',
        'university_id',
        'student_id',
        'application_id',
        'country_id',
        'handover_officer_id',
        'rate_type',
        'rate_value',
        'base_amount',
        'currency',
        'exchange_rate',
        'total_commission',
        'total_amount_bdt',
        'paid_amount_bdt',
        'due_amount_bdt',
        'workflow_status',
        'payment_status',
        'handover_date',
        'processing_stage',
        'agreement_reference',
        'processing_notes',
        'created_by',
    ];

    protected $casts = [
        'rate_value' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'total_commission' => 'decimal:2',
        'total_amount_bdt' => 'decimal:2',
        'paid_amount_bdt' => 'decimal:2',
        'due_amount_bdt' => 'decimal:2',
        'handover_date' => 'date',
    ];

    public function partnerAgency()
    {
        return $this->belongsTo(PartnerAgency::class, 'partner_agency_id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function handoverOfficer()
    {
        return $this->belongsTo(User::class, 'handover_officer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function settlements()
    {
        return $this->hasMany(PartnerCommissionSettlement::class, 'partner_commission_id')->orderBy('settlement_date', 'desc');
    }

    /**
     * Recalculate settlement amounts and status
     */
    public function recalculateBalances(): void
    {
        $paid = (float) $this->settlements()->sum('amount_bdt');
        $total = (float) $this->total_amount_bdt;
        $due = max(0, $total - $paid);

        $paymentStatus = 'unpaid';
        if ($paid >= $total && $total > 0) {
            $paymentStatus = 'paid';
        } elseif ($paid > 0) {
            $paymentStatus = 'partial';
        }

        $this->update([
            'paid_amount_bdt' => $paid,
            'due_amount_bdt' => $due,
            'payment_status' => $paymentStatus,
        ]);
    }

    // Scopes
    public function scopeUniversity($query)
    {
        return $query->where('commission_type', 'university');
    }

    public function scopeAgency($query)
    {
        return $query->where('commission_type', 'agency');
    }

    public function scopeReceivable($query)
    {
        return $query->where('direction', 'receivable');
    }

    public function scopePayable($query)
    {
        return $query->where('direction', 'payable');
    }
}
