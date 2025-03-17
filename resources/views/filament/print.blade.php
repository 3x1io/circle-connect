<div>
    @if($this->type === 'document')
        <div class="prose max-w-full">
            {!! $this->record->body !!}
        </div>
    @else
        <h1 class="underline text-lg">Kundennummer: {{$this->record->account->id}}</h1>
        <br />
        <h1 class="underline text-lg">Auftragsbestätigung</h1>
        <p class="text-sm">
            Wir bedanken uns für Ihren telefonischen Auftrag und sichern Ihnen als Kunde unseres Hauses eine stets zuvorkommende
            Bedienung und ptinktliche Lieferung zu.
        </p>
        <br />
        <div class="flex justify-between ">
            <div class="w-full">
                <h1>Rechnungsanschrift:</h1>
                <h1>Firma:</h1>
                <h1>Kunde: <b>{{ $this->record->account->meta('letter_salutation') . " " . $this->record->account->meta('first_name') . " " . $this->record->account->meta('last_name') }}</b></h1>
                <h1>Straße: <b>{{ $this->record->account->meta('street') . " " . $this->record->account->meta('number') }}</b></h1>
                <h1>PLZ / Ort: <b>{{ $this->record->account->meta('postcode') . " " . $this->record->account->meta('city') }}</b></h1>
            </div>
            <div class="w-full">
                <h1>Lieferanschrift:</h1>
                <h1>Firma:</h1>
                @if($this->record->account->meta('other_address'))
                    <h1>Kunde: <b>{{ $this->record->account->meta('letter_salutation') . " " . $this->record->account->meta('first_name') . " " . $this->record->account->meta('last_name') }}</b></h1>
                    <h1>Straße: <b>{{ $this->record->account->meta('other_address_address') . " " . $this->record->account->meta('other_address_number') }}</b></h1>
                    <h1>PLZ / Ort: <b>{{ $this->record->account->meta('other_address_postcode') . " " . $this->record->account->meta('other_address_city') }}</b></h1>
                @else
                    <h1>Kunde: <b>{{ $this->record->account->meta('letter_salutation') . " " . $this->record->account->meta('first_name') . " " . $this->record->account->meta('last_name') }}</b></h1>
                    <h1>Straße: <b>{{ $this->record->account->meta('street') . " " . $this->record->account->meta('number') }}</b></h1>
                    <h1>PLZ / Ort: <b>{{ $this->record->account->meta('postcode') . " " . $this->record->account->meta('city') }}</b></h1>
                @endif
            </div>
        </div>
        <br />
        <div class="border border-black"></div>
        <div class="flex justify-between py-2">
            <div>
                Lieferdatum: <b>sofort</b>
            </div>
            <div>
                Fachberater: <b>israil 1-</b>
            </div>
            <div>
                Datum:  <b>{{$this->record->created_at->format('d.m.Y')}}</b>
            </div>
        </div>
        <div class="border border-black"></div>
        <br />
        <div class="flex flex-col">
            <div class="flex justify-between">
                <div class="flex justify-start gap-2">
                    <div>
                        Rechnung:
                    </div>
                    @if($this->record->account->meta('payment_type') === 'invoice')
                        <div class="flex flex-col justify-center items-center">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    @endif
                </div>
                <div class="flex justify-start gap-2">
                    <div>
                        Kundenkarte:
                    </div>
                    @if($this->record->account->meta('payment_type') === 'customer_card')
                        <div class="flex flex-col justify-center items-center">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex justify-between">
                <div>
                    Kontoinhaber: {{ $this->record->account->meta('owner') }}
                </div>
                <div>
                    Bank: {{ $this->record->account->meta('bank') }}
                </div>
            </div>
            <div class="flex justify-between">
                <div>
                    IBAN: {{ $this->record->account->meta('iban') }}
                </div>
                <div>
                    BIC: {{ $this->record->account->meta('bic') }}
                </div>
            </div>
            <div class="flex justify-between">
                <div>
                    Kreditkarte
                </div>
                <div class="flex justify-start">
                    <div>
                        Amex:
                    </div>
                    @if( $this->record->account->meta('credit_type') === 'amex')
                        <div class="flex flex-col justify-center items-center">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    @endif
                </div>
                <div class="flex justify-start">
                    <div>
                        MasterCard:
                    </div>
                    @if($this->record->account->meta('credit_type') === 'mastercard' )
                        <div class="flex flex-col justify-center items-center">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    @endif
                </div>
                <div class="flex justify-start">
                    <div>
                        Visa:
                    </div>
                    @if($this->record->account->meta('credit_type') === 'visa' )
                        <div class="flex flex-col justify-center items-center">
                            <i class="bx bx-check-circle"></i>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex justify-between">
                <div>
                    Karteninhaber: {{$this->record->account->meta('credit_owner')}}
                </div>
            </div>
            <div class="flex justify-between">
                <div>
                    Karten-Nr.: {{$this->record->account->meta('credit_number')}}
                </div>
                <div>
                    Gültig bis: {{$this->record->account->meta('credit_validity_date')}}
                </div>
                <div>
                    Prüf-Nr.: {{$this->record->account->meta('credit_check')}}
                </div>
            </div>
        </div>
        <h1><b>Bemerkung: sofort</b></h1>
        <p class="text-sm">
            Bitte überprüfen Sie Ihre Liefer- und Rechnungsanschrift u. Mengen  sollten diese Fehler enthalten, bitten wir ggf. um Korrektur.
            Unsere geltenden AGB und darin enthaltene Informationen zum Widerrufsrecht erhalten Sie auf www.chateau-royal.de oder per Fax.
        </p>
        <table class="border border-black w-full mt-4">
            <thead>
            <tr>
                <th class="border border-black p-1">Artikel</th>
                <th class="border border-black p-1">Menge</th>
                <th class="border border-black p-1">Einzelpreis €</th>
                <th class="border border-black p-1">Gesamtpreis €</th>
            </tr>
            </thead>
            <tbody>
            @foreach($this->record->items as $item)
                <tr class="border border-black">
                    <td class="border border-black p-1">{{ $item->item }}</td>
                    <td class="border border-black p-1">{{ $item->quantity }}</td>
                    <td class="border border-black p-1">{{ number_format($item->price, 2) }}€</td>
                    <td class="border border-black p-1"><b>{{ number_format($item->total, 2) }}€</b></td>
                </tr>
            @endforeach
            <tr class="border border-black">
                <td class="border border-black p-1">Warentransportversicherung</td>
                <td class="border border-black p-1">1</td>
                <td class="border border-black p-1">1% vom Netto</td>
                <td class="border border-black p-1">Freihaus</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black p-1">Versandkosten</td>
                <td class="border border-black p-1">1</td>
                <td class="border border-black p-1"></td>
                <td class="border border-black p-1">Freihaus</td>
            </tr>
            <tr class="border border-black">
                <td class="border border-black p-1" colspan="3">Gesamt</td>
                <td class="border border-black p-1" ><b>{{number_format($this->record->total, 2)}}€</b></td>
            </tr>
            </tbody>
        </table>
        <br />
        <div class="flex justify-between p-4 text-sm">
            <div></div>
            <div class="flex justify-end">
                Kundenunterschrift: ___________________________
            </div>
        </div>
    @endif
</div>
