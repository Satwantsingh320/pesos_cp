<?php

return [
    'title' => 'Terms and Conditions of Sale',
    'content' => '
        <h1 class="mb-4 text-center">Terms and Conditions of Sale</h1>

        <p>These conditions govern the purchase of products offered through the website ' . config('app.name') . ', in compliance with current Spanish regulations.</p>

        <hr>

        <h4>1. Owner Identification</h4>
        <p>
            <strong>Business Name:</strong> ' . config('app.name') . '<br>
            <strong>Contact Email:</strong> ' . __('website.email_address') . '<br>
        </p>

        <h4>2. Purpose</h4>
        <p>
            This document regulates the contracting conditions for products through this online store.
            Placing an order implies full acceptance of these conditions.
        </p>

        <h4>3. Purchase Process</h4>
        <ul>
            <li>Product selection and add to cart.</li>
            <li>Enter billing and shipping information.</li>
            <li>Order confirmation and acceptance of conditions.</li>
            <li>Payment using available methods on the platform.</li>
            <li>Receipt of confirmation email.</li>
        </ul>

        <h4>4. Prices</h4>
        <p>
            All prices are expressed in Euros (' . config('app.currency_symbol', '€') . ') and include applicable taxes (VAT), unless otherwise indicated.
            Shipping costs will be shown before completing the purchase.
        </p>

        <h4>5. Payment Methods</h4>
        <p>
            Payment methods indicated in the purchase process are accepted.
            Payments are made through secure platforms.
        </p>

        <h4>6. Shipping</h4>
        <ul>
            <li>Orders are processed on business days.</li>
            <li>The estimated delivery time will be indicated before completing the purchase.</li>
            <li>We are not responsible for delays attributable to the transport company.</li>
        </ul>

        <h4>7. Right of Withdrawal</h4>
        <p>
            In accordance with Spanish legislation, the customer has a period of <strong>14 calendar days</strong> from receipt of the order to exercise their right of withdrawal without justification.
        </p>
        <p>
            To exercise this right, you must notify us by email at ' . __('website.email_address') . '.
            The product must be returned in perfect condition.
        </p>

        <h4>8. Returns and Refunds</h4>
        <p>
            The refund will be made using the same payment method used by the customer,
            within a maximum period of 14 days from receipt of the returned product.
        </p>

        <h4>9. Warranties</h4>
        <p>
            All products have the legal warranty of conformity in accordance with current regulations.
        </p>

        <h4>10. Data Protection</h4>
        <p>
            Personal data will be processed in accordance with our Privacy Policy and the General Data Protection Regulation (GDPR).
        </p>

        <h4>11. Intellectual Property</h4>
        <p>
            All contents of the website (texts, images, logos, design) are owned by the owner
            and are protected by intellectual property regulations.
        </p>

        <h4>12. Applicable Law and Jurisdiction</h4>
        <p>
            These conditions are governed by Spanish law.
            In case of conflict, the parties will submit to the courts and tribunals of the consumer\'s domicile.
        </p>

        <hr>

        <p class="text-muted small">
            Last updated: ' . now()->format('d/m/Y') . '
        </p>
    ',
];
