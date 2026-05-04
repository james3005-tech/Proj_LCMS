<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCase extends Model
{
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'title',
        'case_number',
        'status',
        'description',
        'client_id',
        'lawyer_id',
        'filed_date',
        'denial_reason',
    ];

    protected $casts = [
        'filed_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class, 'case_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'active'    => 'badge-active',
            'pending'   => 'badge-pending',
            'closed'    => 'badge-closed',
            'dismissed' => 'badge-dismissed',
            default     => 'badge-pending',
        };
    }
}