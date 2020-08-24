<?php

namespace App\Mail;

use App\Model\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantContractMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $contract;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Contrato de locação');
        $this->to($this->contract->tenant_email, $this->contract->tenant_name);
        return $this->markdown('mail.TenantContract', ['contract' => $this->contract]);
    }
}
