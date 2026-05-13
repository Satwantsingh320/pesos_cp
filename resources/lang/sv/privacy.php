<?php

return [
    'title' => 'Integritetspolicy',
    'content' => '
        <h1 class="mb-4 text-center">Integritetspolicy</h1>

        <p>I enlighet med förordning (EU) 2016/679 (GDPR), organisk lag 3/2018 (LOPDGDD) och lag 34/2002 (LSSI-CE), informerar vi dig om behandlingen av dina personuppgifter på denna webbplats.</p>

        <hr>

        <h4>1. Personuppgiftsansvarig</h4>
        <p>
            <strong>Företagsnamn:</strong> ' . config('app.name') . '<br>
            <strong>E-post:</strong> ' . __('website.email_address') . '<br>
        </p>

        <h4>2. Uppgifter vi samlar in</h4>
        <ul>
            <li>Identifieringsuppgifter: namn, efternamn.</li>
            <li>Kontaktuppgifter: e-post, telefon, postadress.</li>
            <li>Faktureringsinformation.</li>
            <li>Betalningsuppgifter (hanteras via säkra betalportaler).</li>
            <li>Webbplatsanvändningsdata (cookies och liknande tekniker).</li>
        </ul>

        <h4>3. Ändamål med behandlingen</h4>
        <ul>
            <li>Hantera beställningar och onlineköp.</li>
            <li>Bearbeta betalningar och fakturering.</li>
            <li>Skicka beställningsrelaterade kommunikationer.</li>
            <li>Besvara frågor och förfrågningar.</li>
            <li>Skicka kommersiella meddelanden (om samtycke ges).</li>
            <li>Uppfylla lagstadgade skyldigheter.</li>
        </ul>

        <h4>4. Laglig grund</h4>
        <ul>
            <li>Genomförande av ett köpeavtal.</li>
            <li>Användarens samtycke.</li>
            <li>Uppfyllelse av lagstadgade skyldigheter.</li>
            <li>Berättigat intresse för den personuppgiftsansvarige.</li>
        </ul>

        <h4>5. Lagring av uppgifter</h4>
        <p>
            Uppgifterna kommer att bevaras så länge avtalsförhållandet finns och därefter under de perioder som krävs enligt spansk skattelagstiftning och handelslagstiftning.
        </p>

        <h4>6. Mottagare</h4>
        <p>Uppgifterna kan komma att lämnas ut till:</p>
        <ul>
            <li>Finansiella institutioner för betalningshantering.</li>
            <li>Transportföretag för leverans av beställningar.</li>
            <li>Teknikleverantörer som tillhandahåller tjänster som är nödvändiga för verksamheten.</li>
        </ul>

        <h4>7. Användarens rättigheter</h4>
        <p>Du har rätt att:</p>
        <ul>
            <li>Få tillgång till dina personuppgifter.</li>
            <li>Begära rättelse eller radering.</li>
            <li>Begränsa behandlingen.</li>
            <li>Invända mot behandlingen.</li>
            <li>Begära dataportabilitet.</li>
            <li>Återkalla samtycke när som helst.</li>
        </ul>

        <p>
            Du kan utöva dina rättigheter genom att skicka en begäran till ' . __('website.email_address') . ', med en kopia av ditt identitetsdokument.
        </p>

        <h4>8. Säkerhetsåtgärder</h4>
        <p>
            Vi tillämpar lämpliga tekniska och organisatoriska åtgärder för att säkerställa säkerheten för personuppgifter och förhindra förändring, förlust eller obehörig åtkomst.
        </p>

        <h4>9. Cookies</h4>
        <p>
            Denna webbplats använder egna och tredjeparts-cookies. Du hittar detaljerad information i vår Cookiepolicy.
        </p>

        <h4>10. Tillsynsmyndighet</h4>
        <p>
            Om du anser att dina rättigheter inte har respekterats kan du lämna in ett klagomål till Spanska dataskyddsmyndigheten (AEPD).
        </p>

        <hr>

        <p class="text-muted small">
            Senast uppdaterad: ' . now()->format('d/m/Y') . '
        </p>
    ',
];
