@component('mail::message')
Olá {{$contract->property->owner_name}}.

Seu imóvel foi alugado.

Segue as informações do Contrato de Locação.

@component('mail::panel')

<p><b>Número do Contrato:</b></p>
<div>{{$contract->id}}</div>
<br>

<p><b>Locatário:</b></p>
<div>{{$contract->tenant_name}}</div>
<div>{{$contract->tenant_email}}</div>
<br>

<p><b>Proprietário:</b></p>
<div>{{$contract->property->owner_name}}</div>
<div>{{$contract->property->owner_email}}</div>
<br>

<p><b>Propriedade:</b></p>
<br>
<div>{{$contract->property->address_street}}, {{$contract->property->address_number}}, {{$contract->property->address_neighborhood}}, {{$contract->property->address_city}}, {{$contract->property->address_state}}</div>

@endcomponent

@component('mail::button', ['url' => route('contract.view', ['id'=> $contract->id]) ])
Visualizar o Contrato
@endcomponent

Qualquer duvida estamos a disposição.
@endcomponent
