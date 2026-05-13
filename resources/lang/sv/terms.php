<?php

return [
    'title' => 'Köpvillkor',
    'content' => '
        <h1 class="mb-4 text-center">Köpvillkor</h1>

        <p>Dessa villkor reglerar köp av produkter som erbjuds via webbplatsen ' . config('app.name') . ', i enlighet med gällande spansk lagstiftning.</p>

        <hr>

        <h4>1. Ägaridentifiering</h4>
        <p>
            <strong>Företagsnamn:</strong> ' . config('app.name') . '<br>
            <strong>Kontakt e-post:</strong> ' . __('website.email_address') . '<br>
        </p>

        <h4>2. Syfte</h4>
        <p>
            Detta dokument reglerar avtalsvillkoren för produkter via denna onlinebutik.
            Att lägga en beställning innebär fullt accepterande av dessa villkor.
        </p>

        <h4>3. Köpprocess</h4>
        <ul>
            <li>Val av produkter och lägg i varukorgen.</li>
            <li>Ange fakturerings- och leveransinformation.</li>
            <li>Bekräftelse av beställning och godkännande av villkor.</li>
            <li>Betalning med tillgängliga metoder på plattformen.</li>
            <li>Mottagande av bekräftelsemejl.</li>
        </ul>

        <h4>4. Priser</h4>
        <p>
            Alla priser anges i Euro (' . config('app.currency_symbol', '€') . ') och inkluderar tillämpliga skatter (moms), om inte annat anges.
            Fraktkostnader visas innan köpet slutförs.
        </p>

        <h4>5. Betalningsmetoder</h4>
        <p>
            Betalningsmetoder som anges i köpprocessen accepteras.
            Betalningar görs via säkra plattformar.
        </p>

        <h4>6. Leverans</h4>
        <ul>
            <li>Beställningar behandlas på arbetsdagar.</li>
            <li>Beräknad leveranstid anges innan köpet slutförs.</li>
            <li>Vi ansvarar inte för förseningar som kan hänföras till transportföretaget.</li>
        </ul>

        <h4>7. Ångerrätt</h4>
        <p>
            Enligt spansk lagstiftning har kunden en period på <strong>14 kalenderdagar</strong> från mottagandet av beställningen att utöva sin ångerrätt utan motivering.
        </p>
        <p>
            För att utöva denna rätt måste du meddela oss via e-post på ' . __('website.email_address') . '.
            Produkten måste returneras i perfekt skick.
        </p>

        <h4>8. Returer och återbetalningar</h4>
        <p>
            Återbetalningen kommer att göras med samma betalningsmetod som kunden använde,
            inom högst 14 dagar från mottagandet av den returnerade produkten.
        </p>

        <h4>9. Garantier</h4>
        <p>
            Alla produkter har den lagstadgade garantin för överensstämmelse i enlighet med gällande regler.
        </p>

        <h4>10. Dataskydd</h4>
        <p>
            Personuppgifter kommer att behandlas i enlighet med vår Integritetspolicy och den allmänna dataskyddsförordningen (GDPR).
        </p>

        <h4>11. Immateriella rättigheter</h4>
        <p>
            Allt innehåll på webbplatsen (texter, bilder, logotyper, design) ägs av ägaren
            och skyddas av immaterialrättsliga regler.
        </p>

        <h4>12. Tillämplig lag och jurisdiktion</h4>
        <p>
            Dessa villkor styrs av spansk lag.
            Vid konflikt kommer parterna att underkasta sig konsumentens hemvists domstolar.
        </p>

        <hr>

        <p class="text-muted small">
            Senast uppdaterad: ' . now()->format('d/m/Y') . '
        </p>
    ',
];
