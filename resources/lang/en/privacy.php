<?php

return [
    'title' => 'Privacy Policy',
    'content' => '
        <h1 class="mb-4 text-center">Privacy Policy</h1>

        <p>In compliance with Regulation (EU) 2016/679 (GDPR), Organic Law 3/2018 (LOPDGDD), and Law 34/2002 (LSSI-CE), we inform you about the processing of your personal data on this website.</p>

        <hr>

        <h4>1. Data Controller</h4>
        <p>
            <strong>Business Name:</strong> ' . config('app.name') . '<br>
            <strong>Email:</strong> contacto@vaakgolvslip.se<br>
        </p>

        <h4>2. Data We Collect</h4>
        <ul>
            <li>Identification data: name, surname.</li>
            <li>Contact data: email, phone, postal address.</li>
            <li>Billing information.</li>
            <li>Payment data (managed through secure gateways).</li>
            <li>Browsing data (cookies and similar technologies).</li>
        </ul>

        <h4>3. Purpose of Processing</h4>
        <ul>
            <li>Manage orders and online purchases.</li>
            <li>Process payments and billing.</li>
            <li>Send order-related communications.</li>
            <li>Respond to inquiries and requests.</li>
            <li>Send commercial communications (if consent is given).</li>
            <li>Comply with legal obligations.</li>
        </ul>

        <h4>4. Legal Basis</h4>
        <ul>
            <li>Execution of a sales contract.</li>
            <li>User consent.</li>
            <li>Compliance with legal obligations.</li>
            <li>Legitimate interest of the controller.</li>
        </ul>

        <h4>5. Data Retention</h4>
        <p>
            Data will be retained as long as the contractual relationship exists and subsequently for the periods required by Spanish tax and commercial legislation.
        </p>

        <h4>6. Recipients</h4>
        <p>Data may be communicated to:</p>
        <ul>
            <li>Financial institutions for payment processing.</li>
            <li>Transport companies for order delivery.</li>
            <li>Technology providers that provide services necessary for the activity.</li>
        </ul>

        <h4>7. User Rights</h4>
        <p>You have the right to:</p>
        <ul>
            <li>Access your personal data.</li>
            <li>Request rectification or deletion.</li>
            <li>Request restriction of processing.</li>
            <li>Object to processing.</li>
            <li>Request data portability.</li>
            <li>Withdraw consent at any time.</li>
        </ul>

        <p>
            You may exercise your rights by sending a request to contacto@vaakgolvslip.se, attaching a copy of your identification document.
        </p>

        <h4>8. Security Measures</h4>
        <p>
            We apply appropriate technical and organizational measures to ensure the security of personal data and prevent its alteration, loss, or unauthorized access.
        </p>

        <h4>9. Cookies</h4>
        <p>
            This website uses its own and third-party cookies. You can find detailed information in our Cookie Policy.
        </p>

        <h4>10. Supervisory Authority</h4>
        <p>
            If you believe your rights have not been respected, you may file a complaint with the Spanish Data Protection Agency (AEPD).
        </p>

        <hr>

        <p class="text-muted small">
            Last updated: ' . now()->format('d/m/Y') . '
        </p>
    ',
];
