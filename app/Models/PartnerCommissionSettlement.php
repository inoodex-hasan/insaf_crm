<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommissionSettlement extends Model
{
    use HasFactory;

    protected $table = 'partner_commission_settlements';

    protected $fillable = [
        'partner_commission_id',
        'office_account_id',
        'journal_entry_id',
        'settlement_type',
        'amount_bdt',
        'payment_method',
        'reference_no',
        'settlement_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount_bdt' => 'decimal:2',
        'settlement_date' => 'date',
    ];

    public function commission()
    {
        return $this->belongsTo(PartnerCommission::class, 'partner_commission_id');
    }

    public function officeAccount()
    {
        return $this->belongsTo(OfficeAccount::class, 'office_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
