<?php

namespace App\Mail;

use App\Model\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerContractMail extends Mailable
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
        $this->subject('Seu imóvel foi alugado');
        $this->to($this->contract->property->owner_email, $this->contract->property->owner_name);
        return $this->markdown('mail.OwnerContract', ['contract' => $this->contract]);
    }
}
